<?php

namespace App\Http\Controllers;

use App\Http\Requests\BudgetCreateRequest;
use App\Models\Budget;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BudgetController extends Controller
{

    public function index()
    {
        try {
            $userId = Auth::user()->id;
            $tgalAwalAkhir = $this->getAwalAkhirBulanIni();


            $uqery = DB::table('tipe_expenses')
                ->select([
                    'tipe_expenses.id as tipe_expense_id',
                    'tipe_expenses.tipe',
                    'budgets.id',
                    'budgets.budget',
                ])
                ->leftJoin('budgets', function ($join) use ($userId) {
                    $join->on('budgets.id_tipe_expenses', '=', 'tipe_expenses.id')
                        ->where('budgets.id_users', '=', $userId);
                })
                ->where('tipe_expenses.id_users', $userId);

            $budgets = $uqery->orderBy('tipe_expenses.id', 'DESC')
                ->get();

            foreach ($budgets as $budget) {
                $expense = DB::table('expenses')
                    ->where('id_tipe_expense', $budget->tipe_expense_id)
                    ->where('id_users', $userId)
                    ->whereBetween('date', [$tgalAwalAkhir['awal'], $tgalAwalAkhir['akhir']])
                    ->sum('nominal');
                $budget->total_expense = $expense;
                $budget->sisa = ($budget->budget ?? 0) - $expense;
            }

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $budgets,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function store(BudgetCreateRequest $request)
    {
        try {
            $id_tipe = DB::table('tipe_expenses')
                ->insertGetId([
                    'tipe'      => $request->tipe,
                    'id_users'  => Auth::id(),
                ]);

            $budget = Budget::create([
                'id_tipe_expenses'  => $id_tipe,
                'budget'       => $request->budget,
                'id_users'     => Auth::id(),
            ]);

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $budget,
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
            $userId = Auth::user()->id;
            $uqery = DB::table('tipe_expenses')
                ->select([
                    'tipe_expenses.id as tipe_expense_id',
                    'tipe_expenses.tipe',
                    'budgets.id',
                    'budgets.budget',
                ])
                ->leftJoin('budgets', function ($join) use ($userId) {
                    $join->on('budgets.id_tipe_expenses', '=', 'tipe_expenses.id')
                        ->where('budgets.id_users', '=', $userId);
                })
                ->where('tipe_expenses.id_users', $userId)
                ->where('tipe_expenses.id', $id);

            $budget = $uqery->orderBy('id', 'asc')
                ->orderBy('tipe_expenses.id', 'DESC')
                ->first();

            if (empty($budget)) {
                throw new Exception("Data empty");
            }

            if ($budget == null) {
                throw new Exception('Data not found.');
            }
            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $budget,
            ], 200);
        } catch (\Throwable $th) {
            return response()->json([
                'success'   => false,
                'message'   => $th->getMessage(),
                'data'      => [],
            ], 500);
        }
    }


    public function update(BudgetCreateRequest $request, $id)
    {
        try {
            $old = DB::table('tipe_expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($old)) {
                throw new Exception("Data not found.");
            }


            $tipe_budget = [
                'tipe'      => $request->tipe,
            ];
            DB::table('tipe_expenses')
                ->where('id', $id)
                ->update($tipe_budget);


            $budget =  DB::table('budgets')
                ->where('id_tipe_expenses', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($budget)) {
                DB::table('budgets')
                    ->insert([
                        'id_tipe_expenses'  => $id,
                        'budget'       => $request->budget,
                        'id_users'     => Auth::id(),
                    ]);
            } else {
                DB::table('budgets')
                    ->where('id_tipe_expenses', $id)
                    ->where('id_users', Auth::id())
                    ->update(['budget' => $request->budget]);
            }

            return response()->json([
                'success'   => true,
                'message'   => 'success',
                'data'      => $request->all(),
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
            $old = DB::table('tipe_expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->first(['id']);
            if (empty($old)) {
                throw new Exception("Data not found.");
            }

            DB::table('tipe_expenses')
                ->where('id', $id)
                ->where('id_users', Auth::id())
                ->delete();

            DB::table('budgets')
                ->where('id_tipe_expenses', $id)
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


    private function getAwalAkhirBulanIni(): array
    {
        $awal  = date('Y-m-01');
        $akhir = date('Y-m-t');

        return [
            'awal'  => $awal,
            'akhir' => $akhir,
        ];
    }
}
