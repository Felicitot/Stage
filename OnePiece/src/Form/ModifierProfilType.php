<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Validator\Constraints\File;

class ModifierProfilType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('Ville', TextType::class)
            ->add('Commune', TextType::class)
            ->add('District', TextType::class)
            ->add('Region', TextType::class)
            ->add('Domicile', TextType::class)
            ->add('NumTel', TextType::class)
            ->add('Email', EmailType::class)
            ->add('Dupli', CheckboxType::class, [
                'required' => false
            ])
            ->add('photo', FileType::class, [
                'required' => false,
                'mapped' => true,
                'data_class' => null,
                'label' => 'Photo de profil (JPG, PNG, max 2 Mo)',
                'constraints' => [
                    new File([
                        'maxSize' => '2M',
                        'mimeTypes' => [
                            'image/jpeg',
                            'image/png',
                        ],
                        'mimeTypesMessage' => 'Choisissez une image valide (JPEG ou PNG)',
                    ])
                ],
            ]);
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
