<?php
/**
 * A standard CakePHP AppModel with the find method overriden.
 * The code from it should be put in app/app_model.php
 */
class AppModel extends Model {

/**
 * Instance of the Cacher object, so we don't have to create it on every call
 *
 * @var object Cacher instance
 */
	var $_Cacher = null;

/**
 * Overriding find in order for it to transparently load data from cache.
 */
	function find($type, $params, $useCache = true) {
		if ($useCache && in_array('Cacheable', $this->Behaviors->_attached)){
			if (!isset($this->_Cacher)) {
				App::import('Model', 'Cacher.Cacher');
				$this->_Cacher = new Cacher;
			}
			return $this->_Cacher->find($this, $type, $params);
		} else {
			return parent::find($type, $params);
		}
	}

}

