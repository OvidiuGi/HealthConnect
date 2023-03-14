<?php

namespace App\Form\Admin;

use App\Entity\Building;
use App\Entity\User;
use App\Repository\BuildingRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateMedicType extends AbstractType
{
    private BuildingRepository $buildingRepository;

    public function __construct(BuildingRepository $buildingRepository)
    {
        $this->buildingRepository = $buildingRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('firstName', TextType::class)
            ->add('lastName', TextType::class)
            ->add('email', TextType::class)
            ->add('telephoneNr', TextType::class)
            ->add('cnp', TextType::class)
            ->add('specialization', TextType::class)
            ->add('experience', NumberType::class)
            ->add('office', EntityType::class, [
                'class' => 'App\Entity\Building',
                'choices' => $this->buildingRepository->findAll(),
                'choice_label' => function(Building $building) {
                    return $building->address;
                },
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}