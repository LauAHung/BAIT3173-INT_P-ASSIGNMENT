<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable; 
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory;

    protected $primaryKey = 'user_id';

    public $timestamps = false;

    protected $fillable = ['first_name', 'last_name', 'email', 'password'];

    protected $hidden = ['password', 'remember_token'];

    public static function createNewUser($data)
    {
        return self::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'password'   => bcrypt($data['password']),
        ]);
    }
}
