<?php

namespace App\Processor;

use App\Controller\ActionController;
use App\Dto\PaymentDto;
use Psr\Container\ContainerInterface;
use Symfony\Contracts\Service\ServiceSubscriberInterface;

class ProcessorLocator implements ServiceSubscriberInterface
{
    public function __construct(
        private ContainerInterface $container,
    ) {
    }

    public static function getSubscribedServices(): array
    {
        return [
            'shift4' => Shift4Processor::class,
            'aci' => AciProcessor::class,
        ];
    }

    public function handle(string $code): ProcessorInterface
    {
        if ($this->container->has($code)) {
            return $this->container->get($code);
        }
        throw new \Exception(sprintf('No Implemented payment processor %s', $code));
    }
}