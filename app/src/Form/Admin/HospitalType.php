<?php

declare(strict_types=1);

namespace App\Form\Admin;

use App\Entity\Hospital;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HospitalType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('address', TextType::class)
            ->add('postalCode', TextType::class)
            ->add('city', TextType::class)
            ->add('startHour', TimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-timepicker'],
                'input' => 'datetime_immutable',
            ])
            ->add('endHour', TimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-timepicker'],
                'input' => 'datetime_immutable',
            ])
            ->add('name', TextType::class)
            ->add('phone', TextType::class)
            ->add('email', EmailType::class)
            ->add('description', TextType::class)
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hospital::class,
        ]);
    }
}
