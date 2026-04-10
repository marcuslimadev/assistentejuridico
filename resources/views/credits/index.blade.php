@extends('layouts.app')

@section('title', 'Créditos')

@section('content')
<div
    class="container-fluid"
    id="creditsPage"
    data-unit-price-cents="{{ $consultaUnitPriceCents }}"
    data-credits-base-url="{{ url('/creditos') }}"
    data-credits-store-url="{{ route('credits.store') }}"
    data-active-payment-method="{{ data_get($activePurchase?->payment_payload, 'metadata.payment_method', 'card') }}"
>
    <div class="page-header">
        <div>
            <h2 class="page-title">Créditos de Consulta</h2>
            <p class="page-subtitle">Compre consultas DataJud por Pix e receba os créditos em tempo real após a aprovação.</p>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-xl-5">
            <div class="dashboard-hero h-100">
                <div class="dashboard-kicker"><i class="bi bi-lightning-charge-fill"></i>Saldo disponível</div>
                <h3 class="fw-bold mb-3">{{ auth()->user()->consulta_credits }} consulta(s) pronta(s) para uso.</h3>
                <p class="text-body-secondary mb-4">Cada consulta DataJud consome 1 crédito. O preço unitário atual é de R$ {{ number_format($consultaUnitPriceCents / 100, 2, ',', '.') }}.</p>

                <div class="hero-stat-grid">
                    <div class="hero-stat">
                        <div class="hero-stat-label">Saldo atual</div>
                        <div class="hero-stat-value" id="creditsBalanceValue">{{ auth()->user()->consulta_credits }}</div>
                    </div>
                    <div class="hero-stat">
                        <div class="hero-stat-label">Preço unitário</div>
                        <div class="hero-stat-value">R$ {{ number_format($consultaUnitPriceCents / 100, 2, ',', '.') }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-7">
            <div class="feature-panel h-100 p-4">
                <div class="d-flex justify-content-between align-items-start gap-3 mb-4">
                    <div>
                        <div class="dashboard-kicker"><i class="bi bi-credit-card-2-front"></i>Checkout Stripe</div>
                        <h5 class="fw-bold mb-1">Gerar pagamento</h5>
                        <p class="text-body-secondary mb-0">Escolha a quantidade de consultas e pague com cartao de credito ou boleto em uma tela segura da Stripe.</p>
                    </div>
                    @if (! $stripeConfigured)
                        <span class="badge text-bg-warning">Stripe não configurada</span>
                    @endif
                </div>

                <form id="purchaseCreditsForm" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="creditsQuantity" class="form-label">Quantidade de consultas</label>
                        <input type="number" min="1" step="1" value="20" class="form-control" id="creditsQuantity" name="credits_quantity">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Forma de pagamento</label>
                        <div class="d-flex flex-column gap-2">
                            <label class="payment-method-option border rounded-3 px-3 py-2">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="card" checked>
                                <span class="fw-semibold">Cartao de credito</span>
                                <span class="text-body-secondary small d-block">Aprovacao imediata pela Stripe.</span>
                            </label>
                            <label class="payment-method-option border rounded-3 px-3 py-2">
                                <input class="form-check-input me-2" type="radio" name="payment_method" value="boleto">
                                <span class="fw-semibold">Boleto</span>
                                <span class="text-body-secondary small d-block">A Stripe coleta o CPF/CNPJ e gera o boleto hospedado.</span>
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total estimado</label>
                        <div class="form-control d-flex align-items-center fw-bold" id="purchaseTotal">R$ 1,00</div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="purchaseCreditsButton" {{ $stripeConfigured ? '' : 'disabled' }}>
                            <i class="bi bi-box-arrow-up-right me-2"></i>Abrir checkout
                        </button>
                    </div>
                </form>

                <div id="purchaseFeedback" class="small mt-3 text-body-secondary">Se o boleto estiver desabilitado no painel da Stripe, a tentativa retornara uma mensagem explicando isso.</div>

                <div id="activePurchasePanel" class="content-card mt-4 p-4 {{ $activePurchase ? '' : 'd-none' }}" data-purchase-id="{{ $activePurchase?->id }}">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">Pagamento em andamento</h6>
                            <div class="text-body-secondary small">Status: <span id="purchaseStatusText">{{ $activePurchase->status ?? 'pending' }}</span></div>
                        </div>
                        <div class="d-flex flex-wrap gap-2 justify-content-end">
                            <span class="badge text-bg-info" id="purchaseCreditsText">{{ $activePurchase?->credits_quantity ?? 0 }} crédito(s)</span>
                            <span class="badge text-bg-light" id="purchaseMethodText">{{ data_get($activePurchase?->payment_payload, 'metadata.payment_method', 'card') === 'boleto' ? 'Boleto' : 'Cartao de credito' }}</span>
                        </div>
                    </div>

                    <div class="row g-4 align-items-start">
                        <div class="col-12">
                            <p class="text-body-secondary mb-3" id="purchaseInstructionsText">A Stripe abrira uma tela segura para concluir o pagamento e, no caso do boleto, emitir o voucher para pagamento.</p>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <a href="{{ $activePurchase?->ticket_url ?: '#' }}" target="_blank" id="openTicketButton" class="btn btn-primary {{ $activePurchase?->ticket_url ? '' : 'd-none' }}">Abrir checkout na Stripe</a>
                            </div>
                            <div class="small text-body-secondary mt-3" id="purchaseApprovedMessage"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mt-1">
        <div class="col-12">
            <div class="feature-panel p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="mb-0">Compras recentes</h5>
                    <span class="inline-meta"><i class="bi bi-clock-history"></i>Últimas transações</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr>
                                <th>Data</th>
                                <th>Forma</th>
                                <th>Créditos</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPurchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ data_get($purchase->payment_payload, 'metadata.payment_method', 'card') === 'boleto' ? 'Boleto' : 'Cartao de credito' }}</td>
                                    <td>{{ $purchase->credits_quantity }}</td>
                                    <td>R$ {{ number_format($purchase->total_amount_cents / 100, 2, ',', '.') }}</td>
                                    <td><span class="badge text-bg-secondary">{{ $purchase->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-body-secondary">Nenhuma compra registrada ainda.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    const creditsPage = document.getElementById('creditsPage');
    const unitPriceCents = Number(creditsPage.dataset.unitPriceCents || 5);
    const creditsBaseUrl = creditsPage.dataset.creditsBaseUrl;
    const creditsStoreUrl = creditsPage.dataset.creditsStoreUrl;
    const quantityInput = document.getElementById('creditsQuantity');
    const totalDisplay = document.getElementById('purchaseTotal');
    const purchaseForm = document.getElementById('purchaseCreditsForm');
    const purchaseButton = document.getElementById('purchaseCreditsButton');
    const feedback = document.getElementById('purchaseFeedback');
    const balanceValue = document.getElementById('creditsBalanceValue');
    const activePurchasePanel = document.getElementById('activePurchasePanel');
    const purchaseStatusText = document.getElementById('purchaseStatusText');
    const purchaseCreditsText = document.getElementById('purchaseCreditsText');
    const purchaseMethodText = document.getElementById('purchaseMethodText');
    const purchaseInstructionsText = document.getElementById('purchaseInstructionsText');
    const openTicketButton = document.getElementById('openTicketButton');
    const approvedMessage = document.getElementById('purchaseApprovedMessage');

    let purchasePollTimer = null;

    const formatMoney = function (cents) {
        return `R$ ${(cents / 100).toFixed(2).replace('.', ',')}`;
    };

    const formatPaymentMethod = function (paymentMethod) {
        return paymentMethod === 'boleto' ? 'Boleto' : 'Cartao de credito';
    };

    const updateTotal = function () {
        const quantity = Math.max(1, Number(quantityInput.value || 0));
        totalDisplay.textContent = formatMoney(quantity * unitPriceCents);
    };

    const renderPurchase = function (purchase, creditsBalance) {
        if (!purchase) {
            return;
        }

        activePurchasePanel.classList.remove('d-none');
        activePurchasePanel.dataset.purchaseId = purchase.id;
        purchaseStatusText.textContent = purchase.status;
        purchaseCreditsText.textContent = `${purchase.credits_quantity} crédito(s)`;
        purchaseMethodText.textContent = purchase.payment_method_label || formatPaymentMethod(purchase.payment_method);
        balanceValue.textContent = creditsBalance;

        if (purchase.ticket_url) {
            openTicketButton.href = purchase.ticket_url;
            openTicketButton.classList.remove('d-none');
        }

        if (purchase.status === 'approved' && purchase.credited_at) {
            approvedMessage.textContent = 'Pagamento aprovado e créditos liberados em tempo real.';
            purchaseInstructionsText.textContent = 'Pagamento concluido na Stripe e saldo atualizado.';
            stopPolling();
        } else if (purchase.status === 'expired' || purchase.status === 'failed') {
            approvedMessage.textContent = 'O pagamento expirou ou falhou. Gere uma nova cobranca para continuar.';
            purchaseInstructionsText.textContent = 'Voce pode abrir um novo checkout e escolher novamente entre cartao e boleto.';
            stopPolling();
        } else {
            approvedMessage.textContent = purchase.payment_method === 'boleto'
                ? 'Boleto emitido. Aguarde a compensacao para liberar os creditos.'
                : 'Aguardando confirmacao do pagamento pela Stripe.';
            purchaseInstructionsText.textContent = purchase.payment_method === 'boleto'
                ? 'Abra o checkout ou voucher da Stripe para copiar a linha digitavel ou baixar o boleto.'
                : 'Abra o checkout da Stripe para concluir o pagamento com cartao de credito.';
            startPolling();
        }
    };

    const stopPolling = function () {
        if (purchasePollTimer) {
            clearInterval(purchasePollTimer);
            purchasePollTimer = null;
        }
    };

    const pollPurchase = async function () {
        const purchaseId = activePurchasePanel.dataset.purchaseId;
        if (!purchaseId) {
            stopPolling();
            return;
        }

        try {
            const response = await fetch(`${creditsBaseUrl}/${purchaseId}`, {
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();
            if (data.purchase) {
                renderPurchase(data.purchase, data.credits_balance);
            }
        } catch (error) {
        }
    };

    const startPolling = function () {
        stopPolling();
        purchasePollTimer = setInterval(pollPurchase, 5000);
    };

    quantityInput.addEventListener('input', updateTotal);
    updateTotal();

    purchaseForm.addEventListener('submit', async function (event) {
        event.preventDefault();
        feedback.textContent = '';
        purchaseButton.disabled = true;

        try {
            const formData = new FormData(purchaseForm);
            const response = await fetch(creditsStoreUrl, {
                method: 'POST',
                body: formData,
                headers: { 'Accept': 'application/json' }
            });

            const data = await response.json();

            if (!response.ok) {
                feedback.textContent = data.error || 'Não foi possível gerar a cobrança.';
            } else {
                renderPurchase(data.purchase, data.credits_balance);
                feedback.textContent = data.purchase.payment_method === 'boleto'
                    ? 'Checkout criado. Abra a Stripe para emitir o boleto.'
                    : 'Checkout criado. Abra a Stripe para pagar com cartao de credito.';
            }
        } catch (error) {
            feedback.textContent = 'Falha de conexão ao gerar a cobrança.';
        }

        purchaseButton.disabled = false;
    });

    if (activePurchasePanel.dataset.purchaseId) {
        startPolling();
    }
</script>
@endsection