<?php
declare(strict_types=1);

use App\Application\Settings\Env;
use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder){
	// Global Settings Object
	$containerBuilder->addDefinitions([
		SettingsInterface::class => function (){
			return new Settings([
				SettingsInterface::displayErrorDetails => Env::isProd(),
				SettingsInterface::logError => true,
				SettingsInterface::logErrorDetails => true,
				SettingsInterface::logger => [
					'name' => 'slim-app',
					// @mod we need to log to stderr to appear in Lambda logs
					'path' => 'php://stderr',
					'level' => Logger::DEBUG,
				],
			]);
		},
	]);
};
