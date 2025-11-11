<?php

namespace App\Form;

use App\Entity\Avatar;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AvatarType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // 👩 Couleur de peau
            ->add('couleurPeau', ChoiceType::class, [
                'label' => 'Couleur de peau',
                'choices' => [
                    'Claire' => 'claire',
                    'Moyenne' => 'moyenne',
                    'Mate' => 'mate',
                    'Foncée' => 'foncee',
                ],
                'expanded' => true,   // <-- pour icônes cliquables
                'multiple' => false,
            ])

            // 💇 Couleur de cheveux
            ->add('couleurCheveux', ChoiceType::class, [
                'label' => 'Couleur de cheveux',
                'choices' => [
                    'Blonde' => 'blonde',
                    'Brune' => 'brune',
                    'Noire' => 'noire',
                    'Rousse' => 'rousse',
                ],
                'expanded' => true,
                'multiple' => false,
            ])

            // 👗 Style vestimentaire
            ->add('styleVestimentaire', ChoiceType::class, [
                'label' => 'Style vestimentaire',
                'choices' => [
                    'Décontractée' => 'decontractee',
                    'Classique' => 'classique',
                    'Sport' => 'sport',
                    'BusinessWoman' => 'businesswoman',
                ],
                'expanded' => true,
                'multiple' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avatar::class,
        ]);
    }
}
