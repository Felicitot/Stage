<?php

namespace App\Form;

use App\Entity\Application;
use App\Entity\Role;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class RoleUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('role',TextType::class,[
                'label' => 'role',
                'attr' => ['placeholder' =>'Ajouter un rôle...' ]
            ])
            ->add('description',TextType::class,[
                'label' => 'Description'
            ])
             ->add('applications', EntityType::class, [
                'class' => Application::class,
                'choice_label' => 'nomAppli', // champ affiché dans le dropdown
                'multiple' => true,           // permet de sélectionner plusieurs applications
                'expanded' => false,          // dropdown classique
                'required' => true,
                'placeholder' => 'Sélectionnez les applications',
            ])
           ->add('Ajouter',SubmitType::class);
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Role::class,
        ]);
    }
}
