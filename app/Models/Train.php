<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Train extends Model
{
    protected $table = 'Trains';
    protected $primaryKey = 'TrainID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'TrainID',
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