<?php

namespace App\Controller;

use App\Repository\ClubRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;


class AdminController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, ClubRepository $clubRepository, UserRepository $userRepository)
    {
        $this->em = $em;
        $this->clubRepository = $clubRepository;
        $this->userRepository = $userRepository;
    }
    /**
     * permet à l'administrateur de se connecter
     * @Route("/admin/login", name="admin_account_login")
     */
    public function index(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $lastname = $utils->getLastUsername();
        return $this->render('admin/account/index.html.twig', [
            'hasError' => $error !== null,
            'username' => $lastname
        ]);
    }

    /**
     * permet à l'administrateur de se déconnecter
     * Route("/admin/logout", name="admin_account_logout")
     */
    public function logout(){

    }

    /**
     * Page d'acceuil de l'administration
     * @Route("/admin/index", name="admin_index")
     */
    public function home(){
        return $this->render('/admin/index.html.twig');
    }

    //GESTION DES CLUBS
    /**
     * permet d'afficher la gestion des clubs
     * @Route("/admin/club/index", name="admin_club_index")
     */
    public function club_index(){
        $clubs = $this->clubRepository->findAll();
        return $this->render('/admin/club/index.html.twig',[
            'clubs' => $clubs
        ]);
    }

    //GESTION DES ETUDIANTS
    /**
     * permet d'afficher la gestion des clubs
     * @Route("/admin/etudiant/index", name="admin_etudiant_index")
     */
    public function etudiant_index(){
        return $this->render('/admin/etudiant/index.html.twig',[
        ]);
    }

    //GESTION DES administrateur
    /**
     * permet de voir tout les administrateur
     * @Route("/admin/user/index", name="admin_user_index")
     */
    public function user_index(){
        $admins = $this->userRepository->findBy([],['id' => 'DESC']);
        return $this->render('/admin/user/index.html.twig',[
            "admins" => $admins
        ]);
    }
}
