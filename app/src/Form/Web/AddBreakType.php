<?php

namespace App\Form\Web;

use App\Entity\Appointment;
use Doctrine\DBAL\Types\DateTimeImmutableType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddBreakType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', TextType::class, [
                'data' => $options['data']->getDate()->format('d-m-Y'),
                'disabled' => true
            ])
            ->add('startTime', TimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-timepicker'],
                'input' => 'datetime_immutable',
            ])
            ->add('endTime', TimeType::class, [
                'widget' => 'single_text',
                'attr' => ['class' => 'js-timepicker'],
                'input' => 'datetime_immutable',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class
        ]);
    }
}