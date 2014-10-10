<?php

namespace uptize\Service;

interface ServiceInterface {

	public function __construct(array $settings);

	public function check();

}
