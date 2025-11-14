<?php

namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UtilisateurType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Ton nom',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Ton email',
            ])
            ->add('motDePasse', PasswordType::class, [
                'label' => 'Mot de passe',
            ])
            ->add('avatar', ChoiceType::class, [
                'label' => 'Choisis ton avatar',
                'choices' => [
                    'Maman Blonde' => 'maman_blonde_normal.png',
                    'Maman Bouclée' => 'maman_boucle_normal.png',
                    'Maman Brune' => 'maman_brune_normal.png',
                    'Maman Lisse' => 'maman_lisse_normal.png',
                    'Maman Noir Bouclé' => 'maman_noir_bouclé_normal.png',
                    'Maman Rousse Lisse' => 'maman_rousse_lisse_normal.png',
                    'Maman Rousse' => 'maman_rousse_normal.png',
                    'Maman Voile' => 'maman_voile_normal.png',
                ],
                'expanded' => true,
                'required' => true,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }
}
