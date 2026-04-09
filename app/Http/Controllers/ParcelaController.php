<?php

namespace App\Http\Controllers;

use App\Models\Parcela;
use Illuminate\Http\Request;

class ParcelaController extends Controller
{
    public function index()
    {
        $parcelas = Parcela::with(['honorario'])->orderBy('vencimento')->paginate(10);
        return view('parcelas.index', compact('parcelas'));
    }
}
