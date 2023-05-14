<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Hospital;
use App\Entity\User;
use App\Repository\HospitalRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateMedicType extends AbstractType
{
    public function __construct(
        private readonly HospitalRepository $hospitalRepository
    ) {
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
                'class' => 'App\Entity\Hospital',
                'choices' => $this->hospitalRepository->findAll(),
                'choice_label' => function (Hospital $hospital) {
                    return $hospital->name;
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
