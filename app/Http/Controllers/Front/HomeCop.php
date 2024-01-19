<?php

namespace App\Http\Controllers\Front;

use App\Models\CarCopy;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\View;

class HomeCop extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function index(): \Illuminate\View\View
    {
        $cars = (new CarCopy)->getDataCar();

        $this->processData($cars, 2023, 1);

        return view('front.cars.index', ['cars' => $cars]);
    }

    public function filter(Request $request): JsonResponse
    {
        $cars = (new CarCopy)->getDataCar();

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
        $startDate = Carbon::createFromDate($year, $month)->startOfMonth()->setHour(13)->setMinute(0)->setSecond(0);
        $endDate = Carbon::createFromDate($year, $month)->endOfMonth()->setHour(21)->setMinute(0)->setSecond(0);

        $daysInMonth = $this->getDaysInMonth($year, $month);

        foreach ($cars as $car) {
            $carModel = $car->carModel;
            $carBrand = $carModel->carBrand->first() ?? '';
            $carModelTranslation = $carModel->carModelTranslation->first();
            $car->color = $carModel->attribute_interior_color;
            $car->year = $car->attribute_year;
            $car->brand = $carBrand->name;
            $car->name = $carModelTranslation->name ?? '';
            $carBookings = $car->carBooking($startDate, $endDate);

            $availableDays = $daysInMonth;

            foreach ($carBookings as $booking) {
                $availableDays -= $this->getBusyDays($booking, $startDate, $endDate);
            }

            $car->availableDays = $availableDays;
            $car->days = $daysInMonth;
        }
    }

    function getBusyDays($booking, $startMonthDate, $endMonthDate)
    {
        $startDate = $booking->start_date <= $startMonthDate ? $startMonthDate : $booking->start_date;
        $endDate = $booking->end_date >= $endMonthDate ? $endMonthDate : $booking->end_date;

        $betweenDays = Carbon::parse($endDate)->day - Carbon::parse($startDate)->day;

        $last_f = Carbon::parse($startDate)->setHour(17)->setMinute(0)->setSecond(0);
        $first_l = Carbon::parse($endDate)->setHour(12)->setMinute(0)->setSecond(0);

        $isSameDay = Carbon::parse($startDate)->startOfDay() == Carbon::parse($endDate)->startOfDay();

        $isFirstDayBusy = !(Carbon::parse($startDate)->gt($last_f));
        $isLastDayBusy = !(Carbon::parse($endDate)->lte($first_l)) && !$isSameDay;

        $firstDay = $isFirstDayBusy ? 1 : 0;
        $lastDay = $isLastDayBusy ? 1 : 0;

        $betweenDaysCondition = $betweenDays > 0 ? $betweenDays - 1 : 0;

        $days = $firstDay + $lastDay + $betweenDaysCondition;

        if($booking->car_id == 3759){
            Log::info("Start Date:  " . $booking->start_date . "  End Date:  " . $booking->end_date . "  Car ID:  " . $booking->car_id);
            Log::info($firstDay . "  " . $lastDay . "  " . $betweenDaysCondition . "  " . $startDate . "   " . $endDate . "   " . $betweenDays);
        }

        return $days;
    }

    public function getDaysInMonth($year, $monthNumber)
    {
        $firstDayOfMonth = Carbon::create($year, $monthNumber)->firstOfMonth();

        $daysInMonth = $firstDayOfMonth->daysInMonth;

        return $daysInMonth;
    }
}
