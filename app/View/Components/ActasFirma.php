<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\ActaSignature;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ActasFirma extends Component
{
    public $pendienteFirma;
    public $count;

    public function __construct()
    {
        $userId = Auth::id();

        // La lógica centralizada
        $pendienteFirma = ActaSignature::where('user_id', $userId)
            ->where('is_signed', false)
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('acta_signatures as anteriores')
                    ->whereColumn('anteriores.document_id', 'acta_signatures.document_id')
                    ->where('anteriores.is_signed', false)
                    ->where('anteriores.order', '<', DB::raw('acta_signatures.order'));
            })
            ->with('document');

        $this->count = $pendienteFirma->count();
        $this->pendienteFirma = $pendienteFirma->first();
    }

    public function render()
    {
        return view('components.actas-firma');
    }
}
