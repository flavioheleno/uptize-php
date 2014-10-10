<?php

namespace uptize;

use uptize\Common\Config;
use uptize\Common\Factory;
use uptize\Report\Report;
use uptize\Service;

final class Agent {

	public static function run(array $settings) {
		$report = new Report;
		foreach ($settings as $check) {
			$service = Factory::create(
				$check['service'],
				(empty($check['settings']) ? array() : $check['settings'])
			);
			if (!empty($check['name']))
				$service->setFriendlyName($check['name']);
			$report->add($service->getFriendlyName(), $service->check());
		}
		return $report;
	}

}