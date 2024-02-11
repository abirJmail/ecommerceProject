<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
        ->add('email', EmailType::class, [
            'label' => 'Email',
            'attr' => [
                'placeholder' => 'Entrez votre adresse email',
            ],
            'constraints' => [
                new NotBlank([
                    'message' => 'Veuillez entrer une adresse email',
                ]),
                new Length([
                    'min' => 3,
                    'minMessage' => 'Votre adresse email doit contenir au moins {{ limit }} caractères',
                    'max' => 50,
                    'maxMessage' => 'Votre adresse email doit contenir au maximum {{ limit }} caractères',
                ]),
                new Regex([
                    'pattern' => '/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/',
                    'message' => 'Veuillez entrer une adresse email valide',
                ]),
            ],
            'attr' => [
                'class' => 'mb-3 form-control'
            ]
        ])
        // ->add('subject')
        ->add('subject', TextType::class, [
            'label' => 'Subject',
            'attr' => [
                'placeholder' => 'Choisissez un Sujet',
            ],
            'attr' => [
                'class' => 'mb-3 form-control'
            ]
        ])

        ->add('content', TextareaType::class,[
            'attr' => [
                'class' => 'mb-3 form-control'
            ]
        ])
        ->add('envoyer', SubmitType::class,[
                            'label' => 'envoyer',
                'attr' => [
                    'class' => 'mb-3 btn btn-dark'
                ]
            
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
