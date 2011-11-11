<?php
App::uses('Model', 'Model');
/**
 * A standard CakePHP AppModel with the find method overriden.
 * The code from it should be put in app/Model/AppModel.php
 */
class AppModel extends Model {

/**
 * Instance of the Cacher object, so we don't have to create it on every call
 *
 * @var object Cacher instance
 */
	protected $_Cacher = null;

/**
 * Overrides Model::find to transparently load data from cache
 */
	public function find($type, $params, $useCache = true) {
		if ($useCache && $this->Behaviors->enabled('Cacheable')){
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
