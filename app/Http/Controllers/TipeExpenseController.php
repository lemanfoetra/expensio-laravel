<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TipeExpenseController extends Controller
{
    public function index()
    {
        try {
            $tipeExpenses = DB::table('tipe_expenses')
                ->select(['id', 'tipe'])
                ->get();

            return response()->json([
                'success'   => true,
                'message'   => '',
                'data'      => $tipeExpenses,
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
