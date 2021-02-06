<?php

namespace App\Controller;

use App\Entity\Etudiant;
use App\Entity\User;
use App\Form\EtudiantType;
use App\Form\UserType;
use App\Repository\RoleRepository;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AccountController extends AbstractController
{
    public function __construct(EntityManagerInterface $em, UserPasswordEncoderInterface $encoder,RoleRepository $roleRepository)
    {
        $this->em = $em;
        $this->encoder = $encoder;
        $this->roleRepository = $roleRepository;
    }
    /**
     * permet de créer un nouvelle utilisateur
     * @Route("/account/user/create", name="account_user_create")
     * @param Request
     * @return Response
     */
    public function create(Request $request): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $password = $this->encoder->encodePassword($user, $user->getHashPassword());
            $user->setHashPassword($password);
            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash("success", "Utilisateur ajouter avec success");
            return $this->redirectToRoute('account_etudiant_create');
        }
        return $this->render('account/createUser.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * permet d'ajouter un nouvel étudiant
     * @Route("/account/etudiant/create", name="account_etudiant_create")
     * @param Request
     * @return Response
     */
    public function createEtudiant(Request $request): Response
    {
        //récupération du role étudiant
        $role = $this->roleRepository->findOneBy(['title' => 'ROLE_ETUDIANT']);
        $etudiant = new Etudiant();
        $form = $this->createForm(EtudiantType::class, $etudiant);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            //On récupère les informations relatives à la requette envoyé afin de créer notre utilisateur qui n'est autre qu'un étudiant
            $firstname = $request->request->get('etudiant')['firstname'];
            $lastname = $form->get('lastname')->getData();
            $sexe = $form->get('sexe')->getData();
            $dateNaiss = $form->get('dateNaiss')->getData();
            $email = $form->get('email')->getData();
            $phone = $form->get('phone')->getData();
            $hash = $form->get('hashPassword')->getData();
            //$date = DateTime::createFromFormat('Y-m-d', '2020-12-12');
            /*dump($dateNaiss);
            die();*/

            //On initialise un nouvelle utilisateur afin de l'enregistrer première dans la base de données avant l'étudiant
            $user = new User();
            
            $password = $this->encoder->encodePassword($user, $hash);
            $user->setFirstname($firstname)
                ->setLastname($lastname)
                ->setSexe($sexe)
                ->setDateNaiss($dateNaiss)
                ->setEmail($email)
                ->setPhone($phone)
                ->setHashPassword($password)
                ->addUserRole($role)
            ;

            $this->em->persist($user);

            //On met à jour notre propriété user de l'étudiant par les informations recupéré plus haut
            $etudiant->setUser($user);

            //on persist l'etudiant et on l'envoie dans la base de données
            $this->em->persist($etudiant);
            $this->em->flush();

            $this->addFlash("success", "Etudiant ajouter avec success");
            return $this->redirectToRoute('account_etudiant_create');
        }
        return $this->render('account/createEtudiant.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    /**
     * permet de s'authentifier
     * @Route("/login", name="account_login")
     */
    public function login(AuthenticationUtils $utils): Response
    {
        $error = $utils->getLastAuthenticationError();
        $lastname = $utils->getLastUsername();
        return $this->render('account/login.html.twig', [
            'lastname' => $lastname,
            'hasError' => $error
        ]);
    }
    /**
     * permet de se déconnecter
     * @Route("/logout", name="account_logout")
     */
    public function logout()
    {
        
    }
}
