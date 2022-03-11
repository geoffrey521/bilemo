<?php

namespace App\Serializer;


use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class ProductNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private ObjectNormalizer $objectNormalizer)
    {}

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof Product;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
       $data = $this->objectNormalizer->normalize($object, $format, $context);

       $data['_links'] = [
           'self' => [
               'rel' => 'show',
               'href' => $this->urlGenerator->generate('app_product_detail', [
                   'id' => $object->getId(),
               ], UrlGeneratorInterface::ABSOLUTE_URL),
               'method' => 'get'
           ]
       ];

       return $data;
    }
}
