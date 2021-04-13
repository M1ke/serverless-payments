<?php
declare(strict_types=1);

use App\Domain\Payment\PaymentRepository;
use App\Infrastructure\Persistence\Payment\DynamoPaymentRepository;
use DI\ContainerBuilder;
use function DI\autowire;

return static function (ContainerBuilder $containerBuilder) {
    $containerBuilder->addDefinitions([
        PaymentRepository::class => autowire(DynamoPaymentRepository::class),
    ]);
};
