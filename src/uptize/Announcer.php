<?php

namespace uptize;

use uptize\Report\Report;

use GuzzleHttp\Client;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Exception;

final class Announcer {

	private $key = '';
	private $host = 'http://api.uptize.me';
	private $port = 80;
	private $version = '1.0';
	private $error = null;

	public function __construct($settings) {
		if (!empty($settings['key']))
			$this->key = $settings['key'];
		if (!empty($settings['host']))
			$this->host = $settings['host'];
		if (!empty($settings['port']))
			$this->port = $settings['port'];
		if (!empty($settings['version']))
			$this->version = $settings['version'];
	}

	public function announce(Report $report) {
		try {
			$data = array();
			foreach ($report as $name => $result) {
				$data[$name] = array(
					'status' => $result->status()
				);
				foreach ($result->details() as $property => $value)
					$data[$name][$property] = $value;
				if (!$result->status())
					$data[$name]['error'] = $result->error();
			}
			$client = new Client;
			$request = $client->createRequest('POST', "{$this->host}:{$this->port}/{$this->version}/metric");
			$request->setHeader('Content-Type', 'application/json');
			$request->setBody(Stream::factory(json_encode(array(
				'data' => $data,
				'key' => $this->key
			))));
			$response = $client->send($request);
			if ($response->getStatusCode() == 201)
				return true;
			if ($response->getBody()) {
				$json = $response->json();
				$this->error = $json['error']['message'];
			}
		} catch (Exception\RequestException $exception) {
			if ($exception->hasResponse()) {
				$response = $exception->getResponse();
				if ($response->getBody()) {
					$json = $response->json();
					if (empty($json['error']['message']))
						$this->error = $exception->getMessage();
					else
						$this->error = $json['error']['message'];
				} else
					$this->error = $exception->getMessage();
			} else
				$this->error = $exception->getMessage();
		} catch (\Exception $exception) {
			$this->error = $exception->getMessage();
		}
		return false;
	}

	public function error() {
		return $this->error;
	}

}