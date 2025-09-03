<?php

namespace App\Builder;

class BookingDirector
{
    public function build(BookingBuilderInterface $builder, array $journey, array $passengers, array $selectedSeats = []): void
    {
        $builder->setJourney($journey)
                ->setPassengers($passengers)
                ->applyDiscounts()
                ->selectSeats($selectedSeats)  // Skippable for other service from ETS
                ->calculateTotalPrice()
                ->createBooking()
                ->createPassengersAndTickets()
                ->updateSeatAvailability();
    }
}