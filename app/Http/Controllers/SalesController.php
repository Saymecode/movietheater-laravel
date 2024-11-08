<?php

namespace App\Http\Controllers;

use App\Models\DailySale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class SalesController extends Controller
{
    public function showForm()
    {
        return view('sales.form');
    }

    public function findTopTheater(Request $request)
    {
        $request->validate([
            'date' => 'required|date_format:n/j/Y',
        ]);

        $date = Carbon::createFromFormat('m/d/Y', $request->date)->format('Y-m-d');

        $topTheater = DailySale::join('theaters', 'daily_sales.theater_id', '=', 'theaters.id')
            ->select('theaters.name as theater_name', DB::raw('SUM(daily_sales.sales) as total_sales'))
            ->whereDate('daily_sales.date', $date)
            ->groupBy('daily_sales.theater_id')
            ->orderByDesc('total_sales')
            ->first();

        if ($topTheater) {
            return view('sales.result', ['topTheater' => $topTheater, 'date' => $date]);
        } else {
            return back()->withErrors(['date' => 'No sales data found for the given date.']);
        }
    }
}