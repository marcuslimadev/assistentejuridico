<?php

namespace App\Http\Controllers;

use App\Models\Honorario;
use Illuminate\Http\Request;

class HonorarioController extends Controller
{
    public function index()
    {
        $honorarios = Honorario::with(['processo', 'cliente', 'parcelas'])->orderByDesc('created_at')->paginate(10);
        return view('honorarios.index', compact('honorarios'));
    }

    public function create() { return view('honorarios.create'); }
    public function store(Request $request) { return redirect()->route('honorarios.index'); }
}
