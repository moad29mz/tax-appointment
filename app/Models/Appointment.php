<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Appointment extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'cin',
        'phone',
        'appointment_type',
        'appointment_date',
        'appointment_time',
        'status',
        'notes'
    ];

    protected $casts = [
        'appointment_date' => 'date',
    ];
}