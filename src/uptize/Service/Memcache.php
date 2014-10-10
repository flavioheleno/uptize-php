<?php

namespace uptize\Service;

use uptize\Report\Result;

final class Memcache extends AbstractService {

	private $processName = 'memcached';
	private $host = '127.0.0.1';
	private $port = 11211;

	private function isRunning() {
		$process = new Process(array(
			'processName' => $this->processName
		));
		return $process->check();
	}

	private function isConnectable() {
		$connect = new Connect(array(
			'host' => $this->host,
			'port' => $this->port
		));
		return $connect->check();
	}

	public function __construct(array $settings) {
		if (!empty($settings['processName']))
			$this->processName = $settings['processName'];
		if (!empty($settings['host']))
			$this->host = $settings['host'];
		if (!empty($settings['port']))
			$this->port = $settings['port'];
	}

	public function check() {
		$process = $this->isRunning();
		if (!$process->status())
			return new Result(false, array(), $process->error());
		$connect = $this->isConnectable();
		if (!$connect->status())
			return new Result(false, $process->details(), $connect->error());
		return new Result(true, array_merge($process->details(), $connect->details()));
	}

}