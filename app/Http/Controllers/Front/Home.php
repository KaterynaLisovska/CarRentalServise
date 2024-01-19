<?php

namespace App\Http\Controllers\Front;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class Home extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;


    private function getCarFreeDays($carBooking, $item, $startDate, $endDate)
    {
        if (count($carBooking) >= 1 ) {
            $days = 0;
            $lastDay = null;
            $startTime = null;
            $endTime = null;
            $lastStatus = null;
            foreach ($carBooking as $index => $item) {
                if ($item->end_date >= $startDate) {
                    if($item['car_id'] == 3715){
                        var_dump($days);
//                        dd($carBooking[$index+1]);
//                        dd($carBooking[$index]);
//                        dd($carBooking[$index-1]);
                    }
                    if ($item->start_date <= $startDate && $item->end_date >= $endDate) {
                        return 0;

                    } elseif ($item->start_date <= $startDate && $item->end_date <= $endDate) {
                        $value = $startDate->diffInDays($item->start_date);
                        $itemEndDate = Carbon::createFromDate($item->end_date)->setHour(12)->setSecond(1);

                        $lastDay = $item->end_date;
                        if ($item->end_date >= $itemEndDate) {
                            $lastStatus = "rent";
                            continue;
                        } else {
                            $lastStatus = "free";
                        }
                    } elseif ($item->start_date <= $endDate) {

                        if ($lastStatus == null) {
                            $startDate = Carbon::parse($startDate);
                            $itemStartDate = Carbon::createFromDate($item->start_date);
                            $count = $itemStartDate->day - $startDate->day;
                            $days = $count;
                        } else {
                            $lastDay = Carbon::parse($lastDay);
                            $itemStartDate = Carbon::createFromDate($item->start_date);
                            $count = $itemStartDate->day - $lastDay->day;
                            if($count != 0){
                            if ($lastStatus != 'free') {
                                $days += $count - 1;
                            } else {
                                $days += $count;
                            }
                            }
                        }


                        $lastDay = Carbon::parse($lastDay);
                        $itemStartDate = Carbon::createFromDate($item->start_date);
                        if($lastDay->day == $itemStartDate->day && ($lastDay->hour <= 12 && $itemStartDate->hour >= 17)) {
                            $lastStatus = "free";
                        }
                        else{
                            if ($item->start_date >= $itemStartDate->setHour(17)->startOfHour()) {
                                $days++;
                                $lastStatus = "free";
                            } else {
                                $lastStatus = "rent";
                            }
                        }



                        if ($item->end_date <= $endDate) {
                            $itemEndDate = Carbon::createFromDate($item->end_date)->setHour(12)->setSecond(1);
                            $lastDay = $item->end_date;
                            if ($item->end_date >= $itemEndDate) {
                                $lastStatus = "rent";
                                continue;
                            } else {

                                $lastStatus = "free";
                            }
                        } else {

                            return $days;
                        }

                    } else {

                        $lastDay = Carbon::parse($lastDay);
                        $itemStartDate = Carbon::createFromDate($item->start_date);

                        if($lastDay->day == $itemStartDate->day && ($lastDay->hour <= 12 && $itemStartDate->hour >= 17)) {
                            $lastStatus = "free";
                            $days++;
                        }
                        else{
                            if ($item->start_date >= $itemStartDate->setHour(17)->startOfHour()) {
                                $days++;
                                $lastStatus = "free";
                            } else {
                                $lastStatus = "rent";
                            }
                        }
                        if ($lastDay != null) {
                            $count = Carbon::createFromDate($endDate)->diffInDays($lastDay);
                                $days += $count;
                        }
                        else{
                            return $startDate->diffInDays($endDate) + 1;
                        }
                        break;
                    }
                }
            }
            return $days;
        }
    }

    public function index()
    {
        $cars = (new \App\Models\Car)->getDataCar();


        $year = 2023;
        $month = 1;
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth();
        $startDateSearch = Carbon::createFromDate($year - 1, $month)->startOfMonth();
        $endDateSearch = Carbon::createFromDate($year + 1, $month)->endOfMonth()->endOfDay();

        foreach ($cars as $item) {
            $carModel = $item->carModel;
            $carBrand = $carModel->carBrand->first() ?? '';
            $carModelTranslation = $carModel->carModelTranslation->first();
            $item->color = $carModel->attribute_interior_color;
            $item->year = $item->attribute_year;
            $item->brand = $carBrand->name;
            $item->name = $carModelTranslation->name ?? '';
            $carBooking = $item->carBooking($startDateSearch, $endDateSearch)->get();
            $days = 0;
            $item->availableDays = $this->getCarFreeDays($carBooking, $item, $startDate, $endDate);
            $item->days = $startDate->diffInDays($endDate) + 1;
        }

        return view('front.cars.index', ['cars' => $cars]);

    }

    public function filter(Request $request)
    {
        $year = $request->input('years');
        $month = $request->input('months');


//        $html = View::make('front.cars.list', ['cars' => $cars])->render();

        $request->validate([
            'years' => 'required|numeric',
            'months' => 'required|numeric|between:1,12',
        ]);


        return response()->json(['html'=>$html]);
    }

//    private function rentCars($cars)
//    {
//        $cars->filter(function ($carBooking) {
//            $startFrom = $carBooking->$startDate;
//            $endTo = $carBooking->$endDate;
//
//            $start = Carbon::parse($carBooking->start_date);
//            $end = Carbon::parse($carBooking->end_date);
//            $rent_time = $end->diff($start);
//            $hours = $rent_time->h;
//
//            $start_time = $start->format('H:i:s');
//            $end_time = $end->format('H:i:s');
//
//            // Check if the car has remained free for more than 9 hours between 9 a.m. and 9 p.m.
//            return $hours > 9 && $start_time >= '09:00:00' && $end_time <= '21:00:00';
//        });
//    }
}
