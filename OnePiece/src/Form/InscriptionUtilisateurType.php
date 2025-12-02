<?php


namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class InscriptionUtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // PIÈCE D'IDENTITÉ
            
           ->add('PieceId', TextType::class, [
                'label' => 'Numéro de pièce',
                'constraints' => [
                    new NotBlank(['message' => 'Le numéro de pièce est obligatoire.']),
                    new Length([
                        'min' => 12,
                        'max' => 12,
                        'exactMessage' => 'Le numéro de pièce doit contenir exactement {{ limit }} chiffres.',
                    ]),
                    new Regex([
                        'pattern' => '/^\d{12}$/',
                        'message' => 'Le numéro de pièce doit contenir uniquement des chiffres.',
                    ]),
                ],
            ])

            ->add('FaitLe', DateType::class, [
                'widget' => 'single_text',
                'label' => 'Fait le',
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('FaitA', TextType::class, [
                'label' => 'Fait à',
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('Dupli', CheckboxType::class, [
                'label' => 'Duplicata',
                'required' => false,
            ])
            ->add('DateDupli', DateType::class, [
                'widget' => 'single_text',
                'required' => false,
                'label' => 'Date Duplicata',
            ])
            ->add('LieuDupli', TextType::class, [
                'required' => false,
                'label' => 'Lieu Duplicata',
            ])
             ->add('Recto', FileType::class, [
            'label' => 'Image recto CIN',
            'mapped' => false, // ⚠️ ce champ ne correspond pas à une propriété de l'entité directement
            'required' => true,
            'constraints' => [
                new File([
                    'maxSize' => '5M',
                    'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                    'mimeTypesMessage' => 'Format d’image non valide (jpg, png, webp uniquement).',
                ]),
                 new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ],
            ])

      
            ->add('Verso', FileType::class, [
                'label' => 'Image verso CIN',
                'mapped' => false,
                'required' => true,
                'constraints' => [
                    new File([
                        'maxSize' => '5M',
                        'mimeTypes' => ['image/jpeg', 'image/png', 'image/webp'],
                        'mimeTypesMessage' => 'Format d’image non valide (jpg, png, webp uniquement).',
                    ]),
                    new NotBlank([ 'message' => 'champ est obligatoire.',]),
                ],
            ])

            

            // NATIONALITÉ
            ->add('Nationalite', ChoiceType::class, [
                'choices' => [
                    'Malagasy' => 'Malagasy',
                    'Autres' => 'autres',
                ],
                'expanded' => true,
                'multiple' => false,
                'label' => 'Nationalité',
                'constraints' => new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])

            // INFORMATIONS GÉNÉRALES
            ->add('civilite', ChoiceType::class, [
                'choices' => ['Monsieur' => 'Mr', 'Madame' => 'Mme'],
                'expanded' => true,
                'multiple' => false,
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('Nom', TextType::class,[
                 'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('Prenoms', TextType::class,[
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('DateNaissance', DateType::class, [
                'widget' => 'single_text',
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('Domicile', TextType::class)
         ->add('NumTel', TextType::class, [
            'label' => 'Numéro téléphone',
            'attr' => ['placeholder' => '+261XXXXXXXXX'],
            'constraints' => [
                new NotBlank(['message' => 'champ est obligatoire.']),
                new Regex([
                    'pattern' => '/^\+261\d{9}$/',
                    'message' => 'Le numéro doit commencer par +261 et contenir exactement 12 chiffres.',
                ]),
            ],
        ])


            ->add('Email', EmailType::class,[
                 'label' => 'test@gmail.com',
                'constraints' =>  [
                    new NotBlank([ 'message' => 'champ est obligatoire.',]),
                    new \Symfony\Component\Validator\Constraints\Email([
                      'message' => 'L’adresse email "{{ value }}" n’est pas valide.',
                    ]),
                ]
            ])
            ->add('Ville', TextType::class,[
                'constraints' => new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('District', TextType::class,[
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('Region', TextType::class,[
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('commune', TextType::class,[
                'constraints' =>  new NotBlank([ 'message' => 'champ est obligatoire.',]),
            ])
            ->add('photo', FileType::class, [
                    'label' => 'Photo de profil (JPEG/PNG)',
                    'mapped' => false,
                    'required' => false, // pas obligatoire
                    'constraints' => [
                        new File([
                            'maxSize' => '2M',
                            'mimeTypes' => [
                                'image/jpeg',
                                'image/png',
                            ],
                            'mimeTypesMessage' => 'Format d’image non valide (JPEG ou PNG uniquement).',
                        ])
                    ],
                ])
            ->add('plainPassword', RepeatedType::class, [
    'type' => PasswordType::class,
    
    'first_options' => [
        'label' => 'Votre mot de passe',
        'hash_property_path' => 'password',
        'attr' => ['placeholder' => 'Mot de passe']
    ],
    'second_options' => [
        'label' => 'Confirmez votre mot de passe',
        'hash_property_path' => 'password',
        'attr' => ['placeholder' => 'Confirmez votre mot de passe']
    ],
    'mapped' => false,
    'invalid_message' => 'Les mots de passe ne correspondent pas.',
    'constraints' => [
        new Length([
            'min' => 8,
            'max' => 16,
            'minMessage' => 'Le mot de passe doit contenir au moins {{ limit }} caractères.',
            'maxMessage' => 'Le mot de passe ne doit pas dépasser {{ limit }} caractères.'
        ]),
        new Regex([
            'pattern' => '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).+$/',
            'message' => 'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial.'
        ])
    ],
]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        
             $resolver->setDefaults([
            'constraints' =>[
                new UniqueEntity(
                    [
                        'entityClass'=> Utilisateur::class,
                        'fields' => 'PieceId'
                    ]
                )
            ], 
            'data_class' => Utilisateur::class,
        ]);
    }
}
