<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Passenger extends Model
{
    protected $table = 'Passengers';
    protected $primaryKey = 'PassengerID';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'PassengerID', 'Name', 'Gender', 'ICno', 'Passportno', 'PassportExpiryDate', 'TicketType', 'Created_at',
    ];

    protected $casts = [
        'PassportExpiryDate' => 'datetime',
        'Created_at' => 'datetime',
    ];
}