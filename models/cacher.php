<?php
/**
 * Cacher 'model'
 *
 * Performs the actual cache reads, validation and updates, called from AppModel
 */
class Cacher { // Not using extends AppModel since we're going to call only $Model's and static Cache methods

	function find(&$Model, $type, $params) {
		if (Configure::read('Cache.disable') !== true) {

			Cache::config('Cacher'.'_'.$Model->alias); // Using the cache config of this model

			$tag = isset($Model->alias) ? '_' . $Model->alias : 'AppModel';
			$paramsHash = md5(json_encode($params));
			$version = (int)Cache::read($tag);
			$fullTag = $tag . '_' . $type . '_' . $paramsHash;
			if ($result = Cache::read($fullTag)) {
				if ($result['version'] == $version) { // Comparing the global model's version with the one from the request's cache
					return $result['data']; // Since version is the same, returning the data from cache
				}
			}

			$result = array('version' => $version, 'data' => $Model->find($type, $params, false)); // Loading the data from AppModel without using the cache
			Cache::write($fullTag, $result); // Overwriting the cache
			Cache::write($tag, $version); // Updating model version
			return $result['data'];
		} else {
			return $Model->find($type, $params, false); // Cache is turned off, not using it in the AppModel
		}
	}

}
