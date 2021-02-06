<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\HttpFoundation\Request;

class ImageController extends AbstractController
{
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }
    /**
     * @Route("/image", name="image_index")
     */
    public function index(): Response
    {
        return $this->render('image/index.html.twig', [
            'controller_name' => 'ImageController',
        ]);
    }

     /**
      * permet d'ajouter une nouvelle image à une activité
     * @Route("/image/{slug}/new", name="image_new")
     * @param Activity $activity
     * @param Request $request
     * @return Response
     * @Security("(is_granted('ROLE_PRESIDENT_CLUB') and user.getEtudiant().getClub() == activity.getClub()) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à cette ressource")
     */
    public function new(Activity $activity, Request $request): Response
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

            return $this->redirectToRoute('activity_show',[
                'slug' => $activity->getSlug()
            ]);
        }
        return $this->render('image/new.html.twig', [
            'form' => $form->createView(),
            'activity' => $activity
        ]);
    }
}
