<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Membership;
use App\Form\MembershipType;
use App\Repository\ClubRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


class AdhesionController extends AbstractController
{public function __construct(EntityManagerInterface $em,ClubRepository $clubRepository)
    {
        $this->em = $em;
        $this->clubRepository = $clubRepository;
    }
    /**
     * permet de demander à sortir d'un club
     * @Route("/adhesion/{slug}/quit", name="adhesion_quit")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function quit(Club $club,Request $request): Response
    {
        $adhesion = new Membership();
        $clubs = $this->clubRepository->findAll();
        $form = $this->createForm(MembershipType::class,$adhesion);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $adhesion->setClub($club)
                    ->setEtudiant($this->getUser()->getEtudiant())
                    ->setState(false)
            ;
            $this->em->persist($adhesion);
            $this->em->flush();

            $this->addFlash("success","Votre demande de sorti du club va etre traité très vite");

            return $this->redirectToRoute("club_index",[
                'clubs' => $clubs
            ]);
        }
        return $this->render('adhesion/quit.html.twig', [
            'form' => $form->createView(),
            'club' => $club
        ]);
    }

    /**
     * Affiche toute les demandes d'adhesion
     * @Route("/adhesion/{slug}/all", name="adhesion_all")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function allByClub(Club $club){
        return $this->render('adhesion/all.html.twig', [
            'club' => $club
        ]);
    }

    /**
     * permet de demander à intégrer un club
     * @Route("/adhesion/{slug}/new", name="adhesion_new")
     * @param Club $club
     * @return Response
     * @Security("is_granted('ROLE_ETUDIANT') or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function new(Club $club,Request $request): Response
    {
        $adhesion = new Membership();
        $clubs = $this->clubRepository->findAll();
        $form = $this->createForm(MembershipType::class,$adhesion);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $adhesion->setClub($club)
                    ->setEtudiant($this->getUser()->getEtudiant())
                    ->setState(true)
            ;
            $this->em->persist($adhesion);
            $this->em->flush();

            $this->addFlash("success","Votre demande d'adhesion au club va etre traité très vite");

            return $this->redirectToRoute("club_index",[
                'clubs' => $clubs
            ]);
        }
        return $this->render('adhesion/adherer.html.twig', [
            'form' => $form->createView(),
            'club' => $club
        ]);
    }
}
