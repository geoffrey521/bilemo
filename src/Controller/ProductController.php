<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Product;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;


class ProductController extends AbstractController
{
    public function __construct(private CacheInterface $cache)
    {
    }

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
     *     description="No products at this page"
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
        ProductRepository  $productRepository,
        PaginatorInterface $paginator,
        Request            $request
    )
    {
        $page = $request->query->getInt('page', 1);

        $products = $this->cache->get('products_page_'.$page,
            function (ItemInterface $item) use ($paginator, $productRepository, $page) {
                $item->expiresAfter(3600);
                return $paginator->paginate($productRepository->findAll(), $page, 5);
            });

        if (count($products->getItems()) === 0) {
            throw new NotFoundHttpException('No products at this page');
        }

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
    public function showAcion(ProductRepository $productRepository, int $id)
    {
        $product = $this->cache->get('product_detail_'.$id,
            function (ItemInterface $item) use ($productRepository, $id) {
            $item->expiresAfter(3600);
            return $productRepository->findOneBy(['id' => $id]);
        });

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
