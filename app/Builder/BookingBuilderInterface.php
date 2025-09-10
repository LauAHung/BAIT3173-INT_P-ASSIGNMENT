<?php

namespace App\Builder;

use App\Models\Booking;

interface BookingBuilderInterface
{
    public function setJourney(array $journey): self;  // Step: Set journey details
    public function setReturnJourney(array $journey2): self;  // Optional: Set return journey details
    public function setPassengers(array $passengers): self;  // Step: Add passengers
    public function applyDiscounts(): self;  // Step: Calculate discounts per passenger
    public function selectSeats(array $selectedSeats): self;  // Step: For ETS only (outbound)
    public function selectReturnSeats(array $selectedSeats2): self;  // Optional: For ETS only (return)
    public function calculateTotalPrice(): self;  // Step: Compute total price
    public function createBooking(): self;  // Step: Create the Booking model
    public function createPassengersAndTickets(): self;  // Step: Create related models
    public function updateSeatAvailability(): self;  // Step: Update seats and journey availability
    public function getBooking(): Booking;  // Final: Return the built Booking
}