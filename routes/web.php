<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::view('/', 'welcome')->name('home');

Route::view('/politica-de-privacidade', 'legal.privacy')->name('legal.privacy');
Route::view('/termos-de-servico', 'legal.terms')->name('legal.terms');

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register'])->name('register.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProcessoController;
use App\Http\Controllers\TarefaController;
use App\Http\Controllers\AgendaController;
use App\Http\Controllers\PrazoController;
use App\Http\Controllers\HonorarioController;
use App\Http\Controllers\ParcelaController;
use App\Http\Controllers\DespesaController;
use App\Http\Controllers\DocumentoController;
use App\Http\Controllers\DocTemplateController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\GoogleCalendarController;
use App\Http\Controllers\CreditController;
use App\Http\Controllers\MercadoPagoWebhookController;
use App\Http\Controllers\RelatorioController;
use App\Http\Controllers\PortalController;

Route::post('/webhooks/mercado-pago', MercadoPagoWebhookController::class)->name('webhooks.mercado-pago');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('clientes', ClienteController::class);
    Route::resource('processos', ProcessoController::class);
    Route::resource('tarefas', TarefaController::class);
    Route::resource('agendas', AgendaController::class);
    Route::resource('prazos', PrazoController::class);
    Route::resource('honorarios', HonorarioController::class);
    Route::resource('parcelas', ParcelaController::class);
    Route::resource('despesas', DespesaController::class);
    Route::resource('documentos', DocumentoController::class);
    Route::resource('doc-templates', DocTemplateController::class);
    Route::get('/google-calendar/connect', [GoogleCalendarController::class, 'redirect'])->name('google-calendar.redirect');
    Route::get('/google-calendar/callback', [GoogleCalendarController::class, 'callback'])->name('google-calendar.callback');
    Route::delete('/google-calendar/disconnect', [GoogleCalendarController::class, 'destroy'])->name('google-calendar.destroy');
    Route::get('/creditos', [CreditController::class, 'index'])->name('credits.index');
    Route::post('/creditos', [CreditController::class, 'store'])->name('credits.store');
    Route::get('/creditos/{purchase}', [CreditController::class, 'show'])->name('credits.show');
    
    // Chat AI
    Route::get('/chat', [ChatController::class, 'index'])->name('chat.index');
    Route::post('/chat/send', [ChatController::class, 'sendMessage'])->name('chat.send');

    // Relatórios e BI
    Route::get('/relatorios', [RelatorioController::class, 'index'])->name('relatorios.index');

    // Portal do Cliente
    Route::get('/portal', [PortalController::class, 'index'])->name('portal.index');
});
