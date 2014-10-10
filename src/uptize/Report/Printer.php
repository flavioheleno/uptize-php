<?php

namespace uptize\Report;

use uptize\Common\Format;

final class Printer {

	private static function format(Report $report) {
		$return = array();
		foreach ($report as $name => $result) {
			$return[] = '[' . ($result->status() ? 'pass' : 'fail') . "] {$name}";
			foreach ($result->details() as $property => $value) {
				switch ($property) {
					case 'time':
						$value = Format::msec($value);
						break;
					case 'diskTotal':
					case 'diskFree':
						$value = Format::size($value);
						break;
				}
				$return[] = "\t{$property}: {$value}";
			}
		}
		return $return;
	}

	public static function console(Report $report) {
		echo 'uptize report' . PHP_EOL;
		echo '=============' . PHP_EOL;
		echo PHP_EOL;
		foreach (self::format($report) as $line)
			echo $line . PHP_EOL;
		echo PHP_EOL;
	}

	public static function file($fileName, Report $report) {
		$output = self::format($report);
		file_put_contents($fileName, $output, LOCK_EX);
	}

}