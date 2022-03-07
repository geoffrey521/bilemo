<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\SerializerInterface;


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
     * @OA\Response(
     *     response=404,
     *     description="this page does not contain any products"
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

        return $this->json(
            $products,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'list_products']
        );
    }


    /**
     * Get product detail
     * @OA\Response(
     *     response=200,
     *     description="Return product datas",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=Product::class))
     *    )
     * )
     * @OA\Response(
     *     response=401,
     *     description="must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Product not found"
     * )
     * @OA\Tag(name="Product")
     * @Security(name="Bearer")
     */
    #[Route('/products/{id}', name: 'app_product_detail', methods: 'GET')]
    public function showAcion(ProductRepository $productRepository,int $id, SerializerInterface $serializer)
    {
        $product = $productRepository->findOneBy(['id' => $id]);

        if (!$product) {
            throw new NotFoundHttpException('Product not found');
        }

        return $this->json(
            $product,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'show_product']
        );

    }

}
