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
            ->whereDate('start_date', '>=', $fromDate)
            ->whereDate('start_date', '<=', $toDate)
            ->whereDate('end_date', '<=', $toDate)
            ->whereDate('end_date', '>=', $fromDate)
            ->where('status','1')
            ->orderBy('start_date');

        //  FILTER BY 01.01.2024
        //  30.12.2023 -- start_date
        //  02.01.2024 -- end_date
        //
        //  01.01.2024 -- start_date -- 09:00:00
        //  01.01.2024 -- end_date -- 14:00:00
    }

    public function carBookingStartData($fromDate)
    {
        return $this->hasMany(Booking::class, 'car_id', 'car_id')
            ->where('start_data', '>=', $fromDate)
            ->orderBy('start_date');
    }

    public function getCarById($id){
        return $this->where('car_id', $id)->where('company_id', 1)->where('status', 1)->where('is_deleted', '!=', 1)->first();
    }

    public function getDataCar(){
        return $this->where('company_id', 1)->where('status', 1)->where('is_deleted', '!=', 1)->get();
    }
}

