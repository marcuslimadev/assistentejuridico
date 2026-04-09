<?php

namespace App\Http\Controllers;

use App\Models\Agenda;
use Illuminate\Http\Request;

class AgendaController extends Controller
{
    public function index()
    {
        $agendas = Agenda::with(['processo', 'cliente', 'usuario'])->orderByDesc('data_inicio')->paginate(10);
        return view('agendas.index', compact('agendas'));
    }

    public function create()
    {
        // Placeholder
        return view('agendas.create');
    }

    public function store(Request $request)
    {
        // Placeholder
        return redirect()->route('agendas.index');
    }
}
