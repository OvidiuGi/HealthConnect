<?php

declare(strict_types=1);

namespace App\Command;

use App\Message\AppointmentReminderNotification;
use App\Repository\AppointmentRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Messenger\MessageBusInterface;

class SendEmailReminderCommand extends Command
{
    protected static $defaultName = 'app:send-email-reminder';

    protected static $defaultDescription = 'Send email reminder for upcoming appointments';

    public function __construct(
        private readonly AppointmentRepository $appointmentRepository,
        private readonly MessageBusInterface $bus
    ) {
        parent::__construct();
    }

    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $appointments = $this->appointmentRepository->findBy(['isCompleted' => false]);

        foreach ($appointments as $appointment) {
            if ($appointment->getDate() == new \DateTimeImmutable('+1 day')) {
                $this->bus->dispatch(new AppointmentReminderNotification($appointment, 24));
            }

            if ($appointment->getDate()->modify('+3 hours') == new \DateTimeImmutable()) {
                $this->bus->dispatch(new AppointmentReminderNotification($appointment, 3));
            }
        }

        $io->success('Reminders sent successfully!');

        return Command::SUCCESS;
    }
}
