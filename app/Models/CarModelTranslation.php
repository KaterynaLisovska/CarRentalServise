<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CarModelTranslation extends Model
{
    use HasFactory;

    protected $table = "rc_cars_models_translations";
    protected $fillable = [
        'car_model_id',
        'name'
    ];

    public function carModel()
    {
        return $this->belongsTo(CarModel::class, 'car_model_id', 'car_model_id');
    }
}
