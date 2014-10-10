<?php

namespace uptize\Report;

final class Report implements \Iterator {

	private $position = 0;
	private $friendlyName = array();
	private $checkResult = array();

	public function rewind() {
		$this->position = 0;
	}

	public function current() {
		return $this->checkResult[$this->position];
	}

	public function key() {
		return $this->friendlyName[$this->position];
	}

	public function next() {
		++$this->position;
	}

	public function valid() {
		return isset($this->checkResult[$this->position]);
	}

	public function add($friendlyName, Result $checkResult) {
		$this->friendlyName[] = $friendlyName;
		$this->checkResult[] = $checkResult;
	}

}