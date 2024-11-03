<?php

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseCreateRequest;
use App\Models\Expense;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ExpenseController extends Controller
{

    public function index(Request $request)
    {
        try {
            $uqery = DB::table('expenses')
                ->where('id_users', Auth::id());

            if ($request->limit != null) {
                $uqery->limit($request->limit);
            } else {
                $uqery->limit(100);
            }

            if ($request->offset != null) {
                $uqery->offset($request->offset);
            }

            if ($request->firstday != '' &&  $request->lastday != '') {
                $uqery->whereBetween('date', [$request->firstday, $request->lastday]);
            }

            $espenses = $uqery->orderBy('date', 'desc')
                ->orderBy('id', 'DESC')
                ->get();

            foreach ($espenses as $key => $value) {
                $tipe = DB::table('tipe_expenses')
                    ->select(['tipe'])
                    ->where('id', $value->id_tipe_expense)
                    ->first();
                $espenses[$key]->tipe_expense = $tipe->tipe;
            }

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


    public function store(ExpenseCreateRequest $request)
    {
        try {
            $expense = Expense::create([
                'date'          => $request->date,
                'nominal'       => $request->nominal,
                'deskripsi'     => $request->deskripsi,
                'id_tipe_expense'   => $request->id_tipe_expense,
                'id_users'      => Auth::id(),
            ]);

            $tipe = DB::table('tipe_expenses')
                ->select(['tipe'])
                ->where('id', $request->id_tipe_expense)
                ->first();
            $expense->tipe_expense = $tipe->tipe;

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $expense,
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
            $expense = DB::table('expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first();

            if (empty($expense)) {
                throw new Exception("Expense empty");
            }

            $tipe = DB::table('tipe_expenses')
                ->select(['tipe'])
                ->where('id', $expense->id_tipe_expense)
                ->first();
            $expense->tipe_expense = $tipe->tipe;

            if ($expense == null) {
                throw new Exception('Data not found.');
            }
            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $expense,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function update(ExpenseCreateRequest $request, $id)
    {
        try {
            $old = DB::table('expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($old)) {
                throw new Exception("Data not found.");
            }

            $expense = [
                'date'          => $request->date,
                'nominal'       => $request->nominal,
                'deskripsi'     => $request->deskripsi,
                'id_tipe_expense'     => $request->id_tipe_expense,
            ];
            DB::table('expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->update($expense);

            $tipe = DB::table('tipe_expenses')
                ->select(['tipe'])
                ->where('id', $request->id_tipe_expense)
                ->first();
            $expense['tipe_expense'] = $tipe->tipe;

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $expense,
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
            $old = DB::table('expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($old)) {
                throw new Exception("Data not found.");
            }

            DB::table('expenses')
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
