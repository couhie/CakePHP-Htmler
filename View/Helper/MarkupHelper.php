<?php
/**
 * MarkupHelper.php
 * @author kohei hieda
 *
 */
class MarkupHelper extends AppHelper {

	function __construct(View $View, $settings = array()) {
		parent::__construct($View, $settings);
		$this->_View = $View;
	}

	/**
	 * getData
	 * @param $data
	 * @param $key1
	 * @param $key2
	 * @param $needle
	 * @return unknown
	 */
	function getData($data, $key1, $key2) {
		if (!isset($data[$key1])) {
			return '';
		}
		if (strpos($key2, '.') === false) {
			return isset($data[$key1][$key2]) ? $data[$key1][$key2] : '';
		} else {
			list($newKey1, $newKey2) = explode('.', $key2, 2);
			return $this->getData($data[$key1], $newKey1, $newKey2);
		}
	}

	/**
	 * checked
	 * @param $key
	 * @param $needle
	 * @param $default
	 * @return string
	 */
	function checked($key, $needle, $default = null) {
		list($model, $key) = $this->keySplit($key);
		$ret = '';
		$data = $this->getData($this->request->data, $model, $key);
		if (isset($data)) {
			if (is_array($data)) {
				if (in_array($needle, $data)) {
					$ret = ' checked="ckecked" ';
				}
			} else {
				if ($needle == $data) {
					$ret = ' checked="ckecked" ';
				}
			}
		} else if ($needle === $default) {
			$ret = ' checked="ckecked" ';
		}
		return $ret;
	}

	/**
	 * selected
	 * @param $key
	 * @param $needle
	 * @return string
	 */
	function selected($key, $needle) {
		list($model, $key) = $this->keySplit($key);
		$ret = '';
		$data = $this->getData($this->request->data, $model, $key);
		if (isset($data)) {
			if ($needle == $data) {
				$ret = ' selected="selected" ';
			}
		}
		return $ret;
	}

	/**
	 * value
	 * @param $key
	 * @return string
	 */
	function value($key) {
		list($model, $key) = $this->keySplit($key);
		$data = $this->getData($this->request->data, $model, $key);
		return $data;
	}

	/**
	 * errorExists
	 * @param $key
	 * @return boolean
	 */
	function errorExists($key) {
		list($model, $key) = $this->keySplit($key);
		$data = $this->getData($this->_View->validationErrors, $model, $key);
		return isset($data) && $data != '' ? true : false;
	}

	/**
	 * errorMessage
	 * @param $key
	 * @return string
	 */
	function errorMessage($key) {
		list($model, $key) = $this->keySplit($key);
		$data = $this->getData($this->_View->validationErrors, $model, $key);
		return is_array($data) && isset($data[0]) ? $data[0] : (isset($data) ? $data : '');
	}

	/**
	 * flashExists
	 * @param $key
	 * @return boolean
	 */
	function flashExists($key = 'flash') {
		return CakeSession::check('Message.'.$key);
	}

	/**
	 * flashMessage
	 * @param $key
	 * @return string
	 */
	function flashMessage($key = 'flash') {
		$flash = CakeSession::read('Message.' . $key);
		CakeSession::delete('Message.'.$key);
		return isset($flash['message']) ? nl2br(h($flash['message'])) : '';
	}

	/**
	 * keySplit
	 * @param $key
	 * @return array
	 */
	function keySplit($key) {
		if (strpos($key, '.') !== false) {
			return explode('.', $key, 2);
		}
		return array(Inflector::camelize($this->request->params['controller']), $key);
	}

}