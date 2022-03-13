<?php

namespace App\Controller\v1;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Model;
use Nelmio\ApiDocBundle\Annotation\Security;
use OpenApi\Annotations as OA;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Cache\CacheInterface;

#[Route('/v1')]
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
     *        @OA\Items(ref=@Model(
     *     type=Product::class,
     *     groups={"list_products"}
     *     )
     * ),
     *    )
     * )
     * @OA\Response(
     *     response=401,
     *     description="must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="Page not found"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page number",
     *     @OA\Schema(type="integer", default=1)
     * )
     *
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

        $products = $paginator->paginate($productRepository->findAll(), $page, 10);

        if (count($products->getItems()) === 0) {
            throw new NotFoundHttpException('Page not found');
        }

        $response = $this->json(
            $products,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'list_products']
        );

        $response->setEtag(md5($response->getContent()));
        $response->setPublic(); // make sure the response is public/cacheable
        $response->isNotModified($request);

        return $response;
    }


    /**
     * Get product detail
     * @OA\Response(
     *     response=200,
     *     description="Return product datas",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(
     *     type=Product::class,
     *     groups={"show_product"}
     *     ))
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
     * @OA\Tag(name="Products")
     * @Security(name="Bearer")
     */
    #[Route('/products/{id}', name: 'app_product_detail', methods: 'GET')]
    #[Cache(lastModified: 'product.getUpdatedAt()', etag: "'Product' ~ product.getId() ~ product.getUpdatedAt().getTimestamp()")]
    public function showAcion(Product $product)
    {
        $response = $this->json(
            $product,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'show_product']
        );

        $response->setEtag(md5($response->getContent()));
        $response->setPublic(); // make sure the response is public/cacheable
        $response->setLastModified($product->getUpdatedAt());

        return $response;

    }
}
