<?php

namespace App\Form\Web;

use App\Entity\Appointment;
use App\Entity\Service;
use App\Repository\AppointmentRepository;
use App\Repository\DayRepository;
use App\Repository\ScheduleRepository;
use App\Repository\ServiceRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAppointmentType extends AbstractType
{
    private RequestStack $requestStack;

    private UserRepository $userRepository;

    private DayRepository $dayRepository;

    private ScheduleRepository $scheduleRepository;

    private ServiceRepository $serviceRepository;

    private AppointmentRepository $appointmentRepository;

    public function __construct(
        UserRepository $userRepository,
        DayRepository $dayRepository,
        RequestStack $requestStack,
        ScheduleRepository $scheduleRepository,
        ServiceRepository $serviceRepository,
        AppointmentRepository $appointmentRepository
    ) {
        $this->userRepository = $userRepository;
        $this->dayRepository = $dayRepository;
        $this->requestStack = $requestStack;
        $this->scheduleRepository = $scheduleRepository;
        $this->serviceRepository = $serviceRepository;
        $this->appointmentRepository = $appointmentRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();

        $builder->add('service', EntityType::class, [
            'placeholder' => 'Choose a service',
            'class' => 'App\Entity\Service',
            'choices' => $this->userRepository->getDoctorServices($currentRequest->get('id')),
            'choice_label' => function(Service $service) {
                return $service->name;
            }
        ])
            ->add('date', ChoiceType::class, [
                'placeholder' => 'Choose a date',
                'choices' => $this->scheduleRepository->getDoctorDays($currentRequest->get('id')),
                'choice_label' => function(\DateTimeImmutable $day) {
                    return $day->format('Y-m-d');
                },
                'mapped' => false
            ]);
            $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($currentRequest) {
                $form = $event->getForm();

                $duration = $this->serviceRepository->findOneBy(['id' => $event->getData()['service']])->duration;
                $date = $this->scheduleRepository->getDoctorDays($currentRequest->get('id'))[$event->getData()['date']];
                $form->add('timeInterval', ChoiceType::class, [
                    'placeholder' => 'Choose an option',
                    'choices' => $this->getIntervalsByServiceId($date['date'], $duration),
                    'choice_label' => function($choice) {
                        return $choice;
                    }
                ]);
            });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class
        ]);
    }

    private function getIntervalsByServiceId(\DateTimeImmutable $date,int $duration): array
    {
        $day = $this->dayRepository->findOneBy(['date' => $date]);
        $result = [];
        $startTime = $day->getStartTime();

        while($startTime < $day->getEndTime()) {
            $oldStartTime = $startTime;
            $startTime = \DateTimeImmutable::createFromMutable(date_add(\DateTime::createFromImmutable($startTime), date_interval_create_from_date_string($duration . ' minutes')));
            if (!$this->isIntervalBusy($date,$oldStartTime, $startTime)) {
                $result[] = [$oldStartTime->format('H:i'). '-'. $startTime->format('H:i') ];
            }

        }

        return $result;
    }

    public function isIntervalBusy(\DateTimeImmutable $date, \DateTimeImmutable $oldStartTime, \DateTimeImmutable $startTime): bool
    {
        $busyIntervals = $this->appointmentRepository->getAppointmentsIntervalByDate($date);
        foreach ($busyIntervals as $busyInterval) {
            $busyInterval = explode('-', $busyInterval['timeInterval']);

            if ($oldStartTime->format('H:i') >= $busyInterval[0] && $oldStartTime->format('H:i') < $busyInterval[1]) {
                return true;
            }
            if ($startTime->format('H:i') >= $busyInterval[0] && $startTime->format('H:i') < $busyInterval[1]) {
                return true;
            }
        }
        return false;
    }
}