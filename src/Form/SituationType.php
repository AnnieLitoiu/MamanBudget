<?php

namespace App\Form;

use App\Entity\Situation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SituationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // nombre d'enfants
            ->add('nbEnfants', ChoiceType::class, [
                'label' => 'Nombre d’enfants',
                'choices' => [
                    '1 enfant' => 1,
                    '2 enfants' => 2,
                    '3 enfants ou +' => 3,
                ],
                'expanded' => true,  
                'multiple' => false,
            ])
            // revenu mensuel
            ->add('revenuMensuel', IntegerType::class, [
                'label' => 'Revenu mensuel (€)',
                'attr' => [
                    'placeholder' => 'Ex: 1800',
                ],
            ])
            // type de logement
            ->add('logement', ChoiceType::class, [
                'label' => 'Type de logement',
                'choices' => [
                    'Studio' => 'studio',
                    'Appartement' => 'appartement',
                    'Penthouse' => 'penthouse',
                    'Villa' => 'villa',
                ],
                'multiple' => false,
                'expanded' => true, // on gère les cartes nous-mêmes dans le Twig
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Situation::class,
        ]);
    }
}
