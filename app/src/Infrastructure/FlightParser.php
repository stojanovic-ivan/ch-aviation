<?php

namespace App\Infrastructure;

class FlightParser
{

    /**
     * Get three longest flights by actual duration
     *
     * @param array $flights
     * @return void
     */
    public static function getThreeLongestFlights(array $flights): void
    {

        usort($flights, function ($a, $b) {
            $durationA = (new \DateTimeImmutable($a->actual_end))->getTimestamp() - (new \DateTimeImmutable($a->actual_start))->getTimestamp();
            $durationB = (new \DateTimeImmutable($b->actual_end))->getTimestamp() - (new \DateTimeImmutable($b->actual_start))->getTimestamp();
            return $durationB - $durationA;
        });

        $longestFlights = array_slice($flights, 0, 3);

        echo "\nThree longest flights by actual duration:\n";
        foreach ($longestFlights as $flight) {
            echo "   - Registration: {$flight->registration}, Duration: " . self::durationToHoursMinutes($flight->actual_start, $flight->actual_end) . "\n";
        }
    }

    /**
     * Get airline with most missed scheduled landings
     *
     * @param array $flights
     * @return void
     */
    public static function getMostMissedLandings(array $flights): void
    {

        $missedLandingsCount = [];

        foreach ($flights as $flight) {
            $scheduledEnd = new \DateTimeImmutable($flight->scheduled_end);
            $actualEnd = new \DateTimeImmutable($flight->actual_end);

            $difference = $actualEnd->getTimestamp() - $scheduledEnd->getTimestamp();

            // Threshold: 5 minutes (300 seconds)
            if ($difference > 300) {
                $airline = substr($flight->registration, 0, 2);
                $missedLandingsCount[$airline] = ($missedLandingsCount[$airline] ?? 0) + 1;
            }
        }

        arsort($missedLandingsCount);
        $airlineWithMostMissedLandings = key($missedLandingsCount);

        echo "\nAirline with most missed scheduled landings: $airlineWithMostMissedLandings\n";
    }

    /**
     * Get Destination with most overnight stays
     *
     * @param array $flights
     * @return void
     */
    public static function getMostOvernightStaysDestination(array $flights): void
    {
        $overnightStaysCount = [];

        foreach ($flights as $flight) {
            $scheduledEnd = new \DateTimeImmutable($flight->scheduled_end);
            $scheduledStart = new \DateTimeImmutable($flight->scheduled_start);

            $overnightStay = $scheduledEnd->format('Ymd') != $scheduledStart->format('Ymd');

            if ($overnightStay) {
                $destination = $flight->to;
                $overnightStaysCount[$destination] = ($overnightStaysCount[$destination] ?? 0) + 1;
            }
        }

        arsort($overnightStaysCount);
        $destinationWithMostOvernightStays = key($overnightStaysCount);

        echo "\nDestination with most overnight stays: $destinationWithMostOvernightStays\n";
    }

    /**
     * Helper function to calculate duration in hours and minutes
     *
     * @param string $start
     * @param string $end
     * @return string
     */
    private static function durationToHoursMinutes(string $start, string $end): string
    {
        $startDateTime = new \DateTime($start);
        $endDateTime = new \DateTime($end);

        $interval = $startDateTime->diff($endDateTime);

        $hours = $interval->h + $interval->days * 24;
        $minutes = $interval->i;

        return "$hours hours, $minutes minutes";
    }
}
