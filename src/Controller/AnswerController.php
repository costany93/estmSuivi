<?php

namespace App\Controller;

use App\Entity\Answer;
use App\Entity\Discussion;
use App\Form\AnswerType;
use App\Form\DiscussionType;
use App\Repository\AnswerRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class AnswerController extends AbstractController
{
    public function __construct(EntityManagerInterface $em,AnswerRepository $answerRepository)
    {
        $this->em = $em;
        $this->answerRepository = $answerRepository;
    }
    /**
     * permet de répondre à une discussion
     * @Route("/answer/{slug}/index", name="answer_index")
     * @param Discussion $discussion
     * @return Response
     * @Security("(is_granted('ROLE_ETUDIANT') and user.getEtudiant().getClub() == discussion.getClub()) or is_granted('ROLE_ADMIN')", message="Vous n'avez pas le droit d'accéder à ce club car ce n'est pas le votre")
     */
    public function index(Discussion $discussion,Request $request): Response
    {
        $answer = new Answer();
        $form = $this->createForm(AnswerType::class,$answer);
        $form->handleRequest($request);

        $answers = $this->answerRepository->findBy(['discussion' => $discussion->getId()],['id' => 'DESC']);

        if($form->isSubmitted() && $form->isValid()){
            $answer->setDiscussion($discussion)
                    ->setEtudiant($this->getUser()->getEtudiant())
            ;
            $this->em->persist($answer);
            $this->em->flush();

            $this->addFlash("success","Merci d'avoir répondu, votre réponse lui sera d'un grand aide");

            return $this->redirectToRoute("answer_index",[
                'slug' => $discussion->getSlug()
            ]);
        }
        return $this->render('answer/index.html.twig', [
            'discussion' => $discussion,
            'club' => $discussion->getClub(),
            'form' => $form->createView(),
            'answers' => $answers
        ]);
    }
}
