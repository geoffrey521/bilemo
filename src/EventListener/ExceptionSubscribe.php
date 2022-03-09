<?php

namespace App\EventListener;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscribe implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof HttpExceptionInterface) {
            $data = [
                'message' => $exception->getMessage()
            ];
            $response = new JsonResponse($data, $exception->getStatusCode());
            $event->setResponse($response);
        }
    }

//    #[ArrayShape([ExceptionEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
//            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
