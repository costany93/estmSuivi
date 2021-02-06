<?php

namespace App\DataFixtures;

use App\Entity\Activity;
use App\Entity\Classe;
use App\Entity\Club;
use App\Entity\Departement;
use App\Entity\Etudiant;
use App\Entity\Filiere;
use App\Entity\Information;
use App\Entity\Role;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
        $faker = Factory::create('fr_FR');
        // $product = new Product();
        // $manager->persist($product);

        //Tableau des roles
        $rolesTable = [];
        //Ajout des roles
        $roleTable = ['ROLE_ETUDIANT','ROLE_PRESIDENT_CLUB'];
        $rl_length = count($roleTable);
        for($i = 0;$i<$rl_length;$i++){
            $role = new Role();
            $role->setTitle($roleTable[$i]);
            $manager->persist($role);

            $rolesTable[] = $role;

        }
        //Déclartion des tableaux de nos entités
        $userTable = [];
        $filiereTable = [];
        $departementTable = [];
        $clubTable = [];
        $classeTable = [];
        $etudiantTable = [];

        $sexe = ['masculin','feminin'];

        //Ajout des utilisateurs
        for($i = 0; $i < 30; $i++){
            $user = new User();
            //$role = $rolesTable[mt_rand(0,count($rolesTable) - 1)];
            $user->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setSexe($faker->randomElement($sexe))
                ->setPhone($faker->phoneNumber)
                ->setEmail($faker->email)
                ->setHashPassword($this->encoder->encodePassword($user,'password'))
                ->setDateNaiss($faker->dateTimeBetween('-25 years'))
                //->addUserRole($role)
            ;
            $manager->persist($user);
            //J'ajoute chaque utilisateur dans le tableau des utilisateurs
            $userTable[] = $user;
        }


        

        //admin role
        $adminRole = new Role();
        $adminRole->setTitle('ROLE_ADMIN');
        $manager->persist($adminRole);
        //Ajout d'un administrateur
        $adminUser = new User();
        $adminUser->setFirstname('kandza')
                ->setLastname('Prince')
                ->setSexe('Masculin')
                ->setPhone('782569464')
                ->setEmail('kandzaprince@gmail.com')
                ->setHashPassword($this->encoder->encodePassword($adminUser, 'password'))
                ->setDateNaiss(DateTime::createFromFormat('Y-m-d','1998-12-18'))
                ->addUserRole($adminRole)
        ;
        $manager->persist($adminUser);

        $manager->flush();
        //Ajout des département
        $departement1 = ['Sciences - Technologies', 'Management - Communication','Génie électrique et Energies renouvelables'];
        $dep_length = count($departement1);

        //Ajout des filière
       $sciences = ['Télécommunication - Réseaux','Génie logiciel et Administration Réseaux', 'Sécurité des Systèmes d\'Information','Monétique et Transactions Sécurisées','Informatique et Multimédia'];
       $sc_length = count($sciences);
       $management = ['Marketing-Communication','Transport - Logistique','Finance-Comptabilité','Gestion des Ressources Humaines','Banque-Assurance','Management de projets et Innovation'];
       $mn_length = count($management);
       $geer = ['Génie électrique et Energies renouvelables'];
       $gr_length = count($geer);
       $club = ['Scientifique','CEMAD','humanitaire','multimédia','d\'anglais'];
       $sb_length = count($club);

        //Gestion des niveau des classes
       $niveau = ['Licence 1', 'Licence 2', 'Licence 3', 'Master 1', 'Master 2'];
       $niv_length = count($niveau);

        //Ajout des classes
        for($ni = 0; $ni < $niv_length; $ni++){
            $classe = new Classe();
            $classe->setNiveau($niveau[$ni])
                    ->setAnnee("2020")
            ;
            $manager->persist($classe);
            //j'ajoute la classe au tableau des classes
            $classeTable[] = $classe;
        }


         //Ajout des départements et filière
       for($i = 0; $i < $dep_length; $i++){
        $departement = new Departement();
        $departement->setNom($departement1[$i]);

        if($i == 0){
            for($sc = 0; $sc < $sc_length; $sc++){
                $filiere = new Filiere();
                $filiere->setNom($sciences[$sc]);
                $filiere->setDepartement($departement);
                $manager->persist($filiere);
                $filiereTable[] = $filiere;
            }
        }

        if($i == 1){
            for($mn = 0; $mn < $mn_length; $mn++){
                $filiere = new Filiere();
                $filiere->setNom($management[$mn]);
                $filiere->setDepartement($departement);
                $manager->persist($filiere);
                $filiereTable[] = $filiere;
            }
        }

        if($i == 2){
            for($gr = 0; $gr < $gr_length; $gr++){
                $filiere = new Filiere();
                $filiere->setNom($geer[$gr]);
                $filiere->setDepartement($departement);
                $manager->persist($filiere);
                $filiereTable[] = $filiere;
                
            }
        }

        $manager->persist($departement);
        //ajout du département dans le tableau des départements
        $departementTable[] = $departement;
   }
        

        //Ajout des clubs

        for($i = 0; $i < $sb_length; $i++){
            $nclub = new Club();
            $nclub->setNom($club[$i])
                ->setCoverImage($faker->imageUrl())
                ->setDescription($faker->paragraph())
            ;

            for($j = 0; $j < mt_rand(3,7); $j++){
                //ajout des informations avec club
                $clubInfo = new Information();
                $clubInfo->setContent($faker->paragraph())
                ->setTitle($faker->sentence(15))
                ->setCoverImage($faker->imageUrl())
                ->setClub($nclub)
                ;
                $manager->persist($clubInfo);
            }
            //Ajout de quelques activités
            for($a = 0; $a < mt_rand(5,15);$a++){
                $activity = new Activity();
                $activity->setDescription($faker->paragraph())
                        ->setLieu('Salle des activités')
                        ->setStartDate($faker->dateTimeBetween('now','+3 months'))
                        ->setClub($nclub)
                        ->setTitle($faker->paragraph())
                ;
                $manager->persist($activity);
            }
            $manager->persist($nclub);
            $clubTable[] = $nclub;
        }

        //Ajout des étudiants
        for($i = 0;$i < 30; $i++){
            //création d'utilisateur étudiant
            $user = new User();
            $role = $rolesTable[0];
            $user->setFirstname($faker->firstName)
                ->setLastname($faker->lastName)
                ->setSexe($faker->randomElement($sexe))
                ->setPhone($faker->phoneNumber)
                ->setEmail($faker->email)
                ->setHashPassword($this->encoder->encodePassword($user,'password'))
                ->setDateNaiss($faker->dateTimeBetween('-25 years'))
                ->addUserRole($role)
            ;
            $manager->persist($user);
            $etudiant = new Etudiant();

            //initialisation de l'utilisateur
            //$user = $userTable[mt_rand(0,count($filiereTable) - 1)];

            //initialisation de la Filiere
            $filiere = $filiereTable[mt_rand(0,count($filiereTable) - 1)];

            //initialisation de la classe
            $classe = $classeTable[mt_rand(0,count($classeTable) - 1)];

            //initialisation du club
            $nclub = $clubTable[mt_rand(0,count($clubTable) - 1)];

            //création de l'étudiant
            $etudiant->setUser($user)
                    ->setFiliere($filiere)
                    ->setClasse($classe)
                    ->setClub($nclub)
            ;
            //On ajoute ici les president des clubs
            /*if($nclub->getPresident() === null){
                $president = new President();
                $president->setClub($nclub)
                        ->setEtudiant($etudiant)
                        ;
                $manager->persist($president);
            }*/

            $manager->persist($etudiant);
            $etudiantTable[] = $etudiant;
        }

            $manager->flush();
        }

}
