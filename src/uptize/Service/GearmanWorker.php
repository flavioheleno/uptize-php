<?php

namespace uptize\Service;

use uptize\Report\Result;

final class GearmanWorker extends AbstractService {

	private $functionName;
	private $host = '127.0.0.1';
	private $port = 4730;
	private $timeout = 100;

	public function __construct(array $settings) {
		if (!empty($settings['functionName']))
			$this->functionName = $settings['functionName'];
		if (!empty($settings['host']))
			$this->host = $settings['host'];
		if (!empty($settings['port']))
			$this->port = $settings['port'];
		if (!empty($settings['timeout']))
			$this->timeout = $settings['timeout'];
	}

	public function check() {
		if (class_exists('\GearmanClient')) {
			$client = new \GearmanClient;
			$client->setTimeout($this->timeout);
			$client->addServer($this->host, $this->port);
			$mtime = microtime(true);
			$result = $client->doNormal($this->functionName, json_encode(array('monitor' => 'uptize')));
			if ($client->returnCode() == \GEARMAN_SUCCESS) {
				$mtime = (microtime(true) - $mtime);
				return new Result(true, array(
					'time' => $mtime
				));
			}
			return new Result(false, array(), $client->error());
		}
		return new Result(false, array(), 'Class GearmanClient not found');
	}

}