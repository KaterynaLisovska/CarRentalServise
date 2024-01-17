<?php

namespace App\Http\Controllers\Front;

use App\Models\Car;
use Illuminate\Foundation\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class Home extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $cars = (new Car)->getDataCar();

        return count($cars);

//        $this->processData($cars, 2023, 1);
//
//        return view('front.cars.index', ['cars' => $cars]);
    }

    public function filter(Request $request): JsonResponse {
        $cars = (new Car)->getDataCar();

        $year = $request->input('years');
        $month = $request->input('months');

        $request->validate([
            'years' => 'required|numeric',
            'months' => 'required|numeric|between:1,12',
        ]);

        $this->processData($cars, $year, $month);

        $html = View::make('front.cars.list', ['cars' => $cars])->render();

        return response()->json(['html' => $html]);
    }

    public function processData($cars, $year, $month)
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth();
        $startDateSearch = Carbon::createFromDate($year - 1, $month, 1)->startOfDay();
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
    }

    private function getCarFreeDays($carBooking, $item, $startDate, $endDate)
    {
        if (count($carBooking) >= 1) {
            $days = 0;
            $lastDay = null;
            $startTime = null;
            $endTime = null;
            $lastStatus = null;
            foreach ($carBooking as $index => $item) {
                if ($item->end_date >= $startDate) {
                    if ($item->start_date <= $startDate && $item->end_date >= $endDate) {
                        return 0;
                    } elseif ($item->start_date <= $startDate && $item->end_date <= $endDate) {
                        $value = $startDate->diffInDays($item->start_date); //   Why????????????????????????????????????????????????????????????????????????????????????????
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
                            $count = Carbon::createFromDate($item->start_date)->diffInDays($startDate);
                            $days = $count;
                        } else {
                            $count = Carbon::createFromDate($item->start_date)->diffInDays($lastDay);
                            if ($lastStatus != 'free') {
                                $days += $count - 1;
                            } else {
                                $days += $count;
                            }
                        }
                        $itemEndDate = Carbon::createFromDate($item->start_date)->setHour(17)->endOfHour();
                        if ($item->start_date >= $itemEndDate) {
                            $days++;
                            $lastStatus = "free";
                        } else {
                            $lastStatus = "rent";
                        }

                        $count = Carbon::createFromDate($item->start_date)->diffInDays($item->end_date);
                        if ($lastStatus != 'free') {
                            $days += $count - 1;
                        } else {
                            $days += $count;
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
                        if ($lastDay != null) {
                            $count = Carbon::createFromDate($endDate)->diffInDays($lastDay);
                            if ($lastStatus != 'free') {
                                $days += $count - 1;
                            } else {
                                $days += $count;
                            }
                        } else {
                            return $startDate->diffInDays($endDate) + 1;
                        }
                        break;
                    }
                }
            }
            return $days;
        }
    }
}
