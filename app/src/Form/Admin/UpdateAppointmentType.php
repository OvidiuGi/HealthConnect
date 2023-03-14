<?php

namespace App\Form\Admin;

use App\Entity\Appointment;
use App\Entity\Service;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UpdateAppointmentType extends AbstractType
{
    private UserRepository $userRepository;

    private ServiceRepository $serviceRepository;

    public function __construct(UserRepository $userRepository, ServiceRepository $serviceRepository)
    {
        $this->userRepository = $userRepository;
        $this->serviceRepository = $serviceRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('service', EntityType::class, [
            'class' => 'App\Entity\Service',
            'choices' => $this->userRepository->getDoctorServices($options['data']->getDoctor()->getId()),
            'choice_label' => function(Service $service) {
                return $service->name;
            }
        ])
            ->add('doctor', TextType::class, [
            'data' => $options['data']->getDoctor()->firstName . ' ' . $options['data']->getDoctor()->lastName,
            'disabled' => true
        ])
            ->add('customer', TextType::class, [
            'data' => $options['data']->getCustomer()->firstName . ' ' . $options['data']->getCustomer()->lastName,
            'disabled' => true
        ])
            ->add('startTime', TimeType::class)
            ->add('endTime', TimeType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class,
        ]);
    }
}