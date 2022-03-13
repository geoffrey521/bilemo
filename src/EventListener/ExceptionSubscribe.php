<?php

namespace App\EventListener;

use App\Exception\InvalidFormException;
use JetBrains\PhpStorm\ArrayShape;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

class ExceptionSubscribe implements EventSubscriberInterface
{

    public function onExceptionEvent(ExceptionEvent $event)
    {
        $exception = $event->getThrowable();

        if ($exception instanceof InvalidFormException) {
            $data = $this->getErrorsFromForm($exception->getForm());
            $response = new JsonResponse($data, 400);
            $event->setResponse($response);
        }
        if ($exception instanceof HttpExceptionInterface) {

            $data = [
                "error" => ($exception->getStatusCode() === 404) ? "Not found" : $exception->getMessage()
            ];

            $response = new JsonResponse($data, $exception->getStatusCode());
            $event->setResponse($response);
        }
        if ($exception instanceof \InvalidArgumentException) {
            $data = [
                "Field error" => $exception->getMessage()
            ];

            $response = new JsonResponse($data, 400);
            $event->setResponse($response);
        }
    }

    protected function getErrorsFromForm(FormInterface $form)
    {
        $errors = array();
        foreach ($form->getErrors() as $error) {
            $errors[] = $error->getMessage();
        }
        foreach ($form->all() as $childForm) {
            if ($childForm instanceof FormInterface) {
                if ($childErrors = $this->getErrorsFromForm($childForm)) {
                    $errors[$childForm->getName()] = $childErrors;
                }
            }
        }

        return $errors;
    }

    #[ArrayShape([ExceptionEvent::class => "string"])]
    public static function getSubscribedEvents(): array
    {
        return [
            ExceptionEvent::class => 'onExceptionEvent',
        ];
    }
}
