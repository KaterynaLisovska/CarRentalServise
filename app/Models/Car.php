<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

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

//    public function carBrand()
//    {
//        return $this->hasOne(CarBrand::class,'car_brand_id','car_model_id');
//    }

    public function getDataCar(){
        $cars = $this->where('company_id', 1)->where('status', 1)->where('is_deleted', '!=', 1)->get();

        foreach ($cars as $item) {
            $carModel = $item->carModel;
            $carBrand = $carModel->carBrand->first() ?? '';
            $carModelTranslation = $carModel->carModelTranslation->first();
            $item->color = $carModel->attribute_interior_color;
            $item->year = $item->attribute_year;
            $item->brand = $carBrand->name;
            $item->name = $carModelTranslation->name ?? '';
        }

            return $cars;
    }
    public function carBooking($year, $month)
    {
        $startDateSearch = Carbon::createFromDate($year - 1, $month)->startOfMonth();
        $endDateSearch = Carbon::createFromDate($year + 1, $month)->endOfMonth()->endOfDay();

        return $this->hasMany(Booking::class, 'car_id', 'car_id')
            ->whereDate('end_date', '<=', $endDateSearch)
            ->whereDate('start_date', '>=', $startDateSearch)
            ->where('status','1')
            ->orderBy('start_date');
    }

    public function carBookingStartData($fromDate)
    {
        return $this->hasMany(Booking::class, 'car_id', 'car_id')
            ->where('start_data', '>=', $fromDate)
            ->orderBy('start_date');
    }
}

