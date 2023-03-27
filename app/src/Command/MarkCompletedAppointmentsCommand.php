<?php

namespace App\Command;

use App\Repository\AppointmentRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class MarkCompletedAppointmentsCommand extends Command
{
    protected static $defaultName = 'app:mark-completed-appointments';

    protected static $defaultDescription = 'Marks completed appointments as completed';

    public function __construct(private AppointmentRepository $appointmentRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        $appointments = $this->appointmentRepository->findAll();

        foreach ($appointments as $appointment) {
            if ($appointment->getDate() < new \DateTimeImmutable()) {
                $appointment->isCompleted = true;
                $this->appointmentRepository->save($appointment);
            }
        }

        $io->success('Appointments marked as completed successfully!');

        return Command::SUCCESS;
    }
}