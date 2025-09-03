<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Station extends Model
{
    protected $table = 'Stations';
    protected $primaryKey = 'StationID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;
    
    protected $fillable = [
        'StationID',
        'StationName',
        'Location',
        'Is_active',
        'Created_at'
    ];

    public function trains()
    {
        return $this->hasMany(Train::class, 'StationID', 'StationID');
    }
}
