<?php

namespace App\Form;

use App\Entity\Hobby;
use App\Entity\Job;
use App\Entity\Profile;
use App\Entity\Personne;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PersonneType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname', TextType::class, [
                'label' => 'Prénoms'
            ])
            ->add('name')
            ->add('age')
            ->add('createdAt')
            ->add('updatedAt')
            ->add('profile', EntityType::class, [
                'expanded' => false,
                'required' => false,
                'class' => Profile::class,
                'multiple' => false,
                'attr' => [
                    'class' => 'select2'
                ]

            ])
            ->add('hobbies', EntityType::class, [
                'expanded' => false,
                'class' => Hobby::class,
                'multiple' => true,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('h')
                        ->orderBy('h.designation', 'ASC');
                },

                //Choise label est l'equivalent de Tostring pour la convertion d'un objet en string
                'choice_label' => 'designation',
                'required' => false,
                'attr' => [
                    'class' => 'select2'
                ]
            ])

            ->add('photo', FileType::class, [
                'label' => 'Vôtre image de profil (Des fichiers images uniquement)',

                // unmapped means that this field is not associated to any entity property
                // Lui dire qu'il n'y a pas de champ appelé photo
                'mapped' => false, 

                // make it optional so you don't have to re-upload the PDF file
                // every time you edit the Product details
                'required' => false,

                // unmapped fields can't define their validation using annotations
                // in the associated entity, so you can use the PHP constraint classes
                'constraints' => [
                    new File([
                        'maxSize' => '1024k',
                        'mimeTypes' => [
                            'image/gif',
                            'image/jpeg',
                            'image/jpg',
                        ],
                        'mimeTypesMessage' => 'Svp importé une image valide',
                    ])
                ],
            ])
            ->add('job', EntityType::class, [
                'attr' => [
                    'class' => 'select2'
                ],
                'class' => Job::class,
                'required' => false,
            ])
            ->add('edit', SubmitType::class)
        ;
    }


    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Personne::class,
        ]);
    }
    
}
