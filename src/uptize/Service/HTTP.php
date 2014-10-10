<?php

namespace uptize\Service;

use uptize\Report\Result;

use GuzzleHttp\Client;

final class HTTP extends AbstractService {

	private $method = 'GET';
	private $url = 'http://localhost/';
	private $headers = array();
	private $options = array();

	public function __construct(array $settings) {
		if (!empty($settings['method']))
			$this->method = $settings['method'];
		if (!empty($settings['url']))
			$this->url = $settings['url'];
		if (!empty($settings['headers']))
			$this->headers = $settings['headers'];
		if (!empty($settings['options']))
			$this->options = $settings['options'];
	}

	public function check() {
		try {
			$client = new Client;
			$mtime = microtime(true);
			switch (strtoupper($this->method)) {
				case 'GET':
					$request = $client->createRequest('GET', $this->url, $this->options);
					break;
				case 'POST':
					$request = $client->createRequest('POST', $this->url, $this->options);
					break;
				case 'PUT':
					$request = $client->createRequest('PUT', $this->url, $this->options);
					break;
				case 'DELETE':
					$request = $client->createRequest('DELETE', $this->url, $this->options);
					break;
				case 'HEAD':
					$request = $client->createRequest('HEAD', $this->url, $this->options);
					break;
				case 'OPTIONS':
					$request = $client->createRequest('OPTIONS', $this->url, $this->options);
					break;
			}
			$request->addHeaders($this->headers);
			$response = $client->send($request);
			$mtime = (microtime(true) - $mtime);
			return new Result(true, array(
				'time' => $mtime
			));
		} catch (\Exception $exception) {
			return new Result(false, array(), $exception->getMessage());
		}
	}

}