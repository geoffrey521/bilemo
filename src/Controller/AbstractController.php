<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController as BaseAbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;

class AbstractController extends BaseAbstractController
{
    /**
     * Returns a JsonResponse that uses the JMSserializer component if enabled, or json_encode.
     */
//    protected function json(mixed $data, int $status = 200, array $headers = [], array $context = []): JsonResponse
//    {
//        if ($this->container->has('hateoas.serializer.json_hal')) {
//            $json = $this->container->get('hateoas.serializer.json_hal')->serialize($data, 'json', array_merge([
//                'json_encode_options' => JsonResponse::DEFAULT_ENCODING_OPTIONS,
//            ], $context));
//
//            return new JsonResponse($json, $status, $headers, true);
//        }
//
//
//        return new JsonResponse($data, $status, $headers);
//    }
}