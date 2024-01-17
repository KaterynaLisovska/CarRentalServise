<?php

namespace App\Http\Controllers\Front;

use App\Models\Car;
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

    public function index(): \Illuminate\View\View
    {
        $cars = (new Car)->getDataCar();

        $this->processData($cars, 2023, 1);

        return view('front.cars.index', ['cars' => $cars]);
    }

    public function filter(Request $request): JsonResponse
    {
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

    public function processData($cars, $year, $month): void
    {
        $startDate = Carbon::createFromDate($year, $month, 1)->setHour(9)->setMinute(0)->setSecond(0);
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth()->setHour(21)->setMinute(0)->setSecond(0);

        foreach ($cars as $item) {
            $carModel = $item->carModel;
            $carBrand = $carModel->carBrand->first() ?? '';
            $carModelTranslation = $carModel->carModelTranslation->first();
            $item->color = $carModel->attribute_interior_color;
            $item->year = $item->attribute_year;
            $item->brand = $carBrand->name;
            $item->name = $carModelTranslation->name ?? '';
            $carBooking = $item->carBooking($startDate, $endDate)->get();
            $item->availableDays = $this->getCarFreeDays($carBooking, $startDate, $endDate, $year, $month);

            $item->days = $startDate->diffInDays($endDate) + 1;
        }
    }

    public function getDaysInMonth($year, $monthNumber)
    {
        // Create a Carbon instance for the first day of the given month
        $firstDayOfMonth = Carbon::create($year, $monthNumber, 1);

        // Get the number of days in the month
        $daysInMonth = $firstDayOfMonth->daysInMonth;

        return $daysInMonth;
    }

    public function generateDays($numbers)
    {
        return array_fill(0, $numbers, true);
    }

    public function isCarBusy($booking, $currentDate)
    {
        $start_date = Carbon::parse($booking->start_date);
        $end_date = Carbon::parse($booking->end_date);
        $day_start = Carbon::parse($currentDate)->setTime(9, 0, 0);
        $day_end = Carbon::parse($currentDate)->setTime(21, 0, 0);

        $begin = $start_date;
        $end = $end_date;

        if ($start_date <= $day_start) {
            $begin = $day_start;
        }
        //  01.01.2024  20:00:00 --- 02.01.2024 10:00:00
        if ($end_date <= $day_end) {
            $end = $day_end;
        }

        $rentHours = $end->diffInHours($begin);
        return $rentHours >= 9;
    }

    public function isTodayBooking($booking, $currentDate)
    {
        $day_start = Carbon::parse($currentDate)->setTime(9, 0, 0);
        $day_end = Carbon::parse($currentDate)->setTime(21, 0, 0);

        $start_date = $booking->start_date;
        $end_date = $booking->end_date;


        return ($start_date >= $day_start && $start_date <= $day_end) || $end_date >= $day_start;
    }

    private function getCarFreeDays($carBookings, $startDate, $endDate, $year, $month)
    {
        $days = 0;
        if (count($carBookings) >= 1) {
            $freeDays = $this->generateDays($this->getDaysInMonth($year, $month));
            //  [true, true, ..., true]
            foreach ($freeDays as $index => $day) {
                $currentDate = Carbon::createFromDate()->setDate($year, $month, $index + 1);
                foreach ($carBookings as $booking) {
                    if ($this->isTodayBooking($booking, $currentDate)) {
                        if ($this->isCarBusy($booking, $currentDate)) {
                            $day = false;
                        }
                    }
                }
            }
            $days = count(array_filter($freeDays, function ($day) {
                return $day;
            }));
        }
        return $days;
    }
}
