<?php

namespace uptize\Service;

use uptize\Report\Result;

final class Process extends AbstractService {

	private $processName = '';

	public function __construct(array $settings) {
		if (!empty($settings['processName']))
			$this->processName = $settings['processName'];
	}

	public function check() {
		if (empty($this->processName))
			return new Result(false, array(), 'Empty process name');
		$pids = exec('pidof -x ' . escapeshellarg($this->processName));
		if (empty($pids))
			return new Result(false, array(), 'Process is not running');
		return new Result(true, array(
			'pids' => $pids
		));
	}

}