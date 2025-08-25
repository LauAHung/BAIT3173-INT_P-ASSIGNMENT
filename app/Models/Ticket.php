<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    protected $table = 'Tickets';
    protected $primaryKey = 'TicketID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'TicketID', 'BookingID', 'JourneyID', 'SeatID', 'PassengerID', 'Status', 'Created_at',
    ];

    protected $casts = [
        'Created_at' => 'datetime',
    ];

    public function booking()
    {
        return $this->belongsTo(Booking::class, 'BookingID', 'BookingID');
    }

    public function journey()
    {
        return $this->belongsTo(Journey::class, 'JourneyID', 'JourneyID');
    }

    public function seat()
    {
        return $this->belongsTo(Seat::class, 'SeatID', 'SeatID');
    }

    public function passenger()
    {
        return $this->belongsTo(Passenger::class, 'PassengerID', 'PassengerID');
    }
}