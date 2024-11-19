<?php

namespace App\ErrorHandling;

use App\EventSubscriber\ExceptionHandling\ExceptionHandler;
use App\Exception\ProcessorException;
use App\Exception\ValidationException;
use App\Processor\ProcessorInterface;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class ExceptionSubscriber implements EventSubscriberInterface
{

    public static function getSubscribedEvents(): array
    {
        return [
            // the priority must be greater than the Security HTTP
            // ExceptionListener, to make sure it's called before
            // the default exception listener
            KernelEvents::EXCEPTION => ['onKernelException', 100],
        ];
    }

    public function onKernelException(ExceptionEvent $event): void
    {
        $throwable = $event->getThrowable();

        if($throwable instanceof ProcessorException){
            $event->setResponse(new JsonResponse(['message'=>
                'PaymentTransactionFailed:'. $throwable->getMessage()],
                $throwable->getCode() == 0 ? 500 : $throwable->getCode()
            ));

        }else if($throwable instanceof ValidationException){
        $event->setResponse(new JsonResponse(['errors'=>$throwable->getData()],
            $throwable->getCode() == 0 ? 500 : $throwable->getCode()
        ));

    }
        else {
            $event->setResponse(
                new JsonResponse(['message' => 'Error:' . $throwable->getMessage()],
                    $throwable->getCode() == 0 ? 500 : $throwable->getCode()
                )
            );
        }
    }
}
