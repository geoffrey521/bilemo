<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user_list', methods: 'GET')]
    /**
     * Get customer's user list
     * @OA\Response(
     *     response=200,
     *     description="Return list of users linked to the customer",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *    )
     * )
     * @OA\Response(
     *     response=401,
     *     description="must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="No users found at this page"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="The page number",
     *     @OA\Schema(type="integer", default=1)
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function listAction(
        PaginatorInterface $paginator,
        Request $request
    )
    {
        $users = $paginator->paginate($this->getUser()->getUsers(), $request->get('page', 1), 5);

        return $this->json(
            $users,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'list_users']
        );
    }

    /**
     * Get one user
     * @OA\Response(
     *     response=200,
     *     description="Return user datas",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *    )
     * )
     * @OA\Response(
     *     response=401,
     *     description="must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/users/{id}', name: 'app_user_show', methods: 'GET')]
    public function showAction(int $id, UserRepository $userRepository, CustomerRepository $customerRepository)
    {
        $user = $userRepository->findOneBy([
            'id' => $id,
            'customer' => $this->getUser()
        ]);

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $this->json(
            $user,
            200,
            ['Content-Type' => 'application/json']
        );

    }
}
