<?php

namespace App\Controller;

use App\Entity\Club;
use App\Entity\Discussion;
use App\Form\DiscussionType;
use App\Repository\DiscussionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DiscussionController extends AbstractController
{
    public function __construct(DiscussionRepository $dr,EntityManagerInterface $em)
    {
        $this->dr = $dr;
        $this->em = $em;
    }
    /**
     * permet d'afficher les différentes discussion d'un club
     * @Route("/discussion/{slug}/index", name="discussion_index")
     * @param Club $club
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function index(Club $club,Request $request): Response
    {
        $discussion = new Discussion();
        $form = $this->createForm(DiscussionType::class, $discussion);
        $form->handleRequest($request);
        $discussions = $this->dr->findBy(['club' => $club->getId()],['id' => 'DESC']);
        if($form->isSubmitted() && $form->isValid()){
            $discussion->setEtudiant($this->getUser()->getEtudiant())
                        ->setClub($club)
            ;

            $this->em->persist($discussion);
            $this->em->flush();

            $this->addFlash("success","Votre question a été posté, maintenant attendez la reaction des autres membres du groupe");
            return $this->redirectToRoute("discussion_index",[
                'slug' => $club->getSlug()
            ]);
        }
        return $this->render('discussion/index.html.twig', [
            'club' => $club,
            'form' => $form->createView(),
            'discussions' => $discussions
        ]);
    }

    /**
     * permet de créer une discussion*
     * @route("/discussion/{slug}/create", name="discussion_create")
     * @param Club $club
     * @return Response
     * @Security("is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == club", message="Vous n'avez pas le droit de créer une discussion ici car ce n'est pas votre club")
     */
    public function create(Club $club){
        return $this->render('discussion/index.html.twig', [
            'club' => $club,
        ]);
    }
}
