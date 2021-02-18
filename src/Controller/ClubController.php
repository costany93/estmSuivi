<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Etudiant;
use App\Form\ClubType;
use App\Repository\ClubRepository;
use App\Services\UniqueFileUpload;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class ClubController extends AbstractController
{
    public function __construct(ClubRepository $clubRepository, EntityManagerInterface $em)
    {
        $this->clubRepository = $clubRepository;
        $this->em = $em;
    }
    /**
     * index des clubs
     * @Route("/club", name="club_index")
     * @IsGranted("ROLE_USER")
     */
    public function index(): Response
    {

        $clubs = $this->clubRepository->findAll();
        return $this->render('club/index.html.twig', [
            'clubs' => $clubs,
        ]);
    }

    /**
     * permet de d'accéder à son club
     * @Route("/club/{slug}/acceuil", name="club_acceuil")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function home(Club $club): Response{
        return $this->render('club/accueil.html.twig',[
            'club' => $club
        ]);
    }

    /**
     * permet de voir tout les participants d'un clubs
     * @Route("/club/{slug}/participants", name="club_students")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function allStudent(Club $club){

        //$etudiants = $this->er->findBy(['club_id' => $club->getId()]);
        return $this->render('club/students.html.twig', [
            'club' => $club,
        ]);
    }

    /**
     * permet à un étudiant d'adhérer à un club
     * @Route("/club/check/{id}", name="club_join_etudiant")
     * @param Etudiant $etudiant
     * @return Response
     * @Security("(is_granted('ROLE_PRESIDENT_CLUB') and user.getEtudiant().getClub() == etudiant.getClub()) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function joinClub(Etudiant $etudiant){

        $club = $etudiant->getMembership()->getClub();
        $adhesion = $etudiant->getMembership();
        //ici on met à jour les informations de l'étudiant
                $etudiant->setUser($etudiant->getUser())
                        ->setClasse($etudiant->getClasse())
                        ->setFiliere($etudiant->getFiliere())
                        ->setClub($club)
                ;
                $club->addEtudiant($etudiant);
                $this->em->persist($etudiant);
                $this->em->persist($club);

                $etudiantFictif = new Etudiant();
                $clubFictif = new Club();
                $adhesion->setEtudiant($etudiantFictif)
                        ->setClub($clubFictif)
                ;
                $this->em->remove($adhesion);

                $this->em->flush();

                $this->addFlash("success","Félicitation! ".$etudiant->getUser()->getFirstname()." a rejoint le club ".$club->getNom());
        return $this->redirectToRoute('club_acceuil', [
            'club' => $club,
            'user' => $etudiant->getUser(),
            'slug' => $club->getSlug()
        ]);
    }

    /**
     * permet de sortir d'un club
     * 	marechal.laure@live.com
     * @Route("/club/quit/{id}", name="club_quit_etudiant")
     * @param Etudiant $etudiant
     * @return Response
     * @Security("(is_granted('ROLE_PRESIDENT_CLUB') and user.getEtudiant().getClub() == etudiant.getClub()) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */

    public function quit(Etudiant $etudiant){
        $adhesion = $etudiant->getMembership();
        $etudiant->setUser($etudiant->getUser())
                ->setClasse($etudiant->getClasse())
                ->setFiliere($etudiant->getFiliere())
                        ->setClub(null)
                ;
        $this->em->persist($etudiant);

        $etudiantFictif = new Etudiant();
        $clubFictif = new Club();
        $adhesion->setEtudiant($etudiantFictif)
                ->setClub($clubFictif)
        ;
        $this->em->remove($adhesion);
                $this->em->flush();
                $this->addFlash("warning","Vous avez acceptez de sortir ".$etudiant->getUser()->getFirstname()." du club");
        $clubs = $this->clubRepository->findAll();
        return $this->redirectToRoute('club_index',['clubs' => $clubs]);
        }
        
        /**
         * permet de créer un nouveau club
         * @route("/admin/club/create", name="club_create")
         * @param Request
         * @param UniqueFileUpload $uniqueFileUpload
         * @return Response
         */
        public function create(Request $request, UniqueFileUpload $uniqueFileUpload):Response
        {
            $club = new Club();

            $clubs = $this->clubRepository->findAll();

            $form = $this->createForm(ClubType::class, $club);
            $form->handleRequest($request);

            if($form->isSubmitted() && $form->isValid()){
                //je récupère l'image
                $coverImage = $form->get('coverImage')->getData();

                //cette fonction permet de crée le nom du fichier et de le déplacé dans le dossier public/uplaods
                $fichier = $uniqueFileUpload->getName($coverImage);

                //je joins le nom du fichier dans la base de donnée
                $club->setCoverImage($fichier);

                $this->em->persist($club);
                $this->em->flush();

                $this->addFlash('success','Le nouveau a été créé avec success vous pouvez y accédez');

                return $this->redirectToRoute('admin_club_index',[
                    'clubs' => $clubs
                ]);
            }
            return $this->render('club/create.html.twig',[
                'form' => $form->createView()
            ]);
        }
}
