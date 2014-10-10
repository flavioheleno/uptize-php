#!/usr/bin/env php
<?php

date_default_timezone_set('UTC');

require_once __DIR__ . '/../vendor/autoload.php';

$cmd = new Commando\Command;

$cmd->setHelp('uptize-php cli v0.1.1')
	->flag('c')
		->aka('config')
		->describedAs('Configuration file')

	->flag('d')
		->aka('dryRun')
		->describedAs('Dry run (do not send check data to uptize.me)')
		->boolean()

	->flag('k')
		->aka('key')
		->describedAs('Machine Key to send check data to uptize.me')

	->flag('o')
		->aka('output')
		->describedAs('Output check data to a file')

	->flag('v')
		->aka('verbose')
		->describedAs('Verbose output')
		->boolean()

	->flag('l')
		->aka('log')
		->describedAs('Log output')
		->default(__DIR__ . '/uptize.log');

try {

	if (empty($cmd['config']))
		$config = __DIR__ . '/uptize.json';
	else
		$config = $cmd['config'];

	if (!is_file($config))
		throw new Exception("Config file '{$config}' not found.");

	$settings = uptize\Common\Config::loadFile($config);
	if (empty($settings['checks']))
		throw new Exception('Empty check list on config file.');
	$report = uptize\Agent::run($settings['checks']);

	if ($cmd['dryRun']) {
		if (empty($cmd['output']))
			uptize\Report\Printer::console($report);
		else
			uptize\Report\Printer::file($cmd['output'], $report);
	} else {
		if ($cmd['verbose'])
			uptize\Report\Printer::console($report);
		if ((empty($cmd['key'])) && (empty($settings['key'])))
			throw new Exception('You have to set the Machine Key with -k (--key) or on the config file.');
		$announcer = new uptize\Announcer(array(
			'key' => $settings['key'],
		));
		if (!$announcer->announce($report))
			throw new Exception($announcer->error());
	}
} catch (Exception $exception) {
	$log = new Monolog\Logger('uptize');
	$log->pushHandler(new Monolog\Handler\StreamHandler($cmd['log'], Monolog\Logger::WARNING));
	if ($cmd['verbose'])
		$log->pushHandler(new uptize\Common\Verbose, Monolog\Logger::DEBUG);
	$log->error($exception->getMessage(), array('file' => $exception->getFile(), 'line' => $exception->getLine()));
}