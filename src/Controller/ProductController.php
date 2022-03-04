<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;


class ProductController extends AbstractController
{

    #[Route('/products', name: 'app_product_list', methods: 'GET')]
    /**
     * Get products list
     * @OA\Response(
     *     response=200,
     *     description="Return a list of our phones",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *    )
     * )
     * @OA\Response(
     *     response=401,
     *     description="must be connected"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page number",
     *     @OA\Schema(type="integer", default=1)
     * )
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     */
    public function listAction(
        ProductRepository $productRepository,
        PaginatorInterface $paginator,
        Request $request
    )
    {
        $data = $productRepository->findAll();
        $products = $paginator->paginate($data, $request->get('page', 1), 5);
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');

        return $this->json($products);
    }

}
