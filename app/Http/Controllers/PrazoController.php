<?php

namespace App\Http\Controllers;

use App\Models\Prazo;
use Illuminate\Http\Request;

class PrazoController extends Controller
{
    public function index()
    {
        $prazos = Prazo::with(['processo', 'usuario'])->orderBy('data_prazo')->paginate(10);
        return view('prazos.index', compact('prazos'));
    }

    public function create()
    {
        // Placeholder
        return view('prazos.create');
    }

    public function store(Request $request)
    {
        // Placeholder
        return redirect()->route('prazos.index');
    }
}
