<?php

namespace App\Builder;

use App\Models\Booking;
use App\Models\Passenger;
use App\Models\Ticket;
use App\Models\Seat;
use App\Models\Journey;
use Illuminate\Support\Facades\Auth;
use Exception;

class ConcreteBookingBuilder implements BookingBuilderInterface
{
    private array $journey;
    private array $returnJourney = [];
    private array $passengers;
    private array $selectedSeats = [];
    private array $returnSelectedSeats = [];
    private array $ticketPrices = [];
    private array $returnTicketPrices = [];
    private array $passengerModels = [];
    private float $totalPrice = 0.0;
    private ?Booking $booking = null;
    private string $bookingType = 'OneWay';
    private ?int $userId = null;

    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }
    public function setJourney(array $journey): self
    {
        $this->journey = $journey;
        $this->bookingType = $journey['booking_type'] ?? 'OneWay';
        return $this;
    }

    public function setReturnJourney(array $journey2): self
    {
        $this->returnJourney = $journey2;
        $this->bookingType = 'Return';
        // validate reverse route for return use
        if ($this->journey['to_location'] !== $journey2['from_location'] || 
            $this->journey['from_location'] !== $journey2['to_location']) {
            throw new Exception('Return journey must be the reverse route of the outbound journey.');
        }
        return $this;
    }

    public function setPassengers(array $passengers): self
    {
        $this->passengers = array_values($passengers);
        return $this;
    }

    public function applyDiscounts(): self
    {
        // always fetch base price from DB not array
        $journeyModel = Journey::findOrFail($this->journey['id']); // use findorfail for existence check
        $basePrice = $journeyModel->Price;

        foreach ($this->passengers as $index => $passenger) {
            $ticketPrice = $basePrice;
            if ($passenger['ticket_type'] === 'Pelajar/Student') {
                $ticketPrice *= 0.9;
            } elseif ($passenger['ticket_type'] === 'Warga Emas/Senior Citizen') {
                $ticketPrice *= 0.8;
            } elseif ($passenger['ticket_type'] === 'OKU') {
                $ticketPrice *= 0.7;
            }
            $this->ticketPrices[$index] = $ticketPrice;
        }

        if (!empty($this->returnJourney)) {
            $returnBasePrice = $this->returnJourney['price'] ?? Journey::find($this->returnJourney['id'])->Price;
            foreach ($this->passengers as $index => $passenger) {
                $returnTicketPrice = $returnBasePrice;
                if ($passenger['ticket_type'] === 'Pelajar/Student') {
                    $returnTicketPrice *= 0.9;
                } elseif ($passenger['ticket_type'] === 'Warga Emas/Senior Citizen') {
                    $returnTicketPrice *= 0.8;
                } elseif ($passenger['ticket_type'] === 'OKU') {
                    $returnTicketPrice *= 0.7;
                } 
                $this->returnTicketPrices[$index] = $returnTicketPrice;
            }
        }
        return $this;
    }

    public function selectSeats(array $selectedSeats): self
    {
        if ($this->journey['train_service'] !== 'ETS') {
            return $this;
        }

        $this->selectedSeats = $selectedSeats;

        if (count($this->selectedSeats) !== count($this->passengers)) {
            throw new Exception('Seat count must match passenger count.');
        }

        $unavailableCount = Seat::where('JourneyID', $this->journey['id'])
            ->whereIn('SeatNo', $this->selectedSeats)
            ->where('is_available', 'N')
            ->lockForUpdate()  // lock rows until transaction commit
            ->count();

        if ($unavailableCount > 0) {
            throw new Exception('One or more seats are unavailable.');
        }

        return $this;
    }

    public function selectReturnSeats(array $selectedSeats2): self
    {
        if (empty($this->returnJourney) || $this->returnJourney['train_service'] !== 'ETS') {
            return $this;
        }

        $this->returnSelectedSeats = $selectedSeats2;

        if (count($this->returnSelectedSeats) !== count($this->passengers)) {
            throw new Exception('Return seat count must match passenger count.');
        }

        $unavailableCount = Seat::where('JourneyID', $this->returnJourney['id'])
            ->whereIn('SeatNo', $this->returnSelectedSeats)
            ->where('is_available', 'N')
            ->count();

        if ($unavailableCount > 0) {
            throw new Exception('One or more return seats are unavailable.');
        }

        return $this;
    }

    public function calculateTotalPrice(): self
    {
        $this->totalPrice = array_sum($this->ticketPrices);
        if (!empty($this->returnJourney)) {
            $this->totalPrice += array_sum($this->returnTicketPrices);
        }
        return $this;
    }

    public function createBooking(): self
    {
        $ticketCount = count($this->passengers) * (empty($this->returnJourney) ? 1 : 2);
        $this->booking = Booking::create([
            'BookingID' => 'BK' . str_pad(mt_rand(5, 99999), 5, '0', STR_PAD_LEFT),
            'UserID' => $this->userId ?? Auth::id(),
            'TrainID' => $this->journey['train_id'] ?? Journey::find($this->journey['id'])->TrainID,
            'JourneyID' => $this->journey['id'],
            'JourneyID2' => !empty($this->returnJourney) ? $this->returnJourney['id'] : null,
            'BookingType' => $this->bookingType,
            'PaymentType' => null,
            'TicketNo' => $ticketCount,
            'Price' => $this->totalPrice,
            'Status' => 'Pending',
            'Created_at' => now(),
        ]);
        return $this;
    }

    public function createPassengersAndTickets(): self
    {
        if (!$this->booking) {
            throw new Exception('Booking must be created first.');
        }

        foreach ($this->passengers as $index => $passenger) {
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
                'Created_at' => now(),
            ]);
            $this->passengerModels[$index] = $passengerModel;

            $seatId = null;
            if ($this->journey['train_service'] === 'ETS' && isset($this->selectedSeats[$index])) {
                $seatNo = $this->selectedSeats[$index];
                $seat = Seat::where('JourneyID', $this->journey['id'])
                    ->where('SeatNo', $seatNo)
                    ->first();

                if ($seat) {
                    $seat->is_available = 'N';
                    $seat->status = 'Booked';
                    $seat->save();
                } else {
                    $seat = Seat::create([
                        'SeatID' => 'SE' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                        'TrainID' => $this->journey['train_id'] ?? Journey::find($this->journey['id'])->TrainID,
                        'JourneyID' => $this->journey['id'],
                        'SeatNo' => $seatNo,
                        'is_available' => 'N',
                        'status' => 'Booked',
                        'Created_at' => now(),
                    ]);
                }
                $seatId = $seat->SeatID;
            }

            Ticket::create([
                'TicketID' => 'TK' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                'BookingID' => $this->booking->BookingID,
                'JourneyID' => $this->journey['id'],
                'SeatID' => $seatId,
                'PassengerID' => $passengerModel->PassengerID,
                'Status' => 'Pending',
                'Price' => $this->ticketPrices[$index],
                'Created_at' => now(),
            ]);
        }

        if (!empty($this->returnJourney)) {
            foreach ($this->passengerModels as $index => $passengerModel) {
                $seatId = null;
                if ($this->returnJourney['train_service'] === 'ETS' && isset($this->returnSelectedSeats[$index])) {
                    $seatNo = $this->returnSelectedSeats[$index];
                    $seat = Seat::where('JourneyID', $this->returnJourney['id'])
                        ->where('SeatNo', $seatNo)
                        ->first();

                    if ($seat) {
                        $seat->is_available = 'N';
                        $seat->status = 'Booked';
                        $seat->save();
                    } else {
                        $seat = Seat::create([
                            'SeatID' => 'SE' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                            'TrainID' => $this->returnJourney['train_id'] ?? Journey::find($this->returnJourney['id'])->TrainID,
                            'JourneyID' => $this->returnJourney['id'],
                            'SeatNo' => $seatNo,
                            'is_available' => 'N',
                            'status' => 'Booked',
                            'Created_at' => now(),
                        ]);
                    }
                    $seatId = $seat->SeatID;
                }

                Ticket::create([
                    'TicketID' => 'TK' . str_pad(mt_rand(1, 99999), 5, '0', STR_PAD_LEFT),
                    'BookingID' => $this->booking->BookingID,
                    'JourneyID' => $this->returnJourney['id'],
                    'SeatID' => $seatId,
                    'PassengerID' => $passengerModel->PassengerID,
                    'Status' => 'Pending',
                    'Price' => $this->returnTicketPrices[$index],
                    'Created_at' => now(),
                ]);
            }
        }
        return $this;
    }

    public function updateSeatAvailability(): self
    {
        if ($this->journey['train_service'] === 'ETS') {
            Journey::find($this->journey['id'])->decrement('SeatAvailable', count($this->passengers));
        }
        if (!empty($this->returnJourney) && $this->returnJourney['train_service'] === 'ETS') {
            Journey::find($this->returnJourney['id'])->decrement('SeatAvailable', count($this->passengers));
        }
        return $this;
    }

    public function getBooking(): Booking
    {
        return $this->booking;
    }

    public function getTicketPrices(): array
    {
        return array_merge($this->ticketPrices, $this->returnTicketPrices);
    }

    public function getTotalPrice(): float
    {
        return $this->totalPrice;
    }

    public function getSelectedSeats(): array
    {
        return array_merge($this->selectedSeats, $this->returnSelectedSeats);
    }
}