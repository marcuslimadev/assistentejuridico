<?php

namespace App\Http\Controllers;

use App\Models\Tarefa;
use Illuminate\Http\Request;

class TarefaController extends Controller
{
    public function index()
    {
        $tarefas = Tarefa::with(['processo', 'cliente', 'responsavel'])->orderByDesc('created_at')->paginate(10);
        return view('tarefas.index', compact('tarefas'));
    }

    public function create()
    {
        // Placeholder
        return view('tarefas.create');
    }

    public function store(Request $request)
    {
        // Placeholder
        return redirect()->route('tarefas.index');
    }
}
