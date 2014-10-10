<?php

namespace uptize\Common;

final class Format {

	public static function msec($time) {
		$sec = intval($time);
		$micro = $time - $sec;
		return strftime('%T', mktime(0, 0, $sec)) . str_replace('0.', '.', sprintf('%.3f', $micro)) . " ({$time})";
	}

	public static function size($bytes) {
		$units = array('B', 'KB', 'MB', 'GB', 'TB');
		$bytes = max($bytes, 0);
		$pow = floor(($bytes ? log($bytes) : 0) / log(1024));
		$pow = min($pow, count($units) - 1);
		$bytes /= (1 << (10 * $pow));
		return round($bytes, 0) . ' ' . $units[$pow];
	}

}