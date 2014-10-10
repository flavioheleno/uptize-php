<?php

namespace uptize\Service;

use uptize\Report\Result;

final class Nginx extends AbstractService {

	private $processName = 'nginx';
	private $method = 'GET';
	private $url = 'http://127.0.0.1/status';

	private function isRunning() {
		$process = new Process(array(
			'processName' => $this->processName
		));
		return $process->check();
	}

	private function isConnectable() {
		$http = new HTTP(array(
			'method' => $this->method,
			'url' => $this->url
		));
		return $http->check();
	}

	public function __construct(array $settings) {
		if (!empty($settings['processName']))
			$this->processName = $settings['processName'];
		if (!empty($settings['method']))
			$this->method = $settings['method'];
		if (!empty($settings['url']))
			$this->url = $settings['url'];
	}

	public function check() {
		$process = $this->isRunning();
		if (!$process->status())
			return new Result(false, array(), $process->error());
		$http = $this->isConnectable();
		if (!$http->status())
			return new Result(false, $process->details(), $http->error());
		return new Result(true, array_merge($process->details(), $http->details()));
	}

}
