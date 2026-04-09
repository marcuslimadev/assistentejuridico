<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\Cliente;
use Illuminate\Http\Request;

class RelatorioController extends Controller
{
    public function index()
    {
        // Dummy logic to represent Phase 11
        $totalClientes = Cliente::count();
        $processosGanhos = Processo::where('status', 'ganho')->count();
        $processosPerdidos = Processo::where('status', 'perdido')->count();

        return view('relatorios.index', compact('totalClientes', 'processosGanhos', 'processosPerdidos'));
    }
}
