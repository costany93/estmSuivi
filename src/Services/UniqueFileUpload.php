<?php

namespace App\Services;

use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UniqueFileUpload extends AbstractController{
    
    public function getName(UploadedFile $image){

        //génére un nom unique pour le fichier
        $fichier = md5(uniqid()).'.'.$image->guessExtension();

        //Envoie le fichier dans le dossier public/uploads
        $image->move(
            $this->getParameter('images_directory'),
            $fichier
        );

        return $fichier;
    }
}