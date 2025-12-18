<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCreateRequest;
use App\Http\Requests\IncomesCreateRequest;
use App\Models\Income;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class IncomesController extends Controller
{

    public function index(Request $request)
    {
        try {
            $uqery = DB::table('incomes')
                ->where('id_users', Auth::id());

            if ($request->limit != null) {
                $uqery->limit($request->limit);
            } else {
                $uqery->limit(100);
            }

            if ($request->offset != null) {
                $uqery->offset($request->offset);
            }

            $espenses = $uqery->orderBy('income_date', 'desc')
                ->orderBy('id', 'DESC')
                ->get();

            return response()->json([
                'success'   => true,
                'message'   => '',
                'data'      => $espenses,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function store(IncomesCreateRequest $request)
    {
        try {
            $income = Income::create([
                'income_date'  => $request->income_date,
                'source'       => $request->source,
                'amount'       => $request->amount,
                'id_users'     => Auth::id(),
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $income,
            ], 201);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function show($id)
    {
        try {
            $income = DB::table('incomes')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first();

            if (empty($income)) {
                throw new Exception("Income empty");
            }

            if ($income == null) {
                throw new Exception('Data not found.');
            }
            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $income,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function update(IncomesCreateRequest $request, $id)
    {
        try {
            $old = DB::table('incomes')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($old)) {
                throw new Exception("Data not found.");
            }

            $income = [
                'income_date'  => $request->income_date,
                'source'       => $request->source,
                'amount'       => $request->amount,
            ];
            DB::table('incomes')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->update($income);

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $income,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function destroy($id)
    {
        try {
            $old = DB::table('incomes')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($old)) {
                throw new Exception("Data not found.");
            }

            DB::table('incomes')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->delete();

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => [],
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
