<?php
declare(strict_types=1);

use App\Application\Settings\Settings;
use App\Application\Settings\SettingsInterface;
use DI\ContainerBuilder;
use Monolog\Logger;

return static function (ContainerBuilder $containerBuilder){
	// Global Settings Object
	$containerBuilder->addDefinitions([
		SettingsInterface::class => function (){
			return new Settings([
				SettingsInterface::displayErrorDetails => true, // Should be set to false in production
				SettingsInterface::logError => false,
				SettingsInterface::logErrorDetails => false,
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
