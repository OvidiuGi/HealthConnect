<?php

namespace App\Command;

use App\Repository\ScheduleRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class DeletePastDueDateSchedulesCommand extends Command
{
    protected static $defaultName = 'app:delete-past-due-date-schedules';

    protected static $defaultDescription = 'Delete past due date schedules as past due date';

    public function __construct(private ScheduleRepository $scheduleRepository)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $schedules = $this->scheduleRepository->findAll();

        foreach ($schedules as $schedule) {
            if ($schedule->getEndDate() < new \DateTimeImmutable()) {
                $this->scheduleRepository->delete($schedule);
            }
        }

        $io->success('You have a new command! Now make it your own! Pass --help to see your options.');

        return Command::SUCCESS;
    }
}