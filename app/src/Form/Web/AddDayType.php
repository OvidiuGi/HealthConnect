<?php

namespace App\Form\Web;

use App\Entity\Day;
use App\Repository\ScheduleRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddDayType extends AbstractType
{
    private RequestStack $requestStack;

    private ScheduleRepository $scheduleRepository;

    public function __construct(RequestStack $requestStack, ScheduleRepository $scheduleRepository)
    {
        $this->requestStack = $requestStack;
        $this->scheduleRepository = $scheduleRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        $builder->add('date', ChoiceType::class, [
            'placeholder' => 'Choose a date',
            'choices' => $this->scheduleRepository->getAvailableDates($currentRequest->get('id')),
            'choice_label' => function($choice) {
                return $choice->format('d-m-Y');
            }
        ])
            ->add('startTime', TimeType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'choice',

            ])
            ->add('endTime', TimeType::class, [
                'input' => 'datetime_immutable',
                'widget' => 'choice',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Day::class
        ]);
    }
}