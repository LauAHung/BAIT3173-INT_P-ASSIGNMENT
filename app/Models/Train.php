<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    protected $table = 'Trains';
    protected $primaryKey = 'TrainID';
    protected $fillable = [
        'StationID',
        'TrainNo',
        'TrainService',
        'SeatCount',
        'Is_available',
        'Created_at'
    ];

    public function journeys()
    {
        return $this->hasMany(Journey::class, 'TrainID', 'TrainID');
    }

    public function station()
    {
        return $this->belongsTo(Station::class, 'StationID', 'StationID');
    }
}