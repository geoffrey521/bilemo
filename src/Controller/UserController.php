<?php

namespace App\Controller;

use App\Exception\InvalidFormException;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Knp\Component\Pager\PaginatorInterface;
use Nelmio\ApiDocBundle\Annotation\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Cache;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\User;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Annotations as OA;
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
     *     description="Page not found"
     * )
     * @OA\Parameter(
     *     name="page",
     *     in="query",
     *     description="Page number",
     *     @OA\Schema(type="integer", default=1)
     * )
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    public function listAction(
        PaginatorInterface $paginator,
        Request            $request
    )
    {
        $page = $request->query->getInt('page', 1);

        $users = $paginator->paginate($this->getUser()->getUsers(), $page, 10);

        if (count($users->getItems()) === 0) {
            throw new NotFoundHttpException('Page not found');
        }

        $response = $this->json(
            $users,
            200,
            ['Content-Type' => 'application/json'],
            ['groups' => 'list_users']
        );

        $response->setEtag(md5($response->getContent()));
        $response->setPublic(); // make sure the response is public/cacheable
        $response->isNotModified($request);

        return $response;
    }

    /**
     * Get one user
     * @OA\Response(
     *     response=200,
     *     description="Return user datas",
     *     @OA\JsonContent(
     *        ref=@Model(type=User::class)
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
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/users/{id}', name: 'app_user_show', methods: 'GET')]
    #[Cache(lastModified: 'user.getUpdatedAt()', etag: "'User' ~ user.getId() ~ user.getUpdatedAt().getTimestamp()")]
    public function showAction(User $user)
    {

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
     * @OA\Response(
     *     response=201,
     *     description="Return user datas",
     *     @OA\JsonContent(
     *        ref=@Model(type=User::class)
     *    )
     *
     * )
     * @OA\Response(
     *     response=400,
     *     description="Syntax error or invalid fields"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Must be connected"
     * )
     *
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     * @throws \Exception
     */
    #[Route('/users', name: 'app_user_create', methods: 'POST')]
    public function createUser(
        Request                $request,
        EntityManagerInterface $entityManager
    )
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(UserType::class);
        $form->submit($data);
        if (!$form->isValid()) {
            throw new InvalidFormException($form);
        }

        $user = $form->getData();

        $user->setCustomer($this->getUser());

        $entityManager->persist($user);
        $entityManager->flush();
        $this->cache->delete('users_page');

        return $this->json(
            $user,
            '201'
        );
    }

    /**
     * Edit user
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
     * @OA\Response(
     *     response=200,
     *     description="Return user datas",
     *     @OA\JsonContent(
     *        ref=@Model(type=User::class)
     *    )
     * )
     * @OA\Response(
     *     response=400,
     *     description="Syntax error or invalid fields"
     * )
     * @OA\Response(
     *     response=401,
     *     description="Must be connected"
     * )
     *
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     * @throws \Exception
     */
    #[Route('/users/{id}', name: 'app_user_edit', methods: 'PUT')]
    public function edit(
        Request                $request,
        User                   $user,
        EntityManagerInterface $entityManager
    )
    {
        $data = json_decode($request->getContent(), true);
        $form = $this->createForm(UserType::class, $user);
        $form->submit($data);
        if (!$form->isValid()) {
            throw new InvalidFormException($form);
        }

        $entityManager->persist($user);
        $entityManager->flush();
        $this->cache->delete('users_page');

        return $this->json(
            $user,
            '200'
        );
    }

    /**
     * Delete user
     *
     * @OA\Response(
     *     response=204,
     *     description="user deleted",
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
     * @OA\Tag(name="Users")
     * @Security(name="Bearer")
     */
    #[Route('/users/{id}', name: 'app_user_delete', methods: 'DELETE')]
    public function deleteUser(int $id, EntityManagerInterface $entityManager, UserRepository $userRepository)
    {
        $user = $userRepository->findOneBy(['id' => $id]);

        if ($user && $user->getCustomer() === $this->getUser()) {
            $entityManager->remove($user);
            $entityManager->flush();
            $this->cache->delete('users_page');

            return $this->json(
                'User deleted',
                '204'
            );
        }

        throw new NotFoundHttpException('User not found');
    }


}
