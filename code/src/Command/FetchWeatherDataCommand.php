<?php

namespace App\Command;

use App\Service\WeatherService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'FetchWeatherDataCommand',
    description: 'Add a short description for your command',
)]
class FetchWeatherDataCommand extends Command
{
    private WeatherService $weatherService;
    public function __construct(WeatherService $weatherService)
    {
        parent::__construct();
        $this->weatherService = $weatherService;
    }

    protected function configure(): void
    {
        $this
            ->setName('FetchWeatherDataCommand')
            ->setDescription('This command allows the program to fetch weather data every 12 hours')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->weatherService->storeWeatherInCache();
        $io->success('Weather data cache successfully updated Mr ayman!');

        return Command::SUCCESS;
    }
}
