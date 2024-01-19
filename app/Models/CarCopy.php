<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarCopy extends Model
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

    public function carBooking($startDate, $endDate)
    {
        return $this->hasMany(Booking::class, 'car_id', 'car_id')
            //  booking started before this period and ended after the period
            ->where(function($query) use ($startDate, $endDate) {
                $query->where('start_date', '>=', $startDate)
                    ->where('end_date', '<=', $endDate)
                    ->orWhere(function($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    })
                    ->orWhere(function($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $startDate)
                            ->where('end_date', '<=', $endDate);
                    })
                    ->orWhere(function($query) use ($startDate, $endDate) {
                        $query->where('start_date', '>=', $startDate)
                            ->where('start_date', '<=', $endDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->where('status', '1')->orderBy('start_date', 'asc')->get();
    }

    public function getDataCar()
    {
        return $this->where('company_id', 1)->where('status', 1)->where('is_deleted', '!=', 1)->get();
    }
}

