<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

class ProductController extends AbstractController
{
    public function __construct(private SerializerInterface $serializer)
    {
    }

    #[Route('/products', name: 'app_product_list', methods: 'GET')]
    public function listAction(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();

        $data = $this->serializer->serialize($products, 'json');
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        return $this->json($products);
    }
}
