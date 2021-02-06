<?php

namespace App\Services;

class UniqueFileUpload{
    public function getName( $image){

        //génére un nom unique pour le fichier
        $fichier = md5(uniqid()).'.'.$image->guessExtension();

        //Envoie le fichier dans le dossier public/uploads
        $image->move(
            $this->getParameter('images_directory'),
            $fichier
        );
    }
}