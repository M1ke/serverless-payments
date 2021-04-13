<?php
declare(strict_types=1);

use App\Application\Settings\Env;
use App\Application\Settings\SettingsInterface;
use Aws\DynamoDb\DynamoDbClient;
use Aws\Sdk;
use DI\ContainerBuilder;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Monolog\Processor\UidProcessor;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

return static function (ContainerBuilder $containerBuilder){
	$containerBuilder->addDefinitions([
		LoggerInterface::class => function (ContainerInterface $container) :Logger{
			$settings = $container->get(SettingsInterface::class);

			$loggerSettings = $settings->get(SettingsInterface::logger);
			$logger = new Logger($loggerSettings['name']);

			$processor = new UidProcessor();
			$logger->pushProcessor($processor);

			$handler = new StreamHandler($loggerSettings['path'], $loggerSettings['level']);
			$logger->pushHandler($handler);

			return $logger;
		},
		DynamoDbClient::class => function(){
			$args = [
				'region' => Env::getAwsRegion(),
				'version' => 'latest',
			];
			$endpoint = Env::getDynamoEndpoint();
			if ($endpoint){
				$args['endpoint'] = $endpoint;
				$args['credentials'] = [
					'key' => 'abc',
					'secret' => 'abc',
				];
			}

			$sdk = new Sdk($args);

			return $sdk->createDynamoDb();
		}
	]);
};
