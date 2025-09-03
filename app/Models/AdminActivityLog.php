<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdminActivityLog extends Model
{
    use HasFactory;

    protected $table = 'admin_activity_logs';

    public $timestamps = true;

    protected $fillable = [
        'admin_email',
        'action',
        'details',
    ];

    protected $casts = [
        'details' => 'array',
    ];
}


