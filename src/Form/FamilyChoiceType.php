<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotBlank;

class FamilyChoiceType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // DÉJÀ PRÉSENT
            ->add('composition', ChoiceType::class, [
                'label' => 'Situation familiale',
                'choices' => [
                    'Bébé' => 'bebe',
                    'Adolescent' => 'ado',
                    'Les deux' => 'les_deux',
                ],
                'expanded' => true,   // radios
                'multiple' => false,
                'constraints' => [new NotBlank()],
            ])

            // 🔸 NOUVEAU : Type de logement
            ->add('logement', ChoiceType::class, [
                'label' => 'Type de logement',
                'choices' => [
                    'Studio' => 'studio',
                    'Appartement' => 'appartement',
                    'Maison' => 'maison',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,         // pas encore lié à une entité
                'constraints' => [new NotBlank()],
            ])

            // 🔸 NOUVEAU : Situation professionnelle
            ->add('situationPro', ChoiceType::class, [
                'label' => 'Situation professionnelle',
                'choices' => [
                    'CDI' => 'cdi',
                    'CDD' => 'cdd',
                    'Intérim' => 'interim',
                    'Sans emploi' => 'sans_emploi',
                ],
                'expanded' => true,
                'multiple' => false,
                'mapped' => false,
                'constraints' => [new NotBlank()],
            ])
        ;
    }
}
