@extends('layouts.app')

@section('title', 'Créditos')

@section('content')
<div
    class="container-fluid"
    id="creditsPage"
    data-unit-price-cents="{{ $consultaUnitPriceCents }}"
    data-credits-base-url="{{ url('/creditos') }}"
    data-credits-store-url="{{ route('credits.store') }}"
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
                        <div class="dashboard-kicker"><i class="bi bi-qr-code-scan"></i>Compra via Pix</div>
                        <h5 class="fw-bold mb-1">Gerar cobrança</h5>
                        <p class="text-body-secondary mb-0">Escolha a quantidade de consultas e pague pelo QR Code do Mercado Pago.</p>
                    </div>
                    @if (! $mercadoPagoConfigured)
                        <span class="badge text-bg-warning">Mercado Pago não configurado</span>
                    @endif
                </div>

                <form id="purchaseCreditsForm" class="row g-3">
                    @csrf
                    <div class="col-md-6">
                        <label for="creditsQuantity" class="form-label">Quantidade de consultas</label>
                        <input type="number" min="1" step="1" value="20" class="form-control" id="creditsQuantity" name="credits_quantity">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Total estimado</label>
                        <div class="form-control d-flex align-items-center fw-bold" id="purchaseTotal">R$ 1,00</div>
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary" id="purchaseCreditsButton" {{ $mercadoPagoConfigured ? '' : 'disabled' }}>
                            <i class="bi bi-cash-coin me-2"></i>Gerar Pix
                        </button>
                    </div>
                </form>

                <div id="purchaseFeedback" class="small mt-3 text-body-secondary"></div>

                <div id="activePurchasePanel" class="content-card mt-4 p-4 {{ $activePurchase ? '' : 'd-none' }}" data-purchase-id="{{ $activePurchase?->id }}">
                    <div class="d-flex justify-content-between align-items-center gap-3 mb-3">
                        <div>
                            <h6 class="fw-bold mb-1">Pagamento em andamento</h6>
                            <div class="text-body-secondary small">Status: <span id="purchaseStatusText">{{ $activePurchase->status ?? 'pending' }}</span></div>
                        </div>
                        <span class="badge text-bg-info" id="purchaseCreditsText">{{ $activePurchase?->credits_quantity ?? 0 }} crédito(s)</span>
                    </div>

                    <div class="row g-4 align-items-start">
                        <div class="col-lg-4">
                            <img id="purchaseQrImage" class="img-fluid rounded-4 border {{ $activePurchase?->pix_qr_code_base64 ? '' : 'd-none' }}" src="{{ $activePurchase?->pix_qr_code_base64 ? 'data:image/png;base64,'.$activePurchase->pix_qr_code_base64 : '' }}" alt="QR Code Pix">
                        </div>
                        <div class="col-lg-8">
                            <label class="form-label fw-bold">Código Pix copia e cola</label>
                            <textarea id="purchaseQrCode" class="form-control" rows="5" readonly>{{ $activePurchase?->pix_qr_code }}</textarea>
                            <div class="d-flex flex-wrap gap-2 mt-3">
                                <button type="button" class="btn btn-outline-primary" id="copyPixCodeButton">Copiar código Pix</button>
                                <a href="{{ $activePurchase?->ticket_url ?: '#' }}" target="_blank" id="openTicketButton" class="btn btn-outline-secondary {{ $activePurchase?->ticket_url ? '' : 'd-none' }}">Abrir cobrança</a>
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
                                <th>Créditos</th>
                                <th>Valor</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($recentPurchases as $purchase)
                                <tr>
                                    <td>{{ $purchase->created_at->format('d/m/Y H:i') }}</td>
                                    <td>{{ $purchase->credits_quantity }}</td>
                                    <td>R$ {{ number_format($purchase->total_amount_cents / 100, 2, ',', '.') }}</td>
                                    <td><span class="badge text-bg-secondary">{{ $purchase->status }}</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-body-secondary">Nenhuma compra registrada ainda.</td>
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
    const purchaseQrImage = document.getElementById('purchaseQrImage');
    const purchaseQrCode = document.getElementById('purchaseQrCode');
    const openTicketButton = document.getElementById('openTicketButton');
    const approvedMessage = document.getElementById('purchaseApprovedMessage');
    const copyPixCodeButton = document.getElementById('copyPixCodeButton');

    let purchasePollTimer = null;

    const formatMoney = function (cents) {
        return `R$ ${(cents / 100).toFixed(2).replace('.', ',')}`;
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
        purchaseQrCode.value = purchase.pix_qr_code || '';
        balanceValue.textContent = creditsBalance;

        if (purchase.pix_qr_code_base64) {
            purchaseQrImage.src = `data:image/png;base64,${purchase.pix_qr_code_base64}`;
            purchaseQrImage.classList.remove('d-none');
        }

        if (purchase.ticket_url) {
            openTicketButton.href = purchase.ticket_url;
            openTicketButton.classList.remove('d-none');
        }

        if (purchase.status === 'approved' && purchase.credited_at) {
            approvedMessage.textContent = 'Pagamento aprovado e créditos liberados em tempo real.';
            stopPolling();
        } else {
            approvedMessage.textContent = 'Aguardando aprovação do Pix pelo Mercado Pago.';
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

    copyPixCodeButton?.addEventListener('click', async function () {
        if (!purchaseQrCode.value) {
            return;
        }

        await navigator.clipboard.writeText(purchaseQrCode.value);
        feedback.textContent = 'Código Pix copiado.';
    });

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
                feedback.textContent = 'Cobrança Pix gerada com sucesso.';
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