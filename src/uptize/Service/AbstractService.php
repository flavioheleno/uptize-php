<?php

namespace uptize\Service;

abstract class AbstractService implements ServiceInterface {

	protected $friendlyName;

	public function setFriendlyName($value) {
		$this->friendlyName = $value;
	}

	public function getFriendlyName() {
		if (empty($this->friendlyName))
			return ltrim(get_class($this), __NAMESPACE__ . '\\');
		return $this->friendlyName;
	}

	abstract public function check();

}