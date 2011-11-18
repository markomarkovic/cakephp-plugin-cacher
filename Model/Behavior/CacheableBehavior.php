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
	protected $_settings = array();

/**
 * setup Callback
 */
	public function setup(&$Model, $settings) {
		// Setting the default settings for a Model
		if (!isset($this->_settings[$Model->alias])) {
			$this->_settings[$Model->alias] = array_merge(
				Cache::settings(),
				array(
					'duration' => 3600, // One hour
					'path' => CACHE . 'cacher' . DS, // Save the cacher caches in the 'cacher' cache subdirectory (relevant if the engine is 'File')
					'prefix' => 'cacher_'
				),
				$settings
			);
			if ($this->_settings[$Model->alias]['engine'] == 'File' && !file_exists($this->_settings[$Model->alias]['path'])) {
				App::uses('Folder', 'Utility');
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
 * Clears the cache for a Model
 *
 * @param string $alias alias of the model to clear
 */
	public function clear($alias) {
		return $this->_updateCounter($alias);
	}

/**
 * afterSave Callback
 *
 * Updating the counter in order to invalidate the cache on next cache request.
 */
	public function afterSave(&$Model, $created) {
		$this->_updateCounter($Model->alias);
		parent::afterSave($Model, $created);
	}

/**
 * afterDelete Callback
 *
 * Updating the counter in order to invalidate the cache on next cache request.
 */
	public function afterDelete(&$Model) {
		$this->_updateCounter($Model->alias);
		parent::afterDelete($Model);
	}

/**
 * Updating the counter (version of the model) in the cache
 */
	protected function _updateCounter($alias = 'AppModel') {
		if (Configure::read('Cache.disable') !== true) {
			$cacheConfig = "Cacher_{$alias}";
			return Cache::write($alias, 1 + (int)Cache::read($alias, $cacheConfig), $cacheConfig);
		}
	}

}

