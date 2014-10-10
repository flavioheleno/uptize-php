<?php

namespace uptize\Common;

use Monolog\Logger;
use Monolog\Handler\AbstractProcessingHandler;

class Verbose extends AbstractProcessingHandler {

	protected function write(array $record) {
		echo $record['level_name'], ': ', $record['message'], PHP_EOL;
	}

}