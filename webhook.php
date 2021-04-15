<?php
declare(strict_types=1);

use App\Application\Settings\Env;
use App\Domain\Payment\PaymentRepository;
use Bref\Context\Context;
use DI\ContainerBuilder;
use Monolog\Formatter\JsonFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Stripe\PaymentIntent;
use Stripe\Stripe;

require __DIR__.'/vendor/autoload.php';

function _getLogger(): LoggerInterface{
	$logger = new Logger('webhooks');
	$json_formatter = new JsonFormatter();
	$handler = new StreamHandler('php://stderr', Env::isProd() ? LogLevel::NOTICE : LogLevel::DEBUG);
	$handler->setFormatter($json_formatter);
	$logger->pushHandler($handler);

	return $logger;
}

// Instantiate PHP-DI ContainerBuilder
$containerBuilder = new ContainerBuilder();

// Set up settings
$settings = require __DIR__.'/app/settings.php';
$settings($containerBuilder);

// Set up dependencies
$dependencies = require __DIR__.'/app/dependencies.php';
$dependencies($containerBuilder);

// Set up repositories
$repositories = require __DIR__.'/app/repositories.php';
$repositories($containerBuilder);

// Build PHP-DI Container instance
$container = $containerBuilder->build();

$logger = _getLogger();

return static function ($event, Context $context) use ($container, $logger){
	$record = reset($event['Records']);
	$data = json_decode($record['body'], true, JSON_THROW_ON_ERROR);

	$logger->debug('Received webhook from queue', [
		'_webhook' => $data,
		'_lambda' => $context->jsonSerialize(),
	]);

	Stripe::setApiKey(Env::getStripeKey());

	$payment_intent_id = $data['data']['object']['payment_intent'];

	$payment_intent = PaymentIntent::retrieve($payment_intent_id);

	$payment_id = $payment_intent->metadata['id'];

	$payments = $container->get(PaymentRepository::class);

	$payment = $payments->findPayment($payment_id);

	$payment->incrementPaid();

	$payments->putPayment($payment);

	$logger->notice("Updated payment", [
		'id' => $payment_id,
		'payment_intent' => $payment_intent_id,
	]);
};
