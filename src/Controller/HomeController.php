<?php

namespace App\Controller;

use App\Entity\Activity;
use App\Entity\Information;
use App\Repository\ActivityRepository;
use App\Repository\ClubRepository;
use App\Repository\InformationRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    public function __construct(ClubRepository $clubRepository, EntityManagerInterface $em, ActivityRepository $activityRep, InformationRepository $informationRep)
        {
            $this->clubRepository = $clubRepository;
            $this->em = $em;
            $this->activityRep = $activityRep;
            $this->informationRep = $informationRep;
        }
    /**
     * @Route("/", name="home_index")
     */
    public function index(): Response
    {
        $activities = $this->activityRep->findBy([],['id' => 'DESC'], 15,0);
        $informations = $this->informationRep->findBy([],['id' => 'DESC'], 8,0);
        return $this->render('home/index.html.twig', [
            'informations' => $informations,
            'activities' => $activities 
        ]);
    }
}
