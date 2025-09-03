<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Seat extends Model
{
    protected $table = 'Seats';
    protected $primaryKey = 'SeatID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'SeatID', 'TrainID', 'JourneyID', 'SeatNo', 'is_available','status', 'Created_at',
    ];

    public function journey()
    {
        return $this->belongsTo(Journey::class, 'JourneyID', 'JourneyID');
    }
}