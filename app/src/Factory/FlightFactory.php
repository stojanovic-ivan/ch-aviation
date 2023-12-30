<?php

namespace App\Factory;

use App\Domain\Airline;
use App\Domain\Airplane;
use App\Domain\Flight;
use App\Infrastructure\AirlineLookup;

class FlightFactory
{

    /**
     * Create a Flight object from provided JSON data
     *
     * @param string $json
     * @return Flight
     */
    public static function create(string $json): Flight
    {
        // prepare the data
        $data = json_decode($json, true);

        // validation of data
        $reqFields = [
            'registration',
            'from',
            'to',
            'scheduled_start',
            'scheduled_end',
            'actual_start',
            'actual_end'
        ];
        foreach ($reqFields as $field) {
            if (!isset($data[$field])) {
                throw new \InvalidArgumentException("Field '$field' is missing in JSON data");
            }
        }

        $airline = new Airline(AirlineLookup::from($data['registration']));
        $airplane = new Airplane($data['registration'], $airline);

        // creating and returning of Flight object
        return new Flight(
            $airplane,
            $data['from'],
            $data['to'],
            new \DateTimeImmutable($data['scheduled_start']),
            new \DateTimeImmutable($data['scheduled_end']),
            new \DateTimeImmutable($data['actual_start']),
            new \DateTimeImmutable($data['actual_end'])
        );
    }
}
