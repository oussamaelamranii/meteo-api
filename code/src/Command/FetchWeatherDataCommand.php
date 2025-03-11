<?php

namespace App\Command;

use App\Service\CacheService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Constraints\DateTime;

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

    /*protected function configure(): void
    {
        $this
            ->setName('app:update-weather-cache')
            ->setDescription('This command allows the program to fetch weather data every 12 hours')
        ;
    }*/

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        try {

            $this->cacheService->storeWeatherInCache();

            $date = new \DateTimeImmutable();
            $io->success(sprintf('Weather data cache successfully updated Mr ayman! on: %s', $date->format('Y-m-d H:i:s')));
            //$io->success("Weather data cache successfully updated Mr ayman!");
            return Command::SUCCESS;
        }catch (\Exception $exception){
            $io->error("Weather data cache update failed Mr ayman!" . $exception->getMessage());
            return Command::FAILURE;
        }
    }
}