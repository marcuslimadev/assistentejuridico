<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termos de Serviço | LexPraxis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --lp-bg: #f4efe6;
            --lp-surface: rgba(255, 255, 255, 0.8);
            --lp-ink: #1f1a16;
            --lp-muted: #6a6058;
            --lp-accent: #2d6b74;
            --lp-accent-soft: #deeff1;
            --lp-border: rgba(40, 55, 60, 0.14);
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--lp-ink);
            background:
                radial-gradient(circle at top right, rgba(45, 107, 116, 0.16), transparent 28%),
                radial-gradient(circle at bottom left, rgba(138, 75, 42, 0.12), transparent 25%),
                linear-gradient(180deg, #fcfaf6 0%, var(--lp-bg) 100%);
        }

        .shell {
            width: min(980px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 3rem 0 4rem;
        }

        .hero,
        .section-card {
            background: var(--lp-surface);
            backdrop-filter: blur(10px);
            border: 1px solid var(--lp-border);
            border-radius: 28px;
            box-shadow: 0 30px 90px rgba(56, 61, 55, 0.08);
        }

        .hero {
            padding: 2rem;
            margin-bottom: 1.5rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            background: var(--lp-accent-soft);
            color: var(--lp-accent);
            font-size: .85rem;
            font-weight: 800;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(2rem, 4vw, 3.5rem);
            line-height: 1;
            margin: 1rem 0 .9rem;
            font-weight: 800;
        }

        .lede {
            max-width: 760px;
            font-size: 1.05rem;
            color: var(--lp-muted);
        }

        .section-card {
            padding: 1.5rem;
            margin-bottom: 1rem;
        }

        h2 {
            font-size: 1.15rem;
            font-weight: 800;
            margin-bottom: .8rem;
        }

        p,
        li {
            color: #342c27;
            line-height: 1.7;
        }

        ul {
            padding-left: 1.15rem;
            margin-bottom: 0;
        }

        .meta {
            display: flex;
            flex-wrap: wrap;
            gap: .75rem;
            margin-top: 1rem;
        }

        .meta span {
            padding: .65rem .9rem;
            border-radius: 16px;
            background: rgba(255, 255, 255, 0.8);
            border: 1px solid var(--lp-border);
            color: var(--lp-muted);
            font-size: .92rem;
        }

        a {
            color: var(--lp-accent);
        }
    </style>
</head>
<body>
    <main class="shell">
        <section class="hero">
            <div class="eyebrow">LexPraxis • Termos</div>
            <h1>Termos de Serviço</h1>
            <p class="lede">
                Estes Termos de Serviço regulam o acesso e o uso da plataforma LexPraxis, estabelecendo direitos, responsabilidades e condições aplicáveis ao usuário da solução.
            </p>
            <div class="meta">
                <span>Última atualização: {{ now()->format('d/m/Y') }}</span>
                <span>Aplicável ao sistema LexPraxis</span>
            </div>
        </section>

        <section class="section-card">
            <h2>1. Aceitação</h2>
            <p>
                Ao acessar ou utilizar o LexPraxis, o usuário declara estar ciente e de acordo com estes Termos de Serviço e com a Política de Privacidade da plataforma.
            </p>
        </section>

        <section class="section-card">
            <h2>2. Objeto da plataforma</h2>
            <p>
                O LexPraxis é uma plataforma de apoio à gestão jurídica, incluindo organização de clientes, processos, agenda, tarefas, documentos e integrações operacionais permitidas pelo usuário.
            </p>
        </section>

        <section class="section-card">
            <h2>3. Responsabilidades do usuário</h2>
            <ul>
                <li>Manter a confidencialidade de suas credenciais de acesso.</li>
                <li>Inserir dados verdadeiros e atualizados quando utilizar a plataforma.</li>
                <li>Utilizar o sistema em conformidade com a legislação aplicável.</li>
                <li>Responder pelo conteúdo e pelos dados cadastrados sob sua conta.</li>
            </ul>
        </section>

        <section class="section-card">
            <h2>4. Integrações externas</h2>
            <p>
                Recursos de integração, como Google Calendar, dependem de autorização expressa do usuário. O acesso concedido será utilizado somente para executar a funcionalidade solicitada e poderá ser revogado pelo próprio usuário a qualquer momento.
            </p>
        </section>

        <section class="section-card">
            <h2>5. Disponibilidade e limitações</h2>
            <p>
                O LexPraxis busca manter a plataforma disponível e funcional, mas não garante operação ininterrupta, ausência total de falhas ou adequação a qualquer finalidade específica sem configuração e uso corretos pelo usuário.
            </p>
        </section>

        <section class="section-card">
            <h2>6. Propriedade intelectual</h2>
            <p>
                O software, sua estrutura, identidade visual, conteúdos institucionais e demais elementos da plataforma permanecem protegidos pela legislação aplicável, vedada sua reprodução ou exploração indevida sem autorização.
            </p>
        </section>

        <section class="section-card">
            <h2>7. Suspensão e encerramento</h2>
            <p>
                O acesso poderá ser suspenso ou encerrado em caso de uso indevido, violação destes termos, risco à segurança da plataforma ou necessidade técnica de manutenção e proteção do serviço.
            </p>
        </section>

        <section class="section-card">
            <h2>8. Alterações</h2>
            <p>
                Estes Termos de Serviço podem ser atualizados periodicamente para refletir ajustes operacionais, legais ou tecnológicos. A versão vigente será aquela publicada nesta página.
            </p>
        </section>
    </main>
</body>
</html>