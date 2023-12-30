<?php

namespace App\Tests\Factory;

use App\Domain\Airplane;
use App\Domain\Airline;
use App\Domain\Flight;
use App\Factory\FlightFactory;
use App\Infrastructure\AirlineLookup;
use PHPUnit\Framework\TestCase;

class FlightFactoryTest  extends TestCase
{

    public function testCreateFlightFromValidJson()
    {
        $json = '{
                "registration": "OO-AAC",
                "from": "STN",
                "to": "BER",
                "scheduled_start": "2021-12-17T10:00:00+00:00",
                "scheduled_end": "2021-12-17T11:30:00+00:00",
                "actual_start": "2021-12-17T10:12:00+00:00",
                "actual_end": "2021-12-17T11:33:00+00:00"
        }';

        $flight = FlightFactory::create($json);
        $airline = new Airline(AirlineLookup::from('OO-AAC'));
        $airplane = new Airplane('OO-AAC', $airline);

        $this->assertInstanceOf(Flight::class, $flight);
        $this->assertEquals($airplane, $flight->getAirplane());
        $this->assertEquals('STN', $flight->getFrom());
        $this->assertEquals('BER', $flight->getTo());
        $this->assertEquals('2021-12-17T10:00:00+00:00', $flight->getScheduledStart()->format('Y-m-d\TH:i:sP'));
        $this->assertEquals('2021-12-17T11:30:00+00:00', $flight->getScheduledEnd()->format('Y-m-d\TH:i:sP'));
        $this->assertEquals('2021-12-17T10:12:00+00:00', $flight->getActualStart()->format('Y-m-d\TH:i:sP'));
        $this->assertEquals('2021-12-17T11:33:00+00:00', $flight->getActualEnd()->format('Y-m-d\TH:i:sP'));
    }

    public function testCreateFlightFromInvalidJson()
    {
        // Test with missing 'registration' field
        $json = '{
            "from": "STN",
            "to": "BER",
            "scheduled_start": "2021-12-17T10:00:00+00:00",
            "scheduled_end": "2021-12-17T11:30:00+00:00",
            "actual_start": "2021-12-17T10:12:00+00:00",
            "actual_end": "2021-12-17T11:33:00+00:00"
        }';


        $this->expectException(\InvalidArgumentException::class);
        FlightFactory::create($json);
    }
}
