<?php

// App\Http\Controllers\RatingController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\SiteRating;
use Illuminate\Support\Facades\Auth;

class RatingController extends Controller
{
    public function store(Request $request)
    {
        // Validación básica
        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:500'
        ]);

        // Verificar si ya calificó para evitar duplicados
        if (SiteRating::where('user_id', Auth::id())->exists()) {
            return response()->json(['message' => 'Ya has calificado anteriormente.'], 400);
        }

        // Crear la calificación
        SiteRating::create([
            'user_id' => Auth::id(),
            'rating' => $request->rating,
            'comment' => $request->comment
        ]);

        return response()->json(['message' => '¡Gracias por tu opinión!']);
    }
}