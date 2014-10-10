<?php

namespace uptize\Service;

use uptize\Report\Result;

final class Health extends AbstractService {

	private $disk = '/';

	public function __construct(array $settings) {
		if (!empty($settings['disk']))
			$this->disk = $settings['disk'];
	}

	public function check() {
		$load = sys_getloadavg();
		return new Result(true, array(
			'load1' => $load[0],
			'load5' => $load[1],
			'load15' => $load[2],
			'diskTotal' => disk_total_space($this->disk),
			'diskFree' => disk_free_space($this->disk)
		));
	}

}