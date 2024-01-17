<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModel extends Model
{
    use HasFactory;

    protected $table = "rc_cars_models";
    protected $fillable = [
        'car_model_id',
        'car_brand_id'
    ];

    public function carBrand()
    {
        return $this->hasMany(CarBrand::class, 'car_brand_id', 'car_brand_id')->where('lang', 'en');
    }

    public function carModelTranslation()
    {
        return $this->hasMany(CarModelTranslation::class, 'car_model_id', 'car_model_id')->where('lang', 'en');
    }
}
