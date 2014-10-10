<?php

namespace uptize\Common;

final class Config {

	public static function loadFile($fileName) {
		if (!is_file($fileName))
			throw new \Exception('add an exception here');
		$string = file_get_contents($fileName);
		return self::loadString($string);
	}

	public static function loadString($string) {
		if (empty($string))
			throw new \Exception('add an exception here');
		$json = json_decode($string, true);
		if (is_null($json))
			throw new Exception('add an exception here');
		return $json;
	}

}