<?php

namespace App\EventListener;

use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscribe implements EventSubscriberInterface
{
    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();
        if ($exception instanceof \Exception) {
            $data = [
                'message' => $exception->getMessage()
            ];
            $response = new JsonResponse($data);
            $event->setResponse($response);
        }
    }

    #[ArrayShape([ExceptionEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
