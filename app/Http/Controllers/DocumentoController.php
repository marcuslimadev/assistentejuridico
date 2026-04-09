<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{
    public function index()
    {
        $documentos = Documento::with(['processo', 'usuario'])->orderByDesc('created_at')->paginate(10);
        return view('documentos.index', compact('documentos'));
    }

    public function create() { return view('documentos.create'); }
    public function store(Request $request) { return redirect()->route('documentos.index'); }
}
