<?php

namespace App\Http\Controllers;

use App\Models\Processo;
use App\Models\Cliente;
use App\Models\User;
use Illuminate\Http\Request;

class ProcessoController extends Controller
{
    public function index(Request $request)
    {
        $query = Processo::with(['cliente', 'advogado']);

        if ($request->filled('busca')) {
            $query->where('numero_cnj', 'like', '%' . $request->busca . '%')
                  ->orWhereHas('cliente', function($q) use ($request) {
                      $q->where('nome', 'like', '%' . $request->busca . '%');
                  });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $processos = $query->orderByDesc('created_at')->paginate(10);
        
        return view('processos.index', compact('processos'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nome')->get();
        $advogados = User::orderBy('name')->get();

        return view('processos.create', compact('clientes', 'advogados'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'cliente_id' => 'required|exists:clientes,id',
            'advogado_id' => 'nullable|exists:users,id',
            'numero_cnj' => 'nullable|string|max:30|unique:processos',
            'tipo_acao' => 'nullable|string|max:255',
            'area_direito' => 'nullable|string|max:255',
            'status' => 'required|in:em andamento,suspenso,arquivado,encerrado,ganho,perdido',
            'vara' => 'nullable|string|max:255',
            'comarca' => 'nullable|string|max:255',
            'tribunal' => 'nullable|string|max:255',
            'juiz' => 'nullable|string|max:255',
            'partes_contrarias' => 'nullable|string',
            'polo' => 'required|in:ativo,passivo,terceiro',
            'valor_causa' => 'nullable|numeric|min:0',
            'data_distribuicao' => 'nullable|date'
        ]);

        Processo::create($validated);

        return redirect()->route('processos.index')->with('success', 'Processo cadastrado com sucesso!');
    }
}
