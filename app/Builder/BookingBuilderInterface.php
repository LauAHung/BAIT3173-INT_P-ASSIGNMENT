<?php

namespace App\Builder;

use App\Models\Booking;

interface BookingBuilderInterface
{
    public function setUserId(int $userId): self; // Add this to enforce user ID setting
    public function setJourney(array $journey): self;
    public function setReturnJourney(array $journey2): self;
    public function setPassengers(array $passengers): self;
    public function applyDiscounts(): self;
    public function selectSeats(array $selectedSeats): self;
    public function selectReturnSeats(array $selectedSeats2): self;
    public function calculateTotalPrice(): self;
    public function createBooking(): self;
    public function createPassengersAndTickets(): self;
    public function updateSeatAvailability(): self;
    public function getBooking(): Booking;
}