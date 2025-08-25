<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Journey extends Model
{
    protected $table = 'Journeys'; // Correct table name
    protected $primaryKey = 'JourneyID';
    public $incrementing = true;
    protected $keyType = 'string';
    public $timestamps = false;
    protected $fillable = [
        'TrainID',
        'FromLocation',
        'ToLocation',
        'DepartureTime',
        'ArrivalTime',
        'SeatAvailable',
        'Price',
        'Status',
        'Created_at'
    ];

    // Relationship with Train
    public function train()
    {
        return $this->belongsTo(Train::class, 'TrainID', 'TrainID');
    }

    // Relationship with Seats (optional, for seat availability)
    public function seats()
    {
        return $this->hasMany(Seat::class, 'JourneyID', 'JourneyID');
    }
}