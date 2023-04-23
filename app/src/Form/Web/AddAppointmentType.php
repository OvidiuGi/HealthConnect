<?php

namespace App\Form\Web;

use App\Entity\Appointment;
use App\Entity\Service;
use App\Repository\AppointmentRepository;
use App\Repository\DayRepository;
use App\Repository\ScheduleRepository;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AddAppointmentType extends AbstractType
{
    public function __construct(
        private UserRepository $userRepository,
        private DayRepository $dayRepository,
        private RequestStack $requestStack,
        private ScheduleRepository $scheduleRepository,
        private AppointmentRepository $appointmentRepository,
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $currentRequest = $this->requestStack->getCurrentRequest();
        $builder->add('service', EntityType::class, [
            'placeholder' => 'Select a service',
            'class' => 'App\Entity\Service',
            'choices' => $this->userRepository->getDoctorServices($currentRequest->get('id')),
            'choice_label' => function(Service $service) {
                return $service->name;
            }
        ])
            ->add('date', ChoiceType::class, [
                'placeholder' => 'Select a date',
                'choices' => $this->scheduleRepository->getDoctorDays($currentRequest->get('id')),
                'choice_label' => function(\DateTimeImmutable $value) {
                    return $value->format('Y-m-d');
                }
            ]);
        $formModifier = function (FormInterface $form, Service $service = null, \DateTimeImmutable $date = null) {
            $choices = (null == $service) || (null == $date) ? [] : $this->getIntervalsByServiceId($date, $service->duration);

            $form->add('startTime', ChoiceType::class, [
                'placeholder' => 'Select a time',
                'choices' => $choices,
                'choice_label' => function($choice) {
                    if ($choice instanceof \DateTimeImmutable) {
                        return $choice->format('H:i');
                    }

                    return '';
                }
            ]);
        };

        $builder->addEventListener(FormEvents::PRE_SET_DATA, function (FormEvent $event) use ($formModifier) {
            $data = $event->getData();
            $formModifier($event->getForm(), $data->getService(), $data->getDate());
        });

        $builder->get('service')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $date = $event->getForm()->getParent()->get('date')->getData();
            $service = $event->getForm()->getData();
            $formModifier($event->getForm()->getParent(), $service, $date);
        });

        $builder->get('date')->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) use ($formModifier) {
            $date = $event->getForm()->getData();
            $service = $event->getForm()->getParent()->get('service')->getData();
            $formModifier($event->getForm()->getParent(), $service, $date);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Appointment::class
        ]);
    }

    private function getIntervalsByServiceId(?\DateTimeImmutable $date,int $duration): array
    {
        if (!$date) {
            return [];
        }

        $day = $this->dayRepository->findOneBy(['date' => $date]);
        $result = [];
        $startTime = $day->getStartTime();

        while($startTime < $day->getEndTime()) {
            $oldStartTime = $startTime;
            $startTime = \DateTimeImmutable::createFromMutable(date_add(\DateTime::createFromImmutable($startTime), date_interval_create_from_date_string($duration . ' minutes')));
            if (!$this->isIntervalBusy($date,$oldStartTime, $startTime)) {
                $result[] = $oldStartTime;
            }

        }

        return $result;
    }

    public function isIntervalBusy(\DateTimeImmutable $date, \DateTimeImmutable $newStartTime, \DateTimeImmutable $newEndTime): bool
    {
        $busyIntervals = $this->appointmentRepository->getAppointmentsIntervalByDate($date);
        /** @var array $busyInterval */
        foreach ($busyIntervals as $busyInterval) {
            $busyStartTime = $busyInterval['startTime'];
            $busyEndTime = $busyInterval['endTime'];
            if ($newStartTime >= $busyStartTime && $newStartTime < $busyEndTime) {
                return true;
            }
            if ($newEndTime >= $busyStartTime && $newEndTime < $busyEndTime) {
                return true;
            }
        }
        return false;
    }
}