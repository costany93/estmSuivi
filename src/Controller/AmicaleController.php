<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Activity;
use App\Entity\Image;
use App\Entity\Information;
use App\Entity\Participation;
use App\Form\ActivityType;
use App\Form\ImageType;
use App\Form\InformationType;
use App\Repository\ActivityRepository;
use App\Repository\InformationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class AmicaleController extends AbstractController
{
    public function __construct(ActivityRepository $activityRepository,EntityManagerInterface $em,InformationRepository $informationRepository)
    {
        $this->activityRepository = $activityRepository;
        $this->em = $em;
        $this->informationRepository = $informationRepository;
        //$this->participationRepository = $participationRepository;
    }

    /**
     * permet d'accéder à la page d'acceuil de l'amicale
     * @Route("/amicale/index", name="amicale_index")
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function index(): Response
    {
        //$participation = $this->participationRepository->findAll();
        $activities = $this->activityRepository->findBy(['club' => null],['id' => 'DESC']);
        $informations = $this->informationRepository->findBy(['club' => null],['id' => 'DESC']);
        return $this->render('amicale/index.html.twig', [
            'activities' => $activities,
            'informations' => $informations
        ]);
    }

    /**
     * permet de voir la vue d'ensemble d'une activité
     * @Route("/amicale/activity/{slug}/show", name="amicale_activity_show")
     * @param Activity $activity
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function activity_show(Activity $activity): Response
    {
        return $this->render('amicale/activity/show.html.twig', [
            'activity' => $activity
        ]);
    }

     /**
     * permet de créer une activité
     * @Route("/amicale/activity/create", name="amicale_activity_add")
     * @return Response
     * @Security("is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function activity_create(Request $request): Response
    {
        $activity = new Activity();
        $form = $this->createForm(ActivityType::class,$activity);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){

            $activity->setClub(null);
            $this->em->persist($activity);
            $this->em->flush();

            $this->addFlash('success','Activité créé avec success, vous allez le voir dans votre fil');
            return $this->redirectToRoute('amicale_index',[
                
            ]);
        }
        $activities = $this->activityRepository->findAll();
        return $this->render('amicale/activity/add.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * permet d'afficher toutes les participations à une activité de l'amicale
     * @Route("/amicale/participation/{slug}/all", name="amicale_participation_index")
     * @param Activity $activity
     * @return Response
     *  @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à cette ressource")
     */
    public function participation_index(Activity $activity){

        $club = $activity->getClub();
        return $this->render('amicale/participation/index.html.twig',[
            'club' => $club,
            'activity' => $activity
        ]);
    }

     /**
      * permet d'ajouter une nouvelle image à une activité
     * @Route("/amicale/image/{slug}/new", name="amicale_image_new")
     * @param Activity $activity
     * @param Request $request
     * @return Response
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à cette ressource")
     */
    public function image_new(Activity $activity, Request $request): Response
    {
        $img = new Image();
        $form = $this->createForm(ImageType::class, $img);
        $form->handleRequest($request);

        if($form->isSubmitted() and $form->isValid()){
            //on récupère notre ou nos images
            $images = $form->get('image')->getData();

            //On boucle sur toutes les images récupéré
            foreach($images as $image){
                //on génere le nom du fichier
                $fichier = md5(uniqid()).'.'.$image->guessExtension();

                //on copie l'image dans notre dossier upload
                $image->move(
                    $this->getParameter('images_directory'),
                    $fichier
                );

                //on stocke le nom de l'image dans la base de données
                $image2 = new Image();
                $image2->setLegend($form->get('legend')->getData())
                        ->setName($fichier)
                        ->setActivity($activity)    
                ;
                
                //on lie l'image à son activity
                $activity->addImage($image2);

            }

            $this->em->persist($activity);
            $this->em->flush();

            $this->addFlash('success','Image ajouté avec success');

            return $this->redirectToRoute('amicale_activity_show',[
                'slug' => $activity->getSlug()
            ]);
        }
        return $this->render('amicale/image/new.html.twig', [
            'form' => $form->createView(),
            'activity' => $activity
        ]);
    }


    /**
     * permet de voir toutes les informations d'un club
     * @Route("/information/amicale", name="amicale_information_index")
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function information_index(): Response
    {
        $informations = $this->informationRepository->findBy(['club' => null],['id' => 'DESC']);
        return $this->render('amicale/information/index.html.twig', [
            'informations' => $informations,
        ]);
    }


    /**
     * permet d'ajouter un information relatif à un club
     * @Route("/information/amicale/add", name="amicale_information_new")
     * @return Response
     * @Security("is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function add(Request $request){
        $information = new Information();
        $form = $this->createForm(InformationType::class, $information);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            $information->setClub(null);
            $this->em->persist($information);
            $this->em->flush();

            $this->addFlash("success","Information ajouté avec succes, il defilera sur le fil des actualités");

            return $this->redirectToRoute("amicale_index",[
            ]);
        }
        return $this->render('amicale/information/new.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * permet de voir la vue d'ensemble d'une information
     * @Route("/amicale/information/{slug}/show", name="amicale_information_show")
     * @param Information $information
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT')) or is_granted('ROLE_ADMIN')", message="Vous avez pas accès à cette ressources")
     */
    public function show(Information $information): Response
    {
        return $this->render('amicale/information/show.html.twig', [
            'information' => $information
        ]);
    }
}
