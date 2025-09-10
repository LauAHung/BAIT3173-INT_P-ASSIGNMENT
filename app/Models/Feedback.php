<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Feedback extends Model
{
    protected $table = 'Feedback';

    protected $primaryKey = 'feeback_id';

    public $incrementing = false;
    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'feeback_id',
        'BookingID',   // ✅ fixed casing
        'feedback_time',
        'rating_value',
        'feedback_text',
    ];
}
