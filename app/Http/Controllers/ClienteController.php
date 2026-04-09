<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function index(Request $request)
    {
        $query = Cliente::query();
        
        if ($request->filled('busca')) {
            $query->where('nome', 'like', '%' . $request->busca . '%')
                  ->orWhere('cpf_cnpj', 'like', '%' . $request->busca . '%');
        }

        $clientes = $query->orderBy('nome')->paginate(10);
        return view('clientes.index', compact('clientes'));
    }

    public function create()
    {
        return view('clientes.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nome' => 'required|string|max:255',
            'tipo' => 'required|in:PF,PJ',
            'cpf_cnpj' => 'nullable|string|max:20|unique:clientes',
            'email' => 'nullable|email|max:255',
            'celular' => 'nullable|string|max:20',
            'status' => 'required|in:ativo,inativo,prospecto'
        ]);

        Cliente::create($validated);

        return redirect()->route('clientes.index')->with('success', 'Cliente cadastrado com sucesso!');
    }
}
