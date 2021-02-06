<?php

namespace App\Form;

use App\Entity\Activity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ActivityType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title',TextType::class,$this->getConfiguration('Nom de l\'activité','Entrer le nom de l\'activité'))
            ->add('startDate',DateTimeType::class,$this->getConfiguration('Date de l\'activité','Entrer la date de l\'activité',[
                'widget' => 'single_text'
            ]))
            ->add('lieu',TextType::class,$this->getConfiguration('Lieu','Entrer le lieu du deroulement de l\'activité'))
            ->add('description',TextareaType::class,$this->getConfiguration('Description détaillé','Expliquer de manière assez clair le but de cette activité'))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Activity::class,
        ]);
    }
}
