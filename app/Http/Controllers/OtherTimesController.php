<?php

namespace App\Http\Controllers;

// Modelos
use App\Models\Entorno;
use App\Models\Notification;
use App\Models\OtherTime;
use App\Models\User;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

// Request
use App\Http\Requests\StoreOtherTimeRequest;

class OtherTimesController extends Controller
{
    public function index()
    {
        $otherTimes = OtherTime::with(['requestTo', 'userId'])->orderBy('id', 'DESC')->limit(50)->get();
        return view('adm.otherTimes', ['otherTimes' => $otherTimes]);
    }

    public function show(Request $request)
    {
        $status = $request->input('ot_status');
        $queryOt = OtherTime::with(['requestTo', 'userId']);

        if ($status != 2) {
            $queryOt->where('approved', $status);
        }
        
        $otherTimes = $queryOt->orderBy('id', 'DESC')->limit(50)->get();

        session(['status' => $status]);
        return view('adm.otherTimes', [
            'otherTimes' => $otherTimes
        ]);
    }

    public function store(StoreOtherTimeRequest $request)
    {        
        try {
            $userAuth = Auth::user();

            $otherTime = OtherTime::create([
                'id_user' => $userAuth->id,
                'time' => $request->time_requested,
                'description' => $request->description,
                'request_to' => $request->technician_id,
                'date_time' => $request->date_activity
            ]);

            try {
                Notification::create([
                    'from_user_id' => $userAuth->id,
                    'to_user_id' => $request->technician_id,
                    'message' => 'solicita el cargue de ' . $request->time_requested . ' horas, con asunto: ' . $request->description . ', para el día ' . $request->date_activity,
                    'type' => 'other-times',
                    'status' => 0,
                    'notifiable_type' => User::class,
                    'notifiable_id' => $request->technician_id,
                    'other_time_id' => $otherTime->id,
                ]);

            } catch (\Throwable $th) {
                Log::error($th->getMessage());
                return redirect()->back()->with('error', 'Ha ocurrido un error al crear la notificación.');
            }

            return redirect()->back()->with('success', 'Solicitud enviada correctamente');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', 'Ha ocurrido un error, por favor intente nuevamente');
        }
    }

    public function update(OtherTime $otherTime, Request $request)
    {
        try {
            $isApproved = $request->isApproved;

            $otherTime->update([
                'approved' => $isApproved
            ]);
    
            $message = $isApproved ? "Horas asignadas correctamente" : "Horas rechazadas";
    
            return  response()->json(['message' => $message], 200);
        } catch (\Exception $e){
            Log::error("Ha ocurrido un error, por favor intente nuevamente: ".$e->getMessage());
            return response()->json(['error' => 'Error al actualizar las horas'], 500);
        }   
    }
}