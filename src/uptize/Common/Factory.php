<?php

namespace uptize\Common;

final class Factory {

	public static function create($serviceName, array $serviceSettings) {
		$map = array(
			'beanstalk' => 'Beanstalkd',
			'gearmand' => 'Gearman',
			'gearmanworker' => 'GearmanWorker',
			'http' => 'HTTP',
			'httpcontent' => 'HTTPContent',
			'content' => 'HTTPContent',
			'memcached' => 'Memcache',
			'mysql' => 'MySQL',
			'php-fpm' => 'PHP',
			'php' => 'PHP',
			'postgresql' => 'PostgreSQL',
			'redis-server' => 'Redis'
		);
		$serviceName = strtolower($serviceName);
		if (isset($map[$serviceName]))
			$serviceName = $map[$serviceName];
		else
			$serviceName = ucfirst($serviceName);
		$class = "\\uptize\\Service\\{$serviceName}";
		if (class_exists($class))
			return new $class($serviceSettings);
		throw new \Exception("Check not found: {$serviceName}");
	}

}