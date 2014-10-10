<?php

namespace uptize\Report;

final class Result {

	private $status;
	private $details;
	private $error;

	public function __construct($status, array $details = array(), $error = null) {
		$this->status = $status;
		$this->details = $details;
		$this->error = $error;
	}

	public function status() {
		return $this->status;
	}

	public function details() {
		return $this->details;
	}

	public function detail($name, $default = null) {
		if (isset($this->details[$name]))
			return $this->details[$name];
		return $default;
	}

	public function error() {
		return $this->error;
	}

}