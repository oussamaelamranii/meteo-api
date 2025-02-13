<?php

namespace App\Command;

use App\Service\CacheService;
use App\Service\WeatherService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:update-weather-cache',
    description: 'Fetch weather data and store it in cache',
)]
class FetchWeatherDataCommand extends Command
{
    private CacheService $cacheService;
    public function __construct(CacheService $cacheService)
    {
        parent::__construct();
        $this->cacheService = $cacheService;
    }

    protected function configure(): void
    {
        $this
            ->setName('app:update-weather-cache')
            ->setDescription('This command allows the program to fetch weather data every 12 hours')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $this->cacheService->storeWeatherInCache();
        $io->success('Weather data cache successfully updated Mr ayman!');

        return Command::SUCCESS;
    }
}
