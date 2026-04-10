<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexPraxis IA</title>
    <meta name="description" content="Plataforma jurídica com gestão operacional, agenda integrada e assistente de IA para escritórios e departamentos jurídicos.">
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;500;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --lp-bg: #f6f0e7;
            --lp-surface: rgba(255, 255, 255, 0.84);
            --lp-ink: #1f1a16;
            --lp-muted: #655b53;
            --lp-accent: #8a4b2a;
            --lp-accent-2: #2c6a74;
            --lp-border: rgba(52, 34, 20, 0.12);
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            min-height: 100vh;
            font-family: 'Manrope', sans-serif;
            color: var(--lp-ink);
            background:
                radial-gradient(circle at top left, rgba(138, 75, 42, 0.18), transparent 28%),
                radial-gradient(circle at bottom right, rgba(44, 106, 116, 0.16), transparent 30%),
                linear-gradient(180deg, #fcfaf6 0%, var(--lp-bg) 100%);
        }

        a {
            color: inherit;
        }

        .shell {
            width: min(1180px, calc(100% - 2rem));
            margin: 0 auto;
            padding: 1rem 0 3rem;
        }

        .topbar,
        .hero,
        .panel,
        .footer {
            background: var(--lp-surface);
            backdrop-filter: blur(10px);
            border: 1px solid var(--lp-border);
            box-shadow: 0 24px 80px rgba(75, 51, 31, 0.08);
        }

        .topbar,
        .footer {
            border-radius: 22px;
            padding: 1rem 1.25rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 1rem;
            font-weight: 800;
        }

        .brand img {
            width: 58px;
            height: auto;
        }

        .nav-links,
        .footer-links {
            display: flex;
            flex-wrap: wrap;
            gap: .9rem 1.2rem;
            align-items: center;
        }

        .nav-links a,
        .footer-links a {
            text-decoration: none;
            color: var(--lp-muted);
            font-weight: 700;
        }

        .nav-links a:hover,
        .footer-links a:hover {
            color: var(--lp-accent);
        }

        .hero {
            margin-top: 1rem;
            border-radius: 34px;
            padding: 2rem;
            display: grid;
            grid-template-columns: 1.3fr .9fr;
            gap: 1.25rem;
        }

        .eyebrow {
            display: inline-flex;
            align-items: center;
            gap: .55rem;
            padding: .45rem .8rem;
            border-radius: 999px;
            background: rgba(138, 75, 42, 0.12);
            color: var(--lp-accent);
            font-size: .78rem;
            font-weight: 800;
            letter-spacing: .05em;
            text-transform: uppercase;
        }

        h1 {
            font-size: clamp(2.4rem, 6vw, 4.8rem);
            line-height: .98;
            margin: 1rem 0;
        }

        .lede {
            font-size: 1.08rem;
            color: var(--lp-muted);
            max-width: 50rem;
            line-height: 1.75;
        }

        .hero-actions {
            display: flex;
            flex-wrap: wrap;
            gap: .9rem;
            margin-top: 1.5rem;
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: .6rem;
            min-height: 52px;
            padding: 0 1.15rem;
            border-radius: 16px;
            border: 1px solid transparent;
            text-decoration: none;
            font-weight: 800;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--lp-accent) 0%, #aa5c31 100%);
            color: #fff;
            box-shadow: 0 18px 40px rgba(138, 75, 42, 0.24);
        }

        .btn-secondary {
            background: rgba(255, 255, 255, .7);
            border-color: var(--lp-border);
            color: var(--lp-ink);
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1rem;
        }

        .panel {
            border-radius: 24px;
            padding: 1.2rem;
        }

        .panel h2,
        .panel h3 {
            margin: 0 0 .7rem;
        }

        .panel p {
            color: var(--lp-muted);
            line-height: 1.7;
            margin: 0;
        }

        .legal-panel a {
            color: var(--lp-accent-2);
            font-weight: 800;
        }

        .hero-side {
            display: grid;
            gap: 1rem;
        }

        .stat {
            display: grid;
            gap: .35rem;
        }

        .stat strong {
            font-size: 2rem;
        }

        .footer {
            margin-top: 1rem;
            color: var(--lp-muted);
            font-size: .95rem;
        }

        @media (max-width: 960px) {
            .hero {
                grid-template-columns: 1fr;
            }

            .card-grid {
                grid-template-columns: 1fr;
            }

            .topbar,
            .footer {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="shell">
        <header class="topbar">
            <div class="brand">
                <img src="{{ asset('logo.png') }}" alt="LexPraxis IA">
                <div>
                    <div>LexPraxis IA</div>
                    <div style="font-size: .9rem; color: var(--lp-muted); font-weight: 700;">Gestão jurídica com agenda, operação e IA</div>
                </div>
            </div>
            <nav class="nav-links" aria-label="Links principais">
                <a href="{{ route('login') }}">Entrar</a>
                <a href="{{ route('register') }}">Criar conta</a>
                <a href="{{ route('legal.privacy') }}">Política de Privacidade</a>
                <a href="{{ route('legal.terms') }}">Termos de Serviço</a>
            </nav>
        </header>

        <main class="hero">
            <section>
                <div class="eyebrow">Plataforma jurídica</div>
                <h1>LexPraxis organiza a operação jurídica e conecta sua agenda ao fluxo real do escritório.</h1>
                <p class="lede">Centralize clientes, processos, prazos, tarefas e compromissos em um ambiente único. A plataforma também oferece integração com Google Calendar mediante autorização explícita do usuário e uso conforme nossa Política de Privacidade.</p>
                <div class="hero-actions">
                    <a class="btn btn-primary" href="{{ route('login') }}">Acessar plataforma</a>
                    <a class="btn btn-secondary" href="{{ route('legal.privacy') }}">Ler Política de Privacidade</a>
                </div>

                <div class="card-grid">
                    <article class="panel">
                        <h2>Agenda operacional</h2>
                        <p>Controle audiências, reuniões, prazos e eventos internos em uma agenda pronta para integração com Google Calendar.</p>
                    </article>
                    <article class="panel">
                        <h2>Fluxo jurídico</h2>
                        <p>Gerencie processos, clientes e tarefas em um fluxo contínuo, com cadastro mais rápido e menos troca de tela.</p>
                    </article>
                    <article class="panel legal-panel">
                        <h2>Transparência</h2>
                        <p>Consulte nossa <a href="{{ route('legal.privacy') }}">Política de Privacidade</a> e nossos <a href="{{ route('legal.terms') }}">Termos de Serviço</a> para entender como a plataforma trata dados e integrações.</p>
                    </article>
                </div>
            </section>

            <aside class="hero-side">
                <section class="panel">
                    <h3>Uso do Google Calendar</h3>
                    <p>A integração só acontece após consentimento OAuth do usuário. O acesso é usado apenas para criar ou atualizar eventos da agenda do sistema.</p>
                </section>
                <section class="panel stat">
                    <span style="color: var(--lp-accent-2); font-weight: 800; text-transform: uppercase; letter-spacing: .05em; font-size: .8rem;">Segurança e governança</span>
                    <strong>Política pública disponível</strong>
                    <p>A página inicial do domínio contém links públicos e permanentes para os documentos legais exigidos pelo Google OAuth.</p>
                </section>
            </aside>
        </main>

        <footer class="footer">
            <div>LexPraxis IA</div>
            <div class="footer-links">
                <a href="{{ route('home') }}">Página inicial</a>
                <a href="{{ route('legal.privacy') }}">Política de Privacidade</a>
                <a href="{{ route('legal.terms') }}">Termos de Serviço</a>
                <a href="{{ route('login') }}">Entrar</a>
            </div>
        </footer>
    </div>
</body>
</html>
