<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'Bookings';
    protected $primaryKey = 'BookingID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'BookingID', 'UserID', 'TrainID', 'JourneyID', 'BookingType', 'PaymentType', 'TicketNo','Price', 'Status', 'Created_at',
    ];

    protected $casts = [
        'Created_at' => 'datetime',
    ];

    public function journey()
    {
        return $this->belongsTo(Journey::class, 'JourneyID', 'JourneyID');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'UserID', 'user_id');
    }
}