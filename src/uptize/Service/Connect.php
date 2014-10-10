<?php

namespace uptize\Service;

use uptize\Report\Result;

final class Connect extends AbstractService {

	private $host = '127.0.0.1';
	private $port = 80;
	private $timeout = 30;

	public function __construct(array $settings) {
		if (!empty($settings['host']))
			$this->host = $settings['host'];
		if (substr_compare($this->host, 'unix://', 0, 7) == 0)
			$this->port = -1;
		else {
			if (!empty($settings['port']))
				$this->port = $settings['port'];
		}
		if (!empty($settings['timeout']))
			$this->timeout = $settings['timeout'];
	}

	public function check() {
		$mtime = microtime(true);
		$sock = fsockopen($this->host, $this->port, $errno, $errstr, $this->timeout);
		if ($sock === false)
			return new Result(false, array(), $errstr);
		$mtime = (microtime(true) - $mtime);
		fclose($sock);
		return new Result(true, array(
			'time' => $mtime
		));
	}

}