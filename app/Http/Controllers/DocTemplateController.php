<?php

namespace App\Http\Controllers;

use App\Models\DocTemplate;
use Illuminate\Http\Request;

class DocTemplateController extends Controller
{
    public function index()
    {
        $templates = DocTemplate::orderBy('nome')->paginate(10);
        return view('doc_templates.index', compact('templates'));
    }

    public function create() { return view('doc_templates.create'); }
    public function store(Request $request) { return redirect()->route('doc_templates.index'); }
}
