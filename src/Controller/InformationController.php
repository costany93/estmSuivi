<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Information;
use App\Form\InformationType;
use App\Repository\InformationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class InformationController extends AbstractController
{
    public function __construct(EntityManagerInterface $em,InformationRepository $informationRepository)
    {
        $this->em = $em;
        $this->informationRepository = $informationRepository;
    }
    /**
     * permet de voir toutes les informations d'un club
     * @Route("/information/{slug}", name="information_index")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function index(Club $club): Response
    {
        $informations = $this->informationRepository->findBy(['club' => $club->getId()],['id' => 'DESC']);
        return $this->render('information/index.html.twig', [
            'informations' => $informations,
            'club' => $club
        ]);
    }


    /**
     * permet d'ajouter un information relatif à un club
     * @Route("/information/{slug}/add", name="information_add")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function add(Club $club,Request $request){
        $information = new Information();
        $form = $this->createForm(InformationType::class, $information);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $information->setClub($club);
            $this->em->persist($information);
            $this->em->flush();

            $this->addFlash("success","Information ajouté avec succes, il defilera sur le fil des actualités");

            return $this->redirectToRoute("club_acceuil",[
                'slug' => $club->getSlug()
            ]);
        }
        return $this->render('information/add.html.twig', [
            'club' => $club,
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de voir la vue d'ensemble d'une information
     * @Route("/information/{slug}/show", name="information_show")
     * @param Information $information
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function show(Information $information): Response
    {
        return $this->render('information/show.html.twig', [
            'information' => $information
        ]);
    }
}
