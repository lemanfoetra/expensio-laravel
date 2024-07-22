<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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


    public function detailPengeluaranHariIni()
    {
        try {
            $results = DB::table('expenses')
                ->where('id_users', Auth::id())
                ->where('date', date('Y-m-d'))
                ->orderBy('date', 'desc')
                ->orderBy('id', 'desc')
                ->limit(50)
                ->get();

            return response()->json([
                'success'   => true,
                'message'   => '',
                'data'      => $results,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function detailPengeluaranBulanIni()
    {
        try {
            $results = DB::table('expenses')
                ->select([
                    "expenses.date",
                    DB::raw("(SELECT SUM(B.nominal) FROM expenses B where B.id_users = expenses.id_users AND B.date = expenses.date ) AS jumlah_nominal")
                ])
                ->where('id_users', Auth::id())
                ->whereRaw(DB::raw("DATE_FORMAT(date, '%Y-%m') = '" . date('Y-m') . "' "))
                ->orderBy('date', 'desc')
                ->groupBy('date')
                ->limit(31)
                ->get();

            return response()->json([
                'success'   => true,
                'message'   => '',
                'data'      => $results,
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
