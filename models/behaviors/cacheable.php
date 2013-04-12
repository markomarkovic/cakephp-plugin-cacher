<?php
/**
 * CacheableBehavior
 *
 * Configures the model's cache and takes care of the cache versions using callbacks
 */
class CacheableBehavior extends ModelBehavior {

/**
 * Used for keeping the configuration of models
 *
 * @var array
 */
	var $_settings = array();

/**
 * setup Callback
 */
	function setup(&$Model, $settings) {
		// Setting the default settings for a Model
		if (!isset($this->_settings[$Model->alias])) {
			$this->_settings[$Model->alias] = array_merge(
				array(
					'duration' => 3600, // One hour
					'path' => CACHE . 'cacher' . DS, // Save the cacher caches in the 'cacher' cache subdirectory (relevant if the engine is 'File')
					'prefix' => 'cacher_'
				),
				$settings
			);
			if (isset($this->_settings[$Model->alias]['engine']) && $this->_settings[$Model->alias]['engine'] == 'File' && !file_exists($this->_settings[$Model->alias]['path'])) {
				$folder = new Folder;
				$folder->create($this->_settings[$Model->alias]['path'], 0777);
			}
		}

		// Creating the cache config for this model
		Cache::config('Cacher'.'_'.$Model->alias, array_merge(
			Cache::settings(),
			$Model->Behaviors->Cacheable->_settings[$Model->alias]
		));
	}

/**
 * afterSave Callback
 *
 * Updating the counter in order to invalidate the cache on next cache request.
 */
	function afterSave(&$Model, $created) {
		$this->_updateCounter($Model->alias);
		parent::afterSave($Model, $created);
	}

/**
 * afterDelete Callback
 *
 * Updating the counter in order to invalidate the cache on next cache request.
 */
	function afterDelete(&$Model) {
		$this->_updateCounter($Model->alias);
		parent::afterDelete();
	}

/**
 * Updating the counter (version of the model) in the cache
 */
	function _updateCounter($alias = 'AppModel') {
		if (Configure::read('Cache.disable') !== true) {
			Cache::write($alias, 1 + (int)Cache::read($alias));
		}
	}

}

