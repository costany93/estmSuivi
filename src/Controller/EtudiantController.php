<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class EtudiantController extends AbstractController
{
    /**
     * permet de créer un nouvelle utilisateur
     * @Route("/etudiant/create", name="etudiant_create")
     * @return Response
     */
    public function create(): Response
    {
        $user = new User();

        $form = $this->createForm(UserType::class, $user);
        

        return $this->render('etudiant/create.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
