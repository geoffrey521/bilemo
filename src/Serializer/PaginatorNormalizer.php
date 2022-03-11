<?php

namespace App\Serializer;

use App\Entity\Product;
use Knp\Bundle\PaginatorBundle\Pagination\SlidingPagination;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\ContextAwareNormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

final class PaginatorNormalizer implements ContextAwareNormalizerInterface
{
    public function __construct(private UrlGeneratorInterface $urlGenerator, private UserNormalizer $userNormalizer, private ProductNormalizer $productNormalizer)
    {}

    public function supportsNormalization(mixed $data, string $format = null, array $context = []): bool
    {
      return $data instanceof SlidingPagination;
    }

    public function normalize(mixed $object, string $format = null, array $context = [])
    {
        $normalizer = ($object->getItems()[0] instanceof Product) ? $this->productNormalizer : $this->userNormalizer;

        $items = [];
        foreach ($object->getItems() as $item) {
            $items[] = $normalizer->normalize($item, $format, $context);
        }
        $paginationData = $object->getPaginationData();
        
        $data = ['items' => $items, '_links' => []];


        if (isset($paginationData['next']))
        {
            $data['_links'][] = [
                'rel' => 'next_page',
                'href' => $this->urlGenerator->generate($object->getRoute(), [
                    'page' => $paginationData['next'],
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'method' => 'get'
            ];
        }
        if (isset($paginationData['previous']))
        {
            $data['_links'][] = [
                'rel' => 'previous_page',
                'href' => $this->urlGenerator->generate($object->getRoute(), [
                    'page' => $paginationData['previous'],
                ], UrlGeneratorInterface::ABSOLUTE_URL),
                'method' => 'get'
            ];
        }
        $data['_links'][] = [
            'rel' => 'first_page',
            'href' => $this->urlGenerator->generate($object->getRoute(), [
                'page' => $paginationData['first'],
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'method' => 'get'
        ];
        $data['_links'][] = [
            'rel' => 'last_page',
            'href' => $this->urlGenerator->generate($object->getRoute(), [
                'page' => $paginationData['last'],
            ], UrlGeneratorInterface::ABSOLUTE_URL),
            'method' => 'get'
        ];
        if($object->getRoute() === 'app_user_list') {
            $data['_links'][] = [
                'rel' => 'create',
                'href' => $this->urlGenerator->generate('app_user_create', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'method' => 'post'
            ];
        }

        return $data;
    }
}
