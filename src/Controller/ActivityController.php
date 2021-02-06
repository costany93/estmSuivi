<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Club;
use App\Form\ActivityType;
use App\Repository\ActivityRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class ActivityController extends AbstractController
{
    public function __construct(ActivityRepository $activityRepository,EntityManagerInterface $em/*participationRepository $participationRepository*/)
    {
        $this->activityRepository = $activityRepository;
        $this->em = $em;
        //$this->participationRepository = $participationRepository;
    }
    /**
     * permet de voir la vue d'ensemble d'une activité
     * @Route("/activity/{slug}/show", name="activity_show")
     * @param Activity $activity
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function show(Activity $activity): Response
    {
        return $this->render('activity/show.html.twig', [
            'activity' => $activity
        ]);
    }

    /**
     * permet d'afficher les activité d'un club
     * @Route("/activity/{slug}/index", name="activity_index")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function index(Club $club): Response
    {
        //$participation = $this->participationRepository->findAll();
        $activities = $this->activityRepository->findBy(['club' => $club->getId()],['id' => 'DESC']);
        return $this->render('activity/index.html.twig', [
            'activities' => $activities,
            'club' => $club
        ]);
    }

    /**
     * permet de créer une activité
     * @Route("/activity/{slug}/create", name="activity_add")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_PRESIDENT_CLUB') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function create(Club $club,Request $request): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class,$activity);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $activity->setClub($club);
            $this->em->persist($activity);
            $this->em->flush();

            $this->addFlash('success','Activité créé avec success, vous allez le voir dans votre fil');
            return $this->redirectToRoute('activity_index',[
                'slug' => $club->getSlug()
            ]);
        }
        $activities = $this->activityRepository->findAll();
        return $this->render('activity/add.html.twig', [
            'club' => $club,
            'form' => $form->createView()
        ]);
    }

}
