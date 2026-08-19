<?php

namespace App\Http\Controllers;

// Facades
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Modelos
use App\Models\Role;

class RoleController extends Controller
{
    public function index(){
        $roles = Role::all();

        return view('env.edit-activities', [
            'roles'=>$roles
        ]);
    }

    public function show(Request $request)
    {
        $user = Auth::user();
        $role = Role::with([
            'activities' => function ($query) use($user) {
                $query->where('environment_id', $user->environment_id)
                    ->orderBy('number_activity', 'asc');
            }
        ])
            ->where('id', $request->role)
            ->first();

        if (!$role) {
            return response()->json([
                'message' => 'No se encontró el perfil'
            ], 404);
        }

        return response()->json([
            'role' => $role
        ]);
    }
}