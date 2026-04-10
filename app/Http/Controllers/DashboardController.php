<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Processo;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $totalClientes = Cliente::where('status', 'ativo')->count();
        $processosAndamento = Processo::where('status', 'em andamento')->count();
        
        // Mocking others for now since the tables don't exist yet
        $audiencias7Dias = 0; 
        $tarefasAtraso = 0;
        $honorariosMes = 0;

        return view('dashboard', compact(
            'user',
            'totalClientes', 
            'processosAndamento',
            'audiencias7Dias',
            'tarefasAtraso',
            'honorariosMes'
        ));
    }
}
