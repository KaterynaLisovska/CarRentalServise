<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Car extends Model
{
    use HasFactory;

    protected $table = "rc_cars";
    protected $fillable = [
        'car_id',
        'registration_number',
        'car_model_id',
        'attribute_year',
        'company_id'
    ];

    public function carModel()
    {
        return $this->hasOne(CarModel::class, 'car_model_id', 'car_model_id');
    }

    public function carBooking($fromDate,$toDate)
    {
        return $this->hasMany(Booking::class, 'car_id', 'car_id')
            ->whereDate('end_date', '<=', $toDate->format('y-m').'-31')  // Assuming 'end_date' is a date field
            ->whereDate('start_date', '>=', $toDate->format('y-m').'2019-05-31')  // Assuming 'end_date' is a date field
            ->where('status','1')
            ->orderBy('start_date');
    }

    public function carBookingStartData($fromDate)
    {
        return $this->hasMany(Booking::class, 'car_id', 'car_id')
            ->where('start_data', '>=', $fromDate)
            ->orderBy('start_date');
    }

    public function getDataCar(){
        return $this->where('company_id', 1)->where('status', 1)->where('is_deleted', '!=', 1)->get();
    }
}

