<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    use HasFactory;

    protected $table = "rc_bookings";
    protected $fillable = [
        'booking_id',
        'car_id',
        'start_date',
        'end_date',
        'status'
    ];

    public function bookingCar()
    {
        return $this->belongsTo(Car::class, 'car_id', 'car_id');
    }
}
