<?php

namespace App\Builder;

use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

class ConcreteBookingBuilder implements BookingBuilderInterface
{
    private array $journey;
    private array $passengers;
    private array $selectedSeats = [];
    private array $ticketPrices = [];  // Store per-passenger prices after discounts
    private float $totalPrice = 0.0;
    private ?Booking $booking = null;

    public function setJourney(array $journey): self
    {
        $this->journey = $journey;
        return $this;
    }

    public function setPassengers(array $passengers): self
    {
        // Re-index passengers to ensure 0-based array
        $this->passengers = array_values($passengers);
        return $this;
    }

    public function applyDiscounts(): self
    {
        $basePrice = $this->journey['price'] ?? Journey::find($this->journey['id'])->Price;
        foreach ($this->passengers as $index => $passenger) {
            $ticketPrice = $basePrice;
            if ($passenger['ticket_type'] === 'Kanak-kanak/Child') {
                $ticketPrice *= 0.9;
            } elseif ($passenger['ticket_type'] === 'OKU') {
                $ticketPrice *= 0.7;
            }
            $this->ticketPrices[$index] = $ticketPrice;
        }
        return $this;
    }

    public function selectSeats(array $selectedSeats): self
    {
        if ($this->journey['train_service'] !== 'ETS') {
            return $this;  // Skip for non-ETS
        }

        $this->selectedSeats = $selectedSeats;

        // Validate seat count
        if (count($this->selectedSeats) !== count($this->passengers)) {
            throw new Exception('Seat count must match passenger count.');
        }

        // Check for unavailable seats (is_available = 'N')
        $unavailableCount = Seat::where('JourneyID', $this->journey['id'])
            ->whereIn('SeatNo', $this->selectedSeats)
            ->where('is_available', 'N')
            ->count();

        if ($unavailableCount > 0) {
            throw new Exception('One or more seats are unavailable.');
        }

        return $this;
    }

    public function calculateTotalPrice(): self
    {
        $this->totalPrice = array_sum($this->ticketPrices);
        return $this;
    }

    public function createBooking(): self
    {
        $this->booking = Booking::create([
            'BookingID' => 'BK' . str_pad(mt_rand(5, 99999), 5, '0', STR_PAD_LEFT),
            'UserID' => (string) Auth::id(),
            'TrainID' => $this->journey['train_id'] ?? Journey::find($this->journey['id'])->TrainID,
            'JourneyID' => $this->journey['id'],
            'BookingType' => 'OneWay',
            'PaymentType' => null,
            'TicketNo' => count($this->passengers),
            'Price' => $this->totalPrice,
            'Status' => 'Pending',
            'Created_at' => now()->format('d-m-Y'),
        ]);
        return $this;
    }

    public function createPassengersAndTickets(): self
    {
        if (!$this->booking) {
            throw new Exception('Booking must be created first.');
        }

        foreach ($this->passengers as $index => $passenger) {
            // Create Passenger
            $passengerModel = Passenger::create([
                'PassengerID' => 'PS' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'BookingID' => $this->booking->BookingID,
                'Name' => $passenger['name'],
                'Gender' => $passenger['gender'],
                'ICno' => $passenger['mykad'] ?? null,
                'Passportno' => $passenger['passport'] ?? null,
                'PassportExpiryDate' => $passenger['passport_expiry'] ?? null,
                'Contactno' => $passenger['contact_no'],
                'TicketType' => $passenger['ticket_type'],
                'Created_at' => now()->format('d-m-Y'),
            ]);

            // Create new seat record if ETS
            $seatId = null;
            if ($this->journey['train_service'] === 'ETS' && isset($this->selectedSeats[$index])) {
                $seatNo = $this->selectedSeats[$index];
                // Create new seat as unavailable
                $seat = Seat::create([
                    'SeatID' => 'SE' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    'TrainID' => $this->journey['train_id'] ?? Journey::find($this->journey['id'])->TrainID,
                    'JourneyID' => $this->journey['id'],
                    'SeatNo' => $seatNo,
                    'is_available' => 'N',
                    'status' => 'Booked',
                    'Created_at' => now()->format('Y-m-d H:i:s'),
                ]);
                $seatId = $seat->SeatID;
            }

            // Create Ticket
            Ticket::create([
                'TicketID' => 'TK' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'BookingID' => $this->booking->BookingID,
                'JourneyID' => $this->journey['id'],
                'SeatID' => $seatId,
                'PassengerID' => $passengerModel->PassengerID,
                'Status' => 'Pending',
                'Price' => $this->ticketPrices[$index],
                'Created_at' => now()->format('d-m-Y'),
            ]);
        }
        return $this;
    }

    public function updateSeatAvailability(): self
    {
        if ($this->journey['train_service'] === 'ETS') {
        $journeyModel = Journey::find($this->journey['id']);
        $journeyModel->decrement('SeatAvailable', count($this->passengers));
        }
        return $this;
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }
}