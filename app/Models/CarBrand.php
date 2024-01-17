<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarBrand extends Model
{
    use HasFactory;

    protected $table = "rc_cars_brands_translations";
    protected $fillable = [
        'car_brand_id',
        'name'
    ];

    public function carBrand()
    {
        return $this->belongsTo(CarModel::class, 'car_brand_id', 'car_brand_id');
    }
}
