<?php
/**
 * Created by PhpStorm.
 * User: khysh
 * Date: 09/09/2019
 * Time: 00:09
 */

namespace App\Controller;

use App\Entity\User;
use App\Handler\UserHandler;
use App\Repository\ImageRepository;
use App\Repository\TrickRepository;
use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;



/**
 * @property TrickRepository trickRepository
 */
class HomeController extends AbstractController
{
    /**
     * @var string
     */
    private $uploadDirAbsolutePath;

    /**
     * @property TrickRepository trickRepository
     */
    private $trickRepository;
    /**
     * @var ImageRepository
     */
    private $imageRepository;

    public function __construct(TrickRepository $trickRepository, ImageRepository $imageRepository,string $uploadDirAbsolutePath)
    {
        $this->trickRepository = $trickRepository;
        $this->imageRepository = $imageRepository;
        $this->uploadDirAbsolutePath = $uploadDirAbsolutePath;
    }

    /**
     * @Route("/", name="home")
     * @return Response
     */
    public function index(): Response
    {
        //initialisation du repository demandé
        $images = $this->imageRepository->getFourRandomImage();
        $tricks = $this->trickRepository->getAllTricks(1);
        return $this->render('pages/home.html.twig', [
                'tricks' => $tricks,
                'images' => $images,
                'uploadDirAbsolutePath' => $this->uploadDirAbsolutePath,
                'current_menu'=>'home',
            ]
        );
    }

    /**
     * @Route("/moretricks", name="moretricks")
     *
     * @param Request $request
     * @return Response
     */
    public function moreTrick(Request $request): Response
    {
        $page = $request->query->getInt("page");
        if($page <= 1){
            $page = 2;
        }
        $tricks = $this->trickRepository->getAllTricks($page);
        return $this->render('parts/fortricks.html.twig', [
                'tricks' => $tricks,
            ]
        );
    }

    /**
     * @Route("/login", name="login")
     * @param AuthenticationUtils $authenticationUtils
     * @return Response
     */
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // retrouver une erreur d'authentification s'il y en a une
        $error = $authenticationUtils->getLastAuthenticationError();
        // retrouver le dernier identifiant de connexion utilisé
        $lastUsername = $authenticationUtils->getLastUsername();
        return $this->render('pages/login/form.html.twig', [
                'last_username' => $lastUsername,
                'error' => $error,
            ]
        );
    }

    /**
     * @Route("/logout", name="logout")
     * @throws Exception
     */
    public function logout(): void
    {
        throw new Exception('This should never be reached!');
    }

    /**
     * @Route("/create-account", name="createUser")
     * @param Request $request
     * @param UserHandler $handler
     * @return Response
     */
    public function createUser(Request $request,UserHandler $handler): Response
    {
        $user = new User();
        if($handler->handle($request, $user)) {
            return $this->redirectToRoute("home");
        }
        return $this->render('pages/login/create_form.html.twig', [
            "form" => $handler->createView()
        ]);
    }
}