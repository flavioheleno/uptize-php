<?php

namespace uptize\Service;

use uptize\Report\Result;

final class Ping extends AbstractService {

	//code taken from http://www.php.net/manual/en/function.socket-create.php#80775

	private $error = 'No Error';
	private $host = 'localhost';
	private $timeout = 30;

	private function ping($host, $timeout = 1000) {
		$port = 0;
		$this->error = 'No Error';
		$ident = array(ord('J'), ord('C'));
		$seq   = array(rand(0, 255), rand(0, 255));

		$packet = '';
		$packet .= chr(8); // type = 8 : request
		$packet .= chr(0); // code = 0

		$packet .= chr(0); // checksum init
		$packet .= chr(0); // checksum init

		$packet .= chr($ident[0]); // identifier
		$packet .= chr($ident[1]); // identifier

		$packet .= chr($seq[0]); // seq
		$packet .= chr($seq[1]); // seq

		for ($i = 0; $i < 64; $i++)
			$packet .= chr(0);

		$chk = $this->icmpChecksum($packet);

		$packet[2] = $chk[0]; // checksum init
		$packet[3] = $chk[1]; // checksum init

		$sock = socket_create(AF_INET, SOCK_RAW,  getprotobyname('icmp'));
		$mtime = microtime(true);
		socket_sendto($sock, $packet, strlen($packet), 0, $host, $port);

		$read = array($sock);
		$write = NULL;
		$except = NULL;

		$select = socket_select($read, $write, $except, 0, $timeout * 1000);
		if (is_null($select)) {
			$this->error = 'Select Error';
			socket_close($sock);
			return -1;
		} elseif ($select === 0) {
			$this->error = 'Timeout';
			socket_close($sock);
			return -1;
		}

		$recv = '';
		$mtime = (microtime(true) - $mtime);
		socket_recvfrom($sock, $recv, 65535, 0, $host, $port);
		socket_close($sock);
		$recv = unpack('C*', $recv);

		// ICMP proto = 1
		if ($recv[10] !== 1) {
			$this->error = 'Not ICMP packet';
			return -1;
		}

		// ICMP response = 0
		if ($recv[21] !== 0) {
			$this->error = 'Not ICMP response';
			return -1;
		}

		if (($ident[0] !== $recv[25]) || ($ident[1] !== $recv[26])) {
			$this->error = 'Bad identification number';
			return -1;
		}

		if (($seq[0] !== $recv[27]) || ($seq[1] !== $recv[28])) {
			$this->error = 'Bad sequence number';
			return -1;
		}

		if ($mtime < 0) {
			$this->error = 'Response too long';
			$mtime = -1;
		}

		return $mtime;
	}

	private function icmpChecksum($data) {
		$bit = unpack('n*', $data);
		$sum = array_sum($bit);

		if (strlen($data) % 2) {
			$temp = unpack('C*', $data[strlen($data) - 1]);
			$sum += $temp[1];
		}

		$sum = ($sum >> 16) + ($sum & 0xffff);
		$sum += ($sum >> 16);

		return pack('n*', ~$sum);
	}

	public function __construct(array $settings) {
		if (!empty($settings['host']))
			$this->host = $settings['host'];
		if (!empty($settings['timeout']))
			$this->timeout = $settings['timeout'];
	}

	public function check() {
		$ping = $this->ping($this->host, $this->timeout);
		if ($ping == -1)
			return new Result(false, array(), $this->error);
		return new Result(true, array(
			'time' => $ping
		));
	}

}