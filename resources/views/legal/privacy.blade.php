<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Política de Privacidade | LexPraxis</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        :root {
            --lp-bg: #f5f1e8;
            --lp-surface: rgba(255, 255, 255, 0.78);
            --lp-ink: #1e1a17;
            --lp-muted: #6f655c;
            --lp-accent: #8a4b2a;
            --lp-accent-soft: #efe1d4;
            --lp-border: rgba(56, 35, 20, 0.12);
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: 'Manrope', sans-serif;
            color: var(--lp-ink);
            background:
                radial-gradient(circle at top left, rgba(138, 75, 42, 0.14), transparent 30%),
                radial-gradient(circle at bottom right, rgba(53, 112, 126, 0.12), transparent 28%),
                linear-gradient(180deg, #fbf8f2 0%, var(--lp-bg) 100%);
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
            box-shadow: 0 30px 90px rgba(78, 49, 28, 0.08);
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
            <div class="eyebrow">LexPraxis • Privacidade</div>
            <h1>Política de Privacidade</h1>
            <p class="lede">
                Esta Política de Privacidade descreve como o LexPraxis coleta, utiliza, armazena e protege as informações fornecidas por seus usuários durante a utilização da plataforma.
            </p>
            <div class="meta">
                <span>Última atualização: {{ now()->format('d/m/Y') }}</span>
                <span>Aplicável ao sistema LexPraxis</span>
            </div>
        </section>

        <section class="section-card">
            <h2>1. Dados coletados</h2>
            <p>
                O LexPraxis pode coletar dados cadastrais, informações de autenticação, dados operacionais lançados pelo usuário no sistema e informações necessárias para integrações externas autorizadas, como o Google Calendar.
            </p>
        </section>

        <section class="section-card">
            <h2>2. Finalidade do uso</h2>
            <p>
                Os dados são utilizados para autenticação, organização de compromissos, gestão de processos, clientes e atividades jurídicas, além de permitir recursos integrados explicitamente autorizados pelo usuário.
            </p>
        </section>

        <section class="section-card">
            <h2>3. Integração com Google Calendar</h2>
            <p>
                Quando autorizada pelo usuário, a integração com Google Calendar é utilizada exclusivamente para criar, atualizar ou sincronizar eventos vinculados à agenda do sistema. O acesso ocorre mediante consentimento OAuth e pode ser revogado a qualquer momento pelo usuário.
            </p>
        </section>

        <section class="section-card">
            <h2>4. Compartilhamento de dados</h2>
            <p>
                O LexPraxis não comercializa dados pessoais. As informações somente podem ser compartilhadas quando necessário para execução de integrações autorizadas, cumprimento de obrigação legal ou proteção dos direitos da plataforma e de seus usuários.
            </p>
        </section>

        <section class="section-card">
            <h2>5. Armazenamento e segurança</h2>
            <p>
                São adotadas medidas técnicas e administrativas razoáveis para reduzir risco de acesso não autorizado, alteração indevida, perda ou divulgação indevida dos dados tratados pela plataforma.
            </p>
        </section>

        <section class="section-card">
            <h2>6. Direitos do usuário</h2>
            <ul>
                <li>Solicitar informações sobre dados armazenados.</li>
                <li>Solicitar correção ou atualização de dados cadastrais.</li>
                <li>Revogar integrações previamente autorizadas.</li>
                <li>Solicitar exclusão de dados, quando legalmente aplicável.</li>
            </ul>
        </section>

        <section class="section-card">
            <h2>7. Contato</h2>
            <p>
                Para assuntos relacionados à privacidade e proteção de dados, o contato pode ser realizado pelo responsável pela operação da plataforma LexPraxis.
            </p>
        </section>
    </main>
</body>
</html>