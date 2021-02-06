<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Etudiant;
use App\Entity\President;
use App\Repository\RoleRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PresidentController extends AbstractController
{
    public function __construct(RoleRepository $roleRepository,EntityManagerInterface $em)
    {
        $this->roleRepository = $roleRepository;
        $this->em = $em;
    }
    /**
     * permet de definir le president d'un club
     * @Route("/president/{id}/new", name="president_new")
     * @param Etudiant $etudiant
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function new(Etudiant $etudiant): Response
    {
        $role = $this->roleRepository->findOneBy(['title' => 'ROLE_PRESIDENT_CLUB']);
        $president = new President();
        $etudiant->getUser()->addUserRole($role);
        $president->setEtudiant($etudiant)
                ->setClub($etudiant->getClub())
        ;
        $this->em->persist($etudiant);
        $this->em->persist($president);
        $this->em->flush();
        return $this->redirectToRoute("club_students",[
            'slug' => $etudiant->getClub()->getSlug()
        ]);
    }

    /**
     * permet de destituer un président de son pose
     * @Route("/president/{id}/destituate", name="president_destituate")
     * @param President $president
     * @return Response
     * @IsGranted("ROLE_ADMIN")
     */
    public function destituate(President $president){
        $role = $this->roleRepository->findOneBy(['title' => 'ROLE_PRESIDENT_CLUB']);
        //ici  l'étudiant perd son role de president dans l'application
        $etudiant = $president->getEtudiant();
        $etudiant->getUser()->removeUserRole($role);
        $club = new Club();
        $et = new Etudiant();

        $president->setClub($club)
                ->setEtudiant($et)
        ;
        //ici on le supprime de la table des presidents
        $this->em->remove($president);
        $this->em->flush();

        return $this->redirectToRoute("club_students",[
            'slug' => $etudiant->getClub()->getSlug()
        ]);
    }
}
