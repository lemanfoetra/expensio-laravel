<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function pengeluaranHariIni()
    {
        try {
            $result = DB::table('expenses')
                ->selectRaw(DB::raw("sum(nominal) as jumlah"))
                ->where('id_users', auth()->id())
                ->where('date', date('Y-m-d'))
                ->first();

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $result->jumlah ?? 0,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function pengeluaranBulanIni()
    {
        try {
            $result = DB::table('expenses')
                ->selectRaw(DB::raw("sum(nominal) as jumlah"))
                ->where('id_users', auth()->id())
                ->whereRaw(DB::raw("DATE_FORMAT(date, '%Y-%m') = '" . date('Y-m') . "' "))
                ->first();

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $result->jumlah ?? 0,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }
}
