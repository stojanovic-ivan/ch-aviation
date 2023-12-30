<?php

namespace App\Infrastructure;

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
// use App\Infrastructure\FlightParser;

#[AsCommand(name: 'parse')]
class ParseCommand extends Command
{
    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        // Prepare the data
        $fileContent = file_get_contents(dirname(__DIR__) . '/../var/input.jsonl');
        $lines = explode("\n", trim($fileContent));
        $flights = array_map('json_decode', $lines);

        // Analyze the data
        $output->writeln("\nSearching for three longest flights...");
        FlightParser::getThreeLongestFlights($flights);
        $output->writeln("==================================================================");

        $output->writeln("\nSearching airline with most missed scheduled landings...");
        FlightParser::getMostMissedLandings($flights);
        $output->writeln("==================================================================");

        $output->writeln("\nSearching for destination with most overnight stays...");
        FlightParser::getMostOvernightStaysDestination($flights);
        $output->writeln("==================================================================");

        return Command::SUCCESS;
    }
}
