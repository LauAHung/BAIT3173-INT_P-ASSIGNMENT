<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ConcessionApplication extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'application_id',
        'type',
        'full_name',
        'ic_number',
        'citizenship',
        'passport_number',
        'oku_card_number',
        'disability_info',
        'age',
        'gender',
        'date_of_birth',
        'matrix_number',
        'education_level',
        'school_name',
        'student_id_photo_path',
        'oku_card_photo_path',
        'senior_ic_photo_path',
        'status',
        'admin_notes',
        'reviewed_at',
        'reviewed_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'reviewed_at' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Generate unique application ID
    public static function generateApplicationId()
    {
        $lastApp = self::orderBy('id', 'desc')->first();
        $nextNumber = $lastApp ? (intval(substr($lastApp->application_id, 3)) + 1) : 1;
        return 'APP' . str_pad($nextNumber, 3, '0', STR_PAD_LEFT);
    }

    // Relationship with user who submitted the application
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with reviewer (admin user)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // Scope for pending applications
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    // Scope for approved applications
    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    // Scope for rejected applications
    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }
}
