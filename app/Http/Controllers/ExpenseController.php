<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\Expense;
use Carbon\Carbon;
class ExpenseController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        $data = $user->expenses->sortByDesc('created_at');

        return response()->json([
            "data" => $data,
            "message" => 'success'
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = $request->user();

        //return $request->all();

        try {
            $expense = Expense::create([
                'user_id' => $user->id,
                'amount' => $request->amount,
                'category_id' => $request->has('category_id') ? $request->category_id : null,
                'product_name' => $request->product_name,
                'notes' => $request->notes,
                'spent_at' => Carbon::now()
            ]);
            return response()->json(['message' => 'added.', 'expense' => $expense], 201);
        } catch (\Exception $e) {
            \Log::info($e->getMessage());
            return response()->json(['message' => 'error occured.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        try {
            $expense = Expense::findOrFail($id);
            if ($user->id != $expense->user_id) {
                return response()->json(['message' => 'Something went wrong.'], 403);
            }
            return response()->json([
                "data" => $expense,
                "message" => "successful"
            ]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong.'], 500);
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = $request->user();

        try {
            $expense = Expense::findOrFail($id);
            if ($expense->user_id != $request->user()->id) {
                return response()->json(['message' => 'Something went wrong.'], 403);
            }
            $expense->update([
                'amount' => $request->amount,
                'category_id' => $request->has('category_id') ? $request->category_id : null,
                'product_name' => $request->product_name,
                'notes' => $request->notes,
                'spent_at' => Carbon::now()
            ]);
            return response()->json(['data' => $expense, 'message' => 'updated.'], 201);
        } catch (\Exception $e) {
            return response()->json(['message' => 'error while updating.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $id = $request->id;

        try {
            $expense = Expense::findOrFail($id);
            // check if the item belongs to this user
            if ($expense->user_id != $request->user()->id) {
                return response()->json(['message' => 'Something went wrong.'], 403);
            }
            $expense->delete();
            return response()->json([
                'message' => 'deleted.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'error'
            ], 500);
        }
    }
}
