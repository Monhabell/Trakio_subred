<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\UserHour;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

//Request
use App\Http\Requests\StoreHoursRequest;

class UserHoursController extends Controller
{
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreHoursRequest $request)
    {
        $month = $request->number_month;
        $year = $request->year;
        $user_id = $request->user_id;
        $month_hours = $request->month_hours ?? 184;
        $overtime_hours = $request->overtime_hours ?? 0;

        DB::beginTransaction();
        try {
            $registeredHours = UserHour::where('user_id', $user_id)
                ->where('number_month', $month)
                ->where('year', $year)
                ->first();

            if ($registeredHours) {
                $registeredHours->update([
                    'total_over_times' => $overtime_hours,
                    'hours_per_month' => $month_hours
                ]);
            }else{
                UserHour::create([
                    'user_id' => $user_id,
                    'number_month' => $month,
                    'year' => $year,
                    'total_over_times' => $overtime_hours,
                    'hours_per_month' => $month_hours
                ]);
            }

            DB::commit();
            return back()->with('success', 'Horas asignadas correctamente');
        } catch (\Throwable $th) {
            DB::rollBack();
            Log::error($th->getMessage());
            return back()->with('error', 'Error al guardar los datos')->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}