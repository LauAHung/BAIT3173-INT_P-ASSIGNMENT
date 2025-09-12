<?php

namespace App\Builder;

class BookingDirector
{
    public function build(
        BookingBuilderInterface $builder,
        array $journey,
        array $passengers,
        int $userId,
        array $selectedSeats = [],
        array $journey2 = [],
        array $selectedSeats2 = []
    ): void {
        $builder->setJourney($journey);
        if (!empty($journey2)) {
            $builder->setReturnJourney($journey2);
        }
        $builder->setPassengers($passengers)
                ->applyDiscounts()
                ->selectSeats($selectedSeats);
        if (!empty($journey2)) {
            $builder->selectReturnSeats($selectedSeats2);
        }
        $builder->calculateTotalPrice()
                ->createBooking()
                ->createPassengersAndTickets()
                ->updateSeatAvailability();
    }
}