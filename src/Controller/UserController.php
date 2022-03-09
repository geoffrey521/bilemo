<?php

namespace App\Controller;

use App\Repository\CustomerRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class UserController extends AbstractController
{
    public function __construct(private CacheInterface $cache)
    {
    }

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
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    public function listAction(
        PaginatorInterface $paginator,
        Request            $request
    )
    {
        $page = $request->query->getInt('page', 1);

        $users = $this->cache->get('users_page-'.$page,
            function (ItemInterface $item) use ($paginator, $page) {
            $item->expiresAfter(3600);
            return $paginator->paginate($this->getUser()->getUsers(), $page, 5);
        });

        if (count($users->getItems()) === 0) {
            throw new NotFoundHttpException('No users found at this page');
        }

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
     *     description="Must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/users/{id}', name: 'app_user_show', methods: 'GET')]
    public function showAction(int $id, UserRepository $userRepository)
    {
        $user = $this->cache->get('user-'.$id,
            function (ItemInterface $item) use ($id, $userRepository) {
            $item->expiresAfter(3600);
            return $userRepository->findOneBy([
                'id' => $id,
                'customer' => $this->getUser()
            ]);
        });

        if (!$user) {
            throw new NotFoundHttpException('User not found');
        }

        return $this->json(
            $user,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'show_user']
        );
    }

    /**
     * Create user
     *
     * @OA\RequestBody(
     *    required=true,
     *    description="Pass user credentials",
     *    @OA\JsonContent(
     *     required={"email","password"},
     *     @OA\Property(property="firstname", type="string", format="text"),
     *     @OA\Property(property="lastname", type="string", format="text"),
     *     @OA\Property(property="email", type="string", format="email", example="user1@mail.com"),
     *     @OA\Property(property="phone_number", type="string", format="text"),
     *     @OA\Property(property="addressLine1", type="string", format="text"),
     *     @OA\Property(property="addressLine2", type="string", format="text"),
     *     @OA\Property(property="zipcode", type="string", format="text"),
     *     @OA\Property(property="city", type="string", format="text"),
     *     @OA\Property(property="country", type="string", format="text")
     *    ),
     * ),
     *    * @OA\Response(
     *     response=200,
     *     description="Return user datas",
     *     @OA\JsonContent(
     *        type="array",
     *        @OA\Items(ref=@Model(type=User::class))
     *    )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Syntax error"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="incorrectly filled fields"
     * )
     *
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     * @throws \Exception
     */
    #[Route('/users/create', name: 'app_user_create', methods: 'POST')]
    public function createUser(
        Request                $request,
        SerializerInterface    $serializer,
        ValidatorInterface     $validator,
        EntityManagerInterface $entityManager
    )
    {
        $data = $request->getContent();

        if (!json_decode($data)) {
            throw new BadRequestHttpException('Syntax error');
        }


        $user = $serializer->deserialize($data, User::class, 'json');

        $user->setCustomer($this->getUser());

        $errors = $validator->validate($user);

        if ($errors->count() > 0) {
            throw new BadRequestHttpException($errors);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        $this->cache->delete('users_page');

        return $this->json(
            'user added successfully',
            '201',
            ['Content-Type' => 'application/json']
        );
    }

    /**
     * Delete user
     *
     * @OA\Response(
     *     response=200,
     *     description="user {id} successfully deleted",
     * )
     * @OA\Response(
     *     response=401,
     *     description="must be connected"
     * )
     * @OA\Response(
     *     response=404,
     *     description="User not found"
     * )
     *
     * @OA\Tag(name="User")
     * @Security(name="Bearer")
     */
    #[Route('/users/delete/{id}', name: 'app_user_delete', methods: 'DELETE')]
    public function deleteUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['id' => $id]);

        if ($user && $user->getCustomer() === $this->getUser()) {
            $userId = $user->getId();
            $entityManager->remove($user);
            $entityManager->flush();
            $this->cache->delete('users_page');

            return $this->json(
                "User " . $userId . " successfully deleted",
                '200'
            );
        }

        throw new NotFoundHttpException('User not found');
    }
}
