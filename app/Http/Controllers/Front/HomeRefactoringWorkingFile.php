<?php

namespace App\Http\Controllers\Front;

use App\Models\Car;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\View;

class HomeRefactoringWorkingFile extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index()
    {
        $cars = (new Car)->getDataCar();

        $year = 2023;
        $month = 1;
        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth();

        foreach ($cars as $item) {
            $carBooking = $item->carBooking($year, $month)->get();
            $item->availableDays = $this->getCarFreeDays($carBooking, $startDate, $endDate);
            $item->days = $startDate->diffInDays($endDate) + 1;
        }

        return view('front.cars.index', ['cars' => $cars]);

    }

    public function filter(Request $request)
    {
        $cars = (new Car)->getDataCar();

        $year = $request->input('years');
        $month = $request->input('months');

        $startDate = Carbon::createFromDate($year, $month, 1)->startOfDay();
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth();

        foreach ($cars as $item) {
            $carBooking = $item->carBooking($year, $month)->get();
            $item->availableDays = $this->getCarFreeDays($carBooking, $startDate, $endDate);
            $item->days = $startDate->diffInDays($endDate) + 1;
        }

        $html = View::make('front.cars.list', ['cars' => $cars])->render();

        $request->validate([
            'years' => 'required|numeric',
            'months' => 'required|numeric|between:1,12',
        ]);


        return response()->json(['html' => $html]);
    }

    private function checkLastDay($lastDay, $item, $days)
    {
        $lastDay = Carbon::parse($lastDay);
        $itemStartDate = Carbon::createFromDate($item->start_date);

        if ($lastDay->day == $itemStartDate->day && ($lastDay->hour <= 12 && $itemStartDate->hour >= 17)) {
            $lastStatus = "free";
            $days++;
        } else {
            if ($item->start_date >= $itemStartDate->setHour(17)->startOfHour()) {
                $days++;
                $lastStatus = "free";
            } else {
                $lastStatus = "rent";
            }
        }

        return ["last" => $lastStatus, "days" => $days];
    }

    private function checkLastStatus($item, $itemEndDate)
    {
        if ($item->end_date >= $itemEndDate) {
            $lastStatus = "rent";
        } else {
            $lastStatus = "free";
        }

        return $lastStatus;
    }

    private function getCarFreeDays($carBooking, $startDate, $endDate)
    {
        if (count($carBooking) >= 1) {
            $days = 0;
            $lastDay = null;
            $startTime = null;
            $endTime = null;
            $lastStatus = null;
            foreach ($carBooking as $index => $item) {
//                dd($item['created_at']);
                if ($item->end_date >= $startDate) {
                    $itemEndDate = Carbon::createFromDate($item->end_date)->setHour(12)->setSecond(1);
                    if ($item->start_date <= $startDate && $item->end_date >= $endDate) {
                        return 0;

                    } elseif ($item->start_date <= $startDate && $item->end_date <= $endDate) {
                        $lastDay = $item->end_date;
                        $lastStatus = $this->checkLastStatus($item, $itemEndDate);
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
                            if ($count != 0) {
                                if ($lastStatus != 'free') {
                                    $days += $count - 1;
                                } else {
                                    $days += $count;
                                }
                            }
                        }

                        $date = $this->checkLastDay($lastDay, $item, $days);
                        $days = $date["days"];
                        $lastStatus = $date["last"];

                        if ($item->end_date <= $endDate) {
                            $lastDay = $item->end_date;
                            $lastStatus = $this->checkLastStatus($item, $itemEndDate);
                        } else {
                            return $days;
                        }
                    } else {
                        $date = $this->checkLastDay($lastDay, $item, $days);
                        $days = $date["days"];
                        $lastStatus = $date["last"];

                        if ($lastDay != null) {
                            $count = Carbon::createFromDate($endDate)->diffInDays($lastDay);
                            $days += $count;

                            return $days;
                        } else {
                            return $startDate->daysInMonth;
                        }
                        break;
                    }
                }
            }
            return $days;
        }
        return $startDate->daysInMonth;
    }
}
