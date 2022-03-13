<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class UserNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private ObjectNormalizer $objectNormalizer)
    {}

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
        return $data instanceof User;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $data = $this->objectNormalizer->normalize($object, $format, $context);

        $data['_links'] = [
            [
                'rel' => 'show',
                'href' => $this->urlGenerator->generate('app_user_show', [
                    'id' => $object->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'method' => 'get'
            ],
            [
                'rel' => 'edit',
                'href' => $this->urlGenerator->generate('app_user_edit', [
                    'id' => $object->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'method' => 'put'
            ],
            [
                'rel' => 'delete',
                'href' => $this->urlGenerator->generate('app_user_delete', [
                    'id' => $object->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'method' => 'delete'
            ]
        ];

        return $data;
    }
}
