<?php

namespace App\Form;

use App\Entity\Club;
use Faker\Provider\ar_JO\Text;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClubType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom',TextType::class, $this->getConfiguration('Nom du club','Entrer le nom du nouveau club'))
            ->add('coverImage',FileType::class, [
                'label' => false,
                'mapped' => false,
                'multiple' => false,
                'required' => true
            ])
            ->add('description',TextareaType::class, $this->getConfiguration('Description','Entrer la description du club',[
                'attr' => [
                    'rows' => 5
                ]
            ]))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Club::class,
        ]);
    }
}
