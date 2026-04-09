<?php

namespace App\Http\Controllers;

use App\Models\Despesa;
use Illuminate\Http\Request;

class DespesaController extends Controller
{
    public function index()
    {
        $despesas = Despesa::with(['processo', 'usuario'])->orderByDesc('data')->paginate(10);
        return view('despesas.index', compact('despesas'));
    }

    public function create() { return view('despesas.create'); }
    public function store(Request $request) { return redirect()->route('despesas.index'); }
}
