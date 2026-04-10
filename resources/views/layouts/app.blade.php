<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LexPraxis IA - @yield('title')</title>
    <link rel="icon" type="image/png" href="{{ asset('logo.png') }}">
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    <meta name="theme-color" content="#0f172a">
    <script>
        (function () {
            const preferredTheme = window.matchMedia("(prefers-color-scheme: dark)").matches ? "darkly" : "flatly";
            const theme = localStorage.getItem("lp_theme") || preferredTheme;
            const darkThemes = ["cyborg", "darkly", "quartz", "slate", "solar", "superhero", "vapor"];
            document.documentElement.setAttribute("data-theme-name", theme);
            document.documentElement.setAttribute("data-theme-mode", darkThemes.includes(theme) ? "dark" : "light");
            document.write('<link id="themeStylesheet" rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/' + theme + '/bootstrap.min.css">');
        })();
    </script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --lp-radius-xl: 28px;
            --lp-radius-lg: 22px;
            --lp-shadow: 0 28px 80px rgba(15, 23, 42, 0.18);
            --lp-sidebar-width: 300px;
        }

        html, body {
            min-height: 100%;
        }

        body {
            font-family: "Manrope", sans-serif;
            background:
                radial-gradient(circle at top left, rgba(59, 130, 246, 0.18), transparent 28%),
                radial-gradient(circle at top right, rgba(14, 165, 233, 0.16), transparent 24%),
                linear-gradient(180deg, color-mix(in srgb, var(--bs-body-bg) 94%, #ffffff 6%) 0%, var(--bs-body-bg) 100%);
            color: var(--bs-body-color);
        }

        [data-theme-mode="dark"] body {
            background:
                radial-gradient(circle at top left, rgba(37, 99, 235, 0.24), transparent 28%),
                radial-gradient(circle at top right, rgba(6, 182, 212, 0.18), transparent 22%),
                linear-gradient(180deg, #081120 0%, #0f172a 100%);
        }

        .app-shell {
            display: flex;
            min-height: 100vh;
        }

        .sidebar {
            width: var(--lp-sidebar-width);
            min-height: 100vh;
            padding: 1.5rem;
            background: color-mix(in srgb, var(--bs-body-bg) 80%, var(--bs-tertiary-bg) 20%);
            border-right: 1px solid color-mix(in srgb, var(--bs-border-color) 75%, transparent 25%);
        }

        [data-theme-mode="dark"] .sidebar {
            background: rgba(8, 15, 28, 0.78);
            backdrop-filter: blur(22px);
        }

        .brand-card,
        .topbar,
        .content-card,
        .feature-panel,
        .metric-card,
        .card {
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%);
            border-radius: var(--lp-radius-lg);
            background: color-mix(in srgb, var(--bs-body-bg) 84%, white 16%);
            box-shadow: var(--lp-shadow);
        }

        [data-theme-mode="dark"] .brand-card,
        [data-theme-mode="dark"] .topbar,
        [data-theme-mode="dark"] .content-card,
        [data-theme-mode="dark"] .feature-panel,
        [data-theme-mode="dark"] .metric-card,
        [data-theme-mode="dark"] .card {
            background: rgba(15, 23, 42, 0.78);
            box-shadow: 0 28px 80px rgba(2, 6, 23, 0.42);
        }

        .brand-card {
            padding: 1.25rem;
            margin-bottom: 1.5rem;
        }

        .sidebar-logo {
            display: block;
            width: 150px;
            max-width: 100%;
            margin-bottom: 0.85rem;
            filter: drop-shadow(0 14px 24px rgba(56, 189, 248, 0.22));
        }

        .brand-kicker {
            margin-bottom: 0.35rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.16em;
            color: var(--bs-primary);
            opacity: 0.9;
        }

        .brand-title {
            margin: 0;
            font-size: 1.35rem;
            font-weight: 800;
        }

        .brand-copy {
            margin: 0.45rem 0 0;
            color: var(--bs-secondary-color);
            font-size: 0.93rem;
            line-height: 1.55;
        }

        .sidebar .nav {
            gap: 0.35rem;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 0.85rem;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            color: var(--bs-body-color);
            font-weight: 700;
            transition: transform 0.18s ease, background-color 0.18s ease, border-color 0.18s ease;
            border: 1px solid transparent;
        }

        .sidebar .nav-link:hover {
            background: color-mix(in srgb, var(--bs-primary) 10%, transparent 90%);
            border-color: color-mix(in srgb, var(--bs-primary) 24%, transparent 76%);
            transform: translateX(4px);
        }

        .sidebar .nav-link.active {
            color: #fff;
            background: linear-gradient(135deg, var(--bs-primary), color-mix(in srgb, var(--bs-primary) 72%, #22c55e 28%));
            box-shadow: 0 18px 34px rgba(37, 99, 235, 0.22);
        }

        .main-panel {
            flex: 1;
            min-width: 0;
            padding: 1.25rem;
        }

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            padding: 1rem 1.25rem;
            margin-bottom: 1.25rem;
        }

        .topbar-meta {
            display: flex;
            flex-direction: column;
            gap: 0.15rem;
        }

        .topbar-meta strong {
            font-size: 1rem;
            font-weight: 800;
        }

        .topbar-meta span {
            color: var(--bs-secondary-color);
            font-size: 0.9rem;
        }

        .topbar-actions {
            display: flex;
            align-items: center;
            gap: 0.85rem;
        }

        .theme-toggle {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.7rem 1rem;
            border-radius: 999px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
            background: color-mix(in srgb, var(--bs-body-bg) 74%, var(--bs-tertiary-bg) 26%);
            color: var(--bs-body-color);
            cursor: pointer;
        }

        .theme-toggle i {
            color: var(--bs-primary);
        }

        .theme-toggle-label {
            display: flex;
            flex-direction: column;
            gap: 0.1rem;
            line-height: 1.1;
            text-align: left;
        }

        .theme-toggle-label strong {
            font-size: 0.9rem;
            font-weight: 800;
        }

        .theme-toggle-label span {
            font-size: 0.76rem;
            color: var(--bs-secondary-color);
        }

        .theme-current-name {
            text-transform: capitalize;
        }

        .theme-picker {
            position: relative;
        }

        .theme-panel {
            position: absolute;
            top: calc(100% + 0.75rem);
            right: 0;
            width: min(28rem, calc(100vw - 2rem));
            padding: 1rem;
            border-radius: 24px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%);
            background: color-mix(in srgb, var(--bs-body-bg) 90%, var(--bs-tertiary-bg) 10%);
            box-shadow: 0 28px 80px rgba(15, 23, 42, 0.2);
            opacity: 0;
            pointer-events: none;
            transform: translateY(-6px);
            transition: opacity 0.18s ease, transform 0.18s ease;
            z-index: 1050;
        }

        .theme-panel.is-open {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        .theme-panel-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 1rem;
            margin-bottom: 0.9rem;
        }

        .theme-panel-header h6 {
            margin: 0;
            font-weight: 800;
        }

        .theme-panel-header p {
            margin: 0.2rem 0 0;
            color: var(--bs-secondary-color);
            font-size: 0.84rem;
        }

        .theme-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            max-height: 22rem;
            overflow: auto;
            padding-right: 0.15rem;
        }

        .theme-option {
            width: 100%;
            padding: 0.8rem;
            border-radius: 18px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%);
            background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%);
            text-align: left;
            color: var(--bs-body-color);
            transition: transform 0.16s ease, border-color 0.16s ease, box-shadow 0.16s ease;
        }

        .theme-option:hover {
            transform: translateY(-2px);
            border-color: color-mix(in srgb, var(--bs-primary) 36%, transparent 64%);
            box-shadow: 0 18px 34px rgba(15, 23, 42, 0.12);
        }

        .theme-option.is-active {
            border-color: color-mix(in srgb, var(--bs-primary) 58%, transparent 42%);
            box-shadow: 0 0 0 0.22rem color-mix(in srgb, var(--bs-primary) 16%, transparent 84%);
        }

        .theme-swatch {
            display: flex;
            gap: 0.35rem;
            margin-bottom: 0.65rem;
        }

        .theme-swatch span {
            display: block;
            width: 100%;
            height: 0.62rem;
            border-radius: 999px;
        }

        .theme-name {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 0.5rem;
            font-weight: 800;
            font-size: 0.9rem;
        }

        .theme-kind {
            font-size: 0.72rem;
            font-weight: 800;
            letter-spacing: 0.06em;
            text-transform: uppercase;
            color: var(--bs-secondary-color);
        }

        .content-area {
            padding: 0;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1.5rem;
        }

        .page-title {
            margin: 0;
            font-size: clamp(1.6rem, 1.4rem + 0.8vw, 2.2rem);
            font-weight: 800;
        }

        .page-subtitle {
            margin: 0.35rem 0 0;
            color: var(--bs-secondary-color);
        }

        .toolbar-form {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
            align-items: center;
            margin-bottom: 1.25rem;
        }

        .section-title {
            margin: 0 0 1rem;
            padding-bottom: 0.75rem;
            border-bottom: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%);
            font-size: 1rem;
            font-weight: 800;
            letter-spacing: 0.01em;
        }

        .form-section + .form-section {
            margin-top: 1.75rem;
        }

        .form-label {
            margin-bottom: 0.55rem;
            font-size: 0.82rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
            color: var(--bs-secondary-color);
        }

        .card,
        .content-card,
        .metric-card,
        .feature-panel {
            padding: 1.5rem;
        }

        .metric-card {
            height: 100%;
            overflow: hidden;
        }

        .metric-icon {
            width: 3.25rem;
            height: 3.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 18px;
            font-size: 1.4rem;
            background: color-mix(in srgb, var(--bs-primary) 12%, transparent 88%);
            color: var(--bs-primary);
        }

        .dashboard-hero {
            position: relative;
            overflow: hidden;
            padding: 1.75rem;
            border-radius: var(--lp-radius-xl);
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
            background:
                radial-gradient(circle at top right, color-mix(in srgb, var(--bs-primary) 18%, transparent 82%), transparent 26%),
                linear-gradient(135deg, color-mix(in srgb, var(--bs-body-bg) 90%, var(--bs-tertiary-bg) 10%), color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%));
            box-shadow: var(--lp-shadow);
        }

        .dashboard-hero::after {
            content: "";
            position: absolute;
            inset: auto -10% -35% auto;
            width: 18rem;
            height: 18rem;
            border-radius: 50%;
            background: color-mix(in srgb, var(--bs-primary) 14%, transparent 86%);
            filter: blur(20px);
            pointer-events: none;
        }

        .dashboard-kicker,
        .chat-kicker {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            margin-bottom: 0.9rem;
            padding: 0.45rem 0.8rem;
            border-radius: 999px;
            background: color-mix(in srgb, var(--bs-primary) 12%, transparent 88%);
            color: var(--bs-primary);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-stat-grid {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 1rem;
            margin-top: 1.5rem;
        }

        .hero-stat {
            padding: 1rem;
            border-radius: 18px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 70%, transparent 30%);
            background: color-mix(in srgb, var(--bs-body-bg) 76%, var(--bs-tertiary-bg) 24%);
        }

        .hero-stat-label {
            margin-bottom: 0.45rem;
            color: var(--bs-secondary-color);
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .hero-stat-value {
            font-size: clamp(1.4rem, 1.1rem + 0.8vw, 2rem);
            font-weight: 800;
            line-height: 1;
        }

        .spotlight-panel {
            height: 100%;
            padding: 1.5rem;
            border-radius: var(--lp-radius-lg);
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
            background: color-mix(in srgb, var(--bs-body-bg) 84%, var(--bs-tertiary-bg) 16%);
            box-shadow: var(--lp-shadow);
        }

        .spotlight-list {
            display: grid;
            gap: 0.85rem;
            margin: 1rem 0 0;
            padding: 0;
            list-style: none;
        }

        .spotlight-item {
            display: flex;
            align-items: flex-start;
            gap: 0.8rem;
            padding: 0.95rem 1rem;
            border-radius: 18px;
            background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%);
        }

        .spotlight-item i {
            color: var(--bs-primary);
        }

        .quick-action-card {
            height: 100%;
            padding: 1.2rem;
            border-radius: 18px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%);
            background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%);
            text-decoration: none;
            color: var(--bs-body-color);
            box-shadow: 0 16px 34px rgba(15, 23, 42, 0.08);
            transition: transform 0.16s ease, border-color 0.16s ease;
        }

        .quick-action-card:hover {
            transform: translateY(-3px);
            border-color: color-mix(in srgb, var(--bs-primary) 38%, transparent 62%);
            color: var(--bs-body-color);
        }

        .quick-action-card i {
            display: inline-flex;
            width: 2.8rem;
            height: 2.8rem;
            align-items: center;
            justify-content: center;
            margin-bottom: 0.9rem;
            border-radius: 16px;
            background: color-mix(in srgb, var(--bs-primary) 12%, transparent 88%);
            color: var(--bs-primary);
            font-size: 1.2rem;
        }

        .chat-shell {
            display: grid;
            grid-template-columns: minmax(0, 1fr) 20rem;
            gap: 1rem;
            min-height: calc(100vh - 230px);
        }

        .chat-stage {
            display: flex;
            flex-direction: column;
            min-height: 0;
        }

        .chat-intro-card,
        .chat-side-card {
            padding: 1.25rem;
            border-radius: 20px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%);
            background: color-mix(in srgb, var(--bs-body-bg) 82%, var(--bs-tertiary-bg) 18%);
        }

        .chat-intro-card {
            margin: 1rem;
            margin-bottom: 0;
        }

        .chat-prompt-grid {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 0.75rem;
            margin-top: 1rem;
        }

        .chat-prompt {
            width: 100%;
            padding: 0.9rem 1rem;
            border-radius: 16px;
            border: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%);
            background: color-mix(in srgb, var(--bs-body-bg) 74%, var(--bs-tertiary-bg) 26%);
            color: var(--bs-body-color);
            text-align: left;
            transition: transform 0.16s ease, border-color 0.16s ease;
        }

        .chat-prompt:hover {
            transform: translateY(-2px);
            border-color: color-mix(in srgb, var(--bs-primary) 42%, transparent 58%);
        }

        .chat-box {
            padding: 1.25rem 1.25rem 0;
            flex-grow: 1;
            overflow: auto;
            background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%);
        }

        .message-row {
            display: flex;
            margin-bottom: 1rem;
        }

        .message-row.user {
            justify-content: flex-end;
        }

        .message-bubble {
            max-width: min(78%, 48rem);
            padding: 1rem 1.1rem;
            border-radius: 22px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
            white-space: pre-wrap;
        }

        .message-bubble.assistant {
            background: var(--bs-primary);
            color: #fff;
            border-top-left-radius: 8px;
        }

        .message-bubble.user {
            background: color-mix(in srgb, var(--bs-primary) 10%, var(--bs-body-bg) 90%);
            border: 1px solid color-mix(in srgb, var(--bs-primary) 20%, transparent 80%);
            color: var(--bs-body-color);
            border-top-right-radius: 8px;
        }

        .message-bubble.error {
            background: var(--bs-danger);
            color: #fff;
            border-top-left-radius: 8px;
        }

        .chat-composer {
            padding: 1rem 1.25rem 1.25rem;
            border-top: 1px solid color-mix(in srgb, var(--bs-border-color) 78%, transparent 22%);
            background: color-mix(in srgb, var(--bs-body-bg) 88%, var(--bs-tertiary-bg) 12%);
        }

        .chat-composer .form-control {
            min-height: 3.4rem;
        }

        .chat-side-stack {
            display: grid;
            gap: 1rem;
        }

        .chat-side-list {
            display: grid;
            gap: 0.7rem;
            margin: 1rem 0 0;
            padding: 0;
            list-style: none;
        }

        .chat-side-list li {
            display: flex;
            gap: 0.75rem;
            align-items: flex-start;
            padding: 0.85rem 0.95rem;
            border-radius: 16px;
            background: color-mix(in srgb, var(--bs-body-bg) 74%, var(--bs-tertiary-bg) 26%);
        }

        [data-theme-name="vapor"] .brand-card,
        [data-theme-name="vapor"] .topbar,
        [data-theme-name="vapor"] .content-card,
        [data-theme-name="vapor"] .feature-panel,
        [data-theme-name="vapor"] .metric-card,
        [data-theme-name="vapor"] .spotlight-panel,
        [data-theme-name="vapor"] .dashboard-hero,
        [data-theme-name="vapor"] .chat-intro-card,
        [data-theme-name="vapor"] .chat-side-card {
            background: rgba(28, 10, 58, 0.88);
            border-color: rgba(115, 76, 255, 0.42);
        }

        [data-theme-name="vapor"] .table thead th,
        [data-theme-name="vapor"] .chat-composer,
        [data-theme-name="vapor"] .theme-panel {
            background: rgba(35, 14, 70, 0.94);
        }

        [data-theme-name="solar"] .brand-card,
        [data-theme-name="solar"] .topbar,
        [data-theme-name="solar"] .content-card,
        [data-theme-name="solar"] .feature-panel,
        [data-theme-name="solar"] .metric-card,
        [data-theme-name="solar"] .spotlight-panel,
        [data-theme-name="solar"] .dashboard-hero,
        [data-theme-name="solar"] .chat-intro-card,
        [data-theme-name="solar"] .chat-side-card {
            background: rgba(10, 46, 55, 0.92);
            border-color: rgba(132, 153, 0, 0.34);
        }

        [data-theme-name="solar"] .table thead th,
        [data-theme-name="solar"] .chat-composer,
        [data-theme-name="solar"] .theme-panel {
            background: rgba(6, 40, 48, 0.96);
        }

        [data-theme-name="sketchy"] .brand-card,
        [data-theme-name="sketchy"] .topbar,
        [data-theme-name="sketchy"] .content-card,
        [data-theme-name="sketchy"] .feature-panel,
        [data-theme-name="sketchy"] .metric-card,
        [data-theme-name="sketchy"] .spotlight-panel,
        [data-theme-name="sketchy"] .dashboard-hero,
        [data-theme-name="sketchy"] .chat-intro-card,
        [data-theme-name="sketchy"] .chat-side-card {
            background: rgba(255, 255, 255, 0.95);
            border-width: 2px;
            border-color: rgba(60, 60, 60, 0.3);
            box-shadow: 0 14px 0 rgba(0, 0, 0, 0.08);
        }

        [data-theme-name="sketchy"] .table thead th,
        [data-theme-name="sketchy"] .chat-composer,
        [data-theme-name="sketchy"] .theme-panel {
            background: rgba(250, 248, 243, 0.98);
        }

        .table,
        .table > :not(caption) > * > * {
            color: var(--bs-body-color);
            border-color: color-mix(in srgb, var(--bs-border-color) 72%, transparent 28%);
        }

        .table thead th {
            padding-top: 0.95rem;
            padding-bottom: 0.95rem;
            font-size: 0.78rem;
            font-weight: 800;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: var(--bs-secondary-color);
            background: color-mix(in srgb, var(--bs-body-bg) 65%, var(--bs-tertiary-bg) 35%);
        }

        .table tbody td {
            padding-top: 1rem;
            padding-bottom: 1rem;
            vertical-align: middle;
        }

        .table-hover tbody tr:hover {
            background: color-mix(in srgb, var(--bs-primary) 8%, transparent 92%);
        }

        .inline-meta {
            display: inline-flex;
            align-items: center;
            gap: 0.45rem;
            color: var(--bs-secondary-color);
            font-size: 0.86rem;
        }

        .entity-link {
            color: var(--bs-body-color);
            font-weight: 800;
            text-decoration: none;
        }

        .entity-link:hover {
            color: var(--bs-primary);
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            padding: 0.45rem 0.75rem;
            border-radius: 999px;
            font-size: 0.76rem;
            font-weight: 800;
            letter-spacing: 0.04em;
            text-transform: uppercase;
        }

        .action-buttons {
            display: inline-flex;
            gap: 0.5rem;
        }

        .action-btn {
            width: 2.25rem;
            height: 2.25rem;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            border-radius: 12px;
        }

        .empty-state {
            padding: 2rem 1rem !important;
            color: var(--bs-secondary-color) !important;
        }

        .form-control,
        .form-select,
        .input-group-text {
            border-radius: 14px;
            border-color: color-mix(in srgb, var(--bs-border-color) 80%, transparent 20%);
            background: color-mix(in srgb, var(--bs-body-bg) 72%, var(--bs-tertiary-bg) 28%);
            color: var(--bs-body-color);
        }

        .form-control:focus,
        .form-select:focus {
            background: color-mix(in srgb, var(--bs-body-bg) 78%, var(--bs-tertiary-bg) 22%);
            color: var(--bs-body-color);
            border-color: color-mix(in srgb, var(--bs-primary) 60%, white 40%);
            box-shadow: 0 0 0 0.25rem color-mix(in srgb, var(--bs-primary) 18%, transparent 82%);
        }

        .btn {
            border-radius: 14px;
            font-weight: 700;
        }

        .dropdown-menu {
            border-radius: 18px;
            border-color: color-mix(in srgb, var(--bs-border-color) 82%, transparent 18%);
            background: color-mix(in srgb, var(--bs-body-bg) 84%, var(--bs-tertiary-bg) 16%);
        }

        .dropdown-item {
            color: var(--bs-body-color);
        }

        .alert {
            border: 0;
            border-radius: 16px;
            box-shadow: 0 18px 40px rgba(15, 23, 42, 0.08);
        }

        @media (max-width: 991.98px) {
            .app-shell {
                flex-direction: column;
            }

            .sidebar {
                width: 100%;
                min-height: auto;
            }

            .main-panel {
                padding-top: 0;
            }

            .topbar {
                flex-direction: column;
                align-items: flex-start;
            }

            .topbar-actions {
                width: 100%;
                justify-content: space-between;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .hero-stat-grid,
            .chat-prompt-grid,
            .chat-shell {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="app-shell">
        <aside class="sidebar d-flex flex-column">
            <div class="brand-card">
                <img src="{{ asset('logo.png') }}" alt="Logo" class="sidebar-logo">
                <div class="brand-kicker">Plataforma jurídica</div>
                <h1 class="brand-title">LexPraxis IA</h1>
                <p class="brand-copy">Operação jurídica com visual limpo, contraste correto e seleção completa de temas Bootswatch para cada perfil de uso.</p>
            </div>
            <ul class="nav nav-pills flex-column mb-auto">
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                        <i class="bi bi-speedometer2 me-2"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="{{ route('clientes.index') }}" class="nav-link {{ request()->routeIs('clientes.*') ? 'active' : '' }}">
                        <i class="bi bi-people me-2"></i> Clientes
                    </a>
                </li>
                <li>
                    <a href="{{ route('processos.index') }}" class="nav-link {{ request()->routeIs('processos.*') ? 'active' : '' }}">
                        <i class="bi bi-folder me-2"></i> Processos
                    </a>
                </li>
                <li>
                    <a href="{{ route('agendas.index') }}" class="nav-link {{ request()->routeIs('agendas.*') ? 'active' : '' }}">
                        <i class="bi bi-calendar-event me-2"></i> Agenda
                    </a>
                </li>
                <li>
                    <a href="{{ route('prazos.index') }}" class="nav-link {{ request()->routeIs('prazos.*') ? 'active' : '' }}">
                        <i class="bi bi-clock-history me-2"></i> Prazos
                    </a>
                </li>
                <li>
                    <a href="{{ route('tarefas.index') }}" class="nav-link {{ request()->routeIs('tarefas.*') ? 'active' : '' }}">
                        <i class="bi bi-check2-square me-2"></i> Tarefas
                    </a>
                </li>
                <li>
                    <a href="{{ route('documentos.index') }}" class="nav-link {{ request()->routeIs('documentos.*') ? 'active' : '' }}">
                        <i class="bi bi-file-earmark-text me-2"></i> Documentos
                    </a>
                </li>
                <li>
                    <a href="{{ route('chat.index') }}" class="nav-link {{ request()->routeIs('chat.*') ? 'active' : '' }}">
                        <i class="bi bi-chat-dots me-2"></i> Chat IA
                    </a>
                </li>
                <li>
                    <a href="{{ route('credits.index') }}" class="nav-link {{ request()->routeIs('credits.*') ? 'active' : '' }}">
                        <i class="bi bi-cash-coin me-2"></i> Créditos
                    </a>
                </li>
            </ul>
        </aside>

        <div class="main-panel d-flex flex-column">
            <header class="topbar">
                <div class="topbar-meta">
                    <strong>@yield('title')</strong>
                    <span>{{ now()->format('d/m/Y') }} • {{ auth()->user()->consulta_credits ?? 0 }} crédito(s) disponível(is)</span>
                </div>
                <div class="topbar-actions">
                    <div class="theme-picker">
                        <button type="button" class="theme-toggle mb-0" id="themePickerButton" aria-expanded="false" aria-controls="themePanel">
                            <i class="bi bi-brush"></i>
                            <span class="theme-toggle-label">
                                <strong>Tema visual</strong>
                                <span class="theme-current-name" id="themeCurrentName">Darkly</span>
                            </span>
                            <i class="bi bi-chevron-down"></i>
                        </button>
                        <div class="theme-panel" id="themePanel" role="dialog" aria-label="Selecionar tema">
                            <div class="theme-panel-header">
                                <div>
                                    <h6>Bootswatch completo</h6>
                                    <p>Escolha qualquer tema oficial e mantenha a preferência salva para todas as telas.</p>
                                </div>
                            </div>
                            <div class="theme-grid" id="themeGrid"></div>
                        </div>
                    </div>
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-body text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <strong>{{ auth()->user()->name }}</strong>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="dropdownUser1">
                        <li><a class="dropdown-item" href="#">Configurações</a></li>
                        <li><a class="dropdown-item" href="#">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">Sair</button>
                            </form>
                        </li>
                    </ul>
                </div>
                </div>
            </header>

            <main class="content-area">
                @yield('content')
            </main>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        (function () {
            const themes = [
                { name: "cerulean", label: "Cerulean", kind: "claro", swatches: ["#033c73", "#2fa4e7", "#ffffff"] },
                { name: "cosmo", label: "Cosmo", kind: "claro", swatches: ["#2780e3", "#373a3c", "#ffffff"] },
                { name: "cyborg", label: "Cyborg", kind: "escuro", swatches: ["#060606", "#2a9fd6", "#adafae"] },
                { name: "darkly", label: "Darkly", kind: "escuro", swatches: ["#375a7f", "#00bc8c", "#222222"] },
                { name: "flatly", label: "Flatly", kind: "claro", swatches: ["#2c3e50", "#18bc9c", "#ffffff"] },
                { name: "journal", label: "Journal", kind: "claro", swatches: ["#eb6864", "#4e5d6c", "#ffffff"] },
                { name: "litera", label: "Litera", kind: "claro", swatches: ["#4582ec", "#adb5bd", "#ffffff"] },
                { name: "lumen", label: "Lumen", kind: "claro", swatches: ["#158cba", "#f6f6f6", "#ffffff"] },
                { name: "lux", label: "Lux", kind: "claro", swatches: ["#1a1a1a", "#f8f9fa", "#ffffff"] },
                { name: "materia", label: "Materia", kind: "claro", swatches: ["#2196f3", "#ff9800", "#ffffff"] },
                { name: "minty", label: "Minty", kind: "claro", swatches: ["#78c2ad", "#f3969a", "#ffffff"] },
                { name: "morph", label: "Morph", kind: "claro", swatches: ["#6e00ff", "#e5d9ff", "#f7f4ff"] },
                { name: "pulse", label: "Pulse", kind: "claro", swatches: ["#593196", "#ff7851", "#ffffff"] },
                { name: "quartz", label: "Quartz", kind: "escuro", swatches: ["#2b1f5f", "#e83283", "#170f2f"] },
                { name: "sandstone", label: "Sandstone", kind: "claro", swatches: ["#325d88", "#93c54b", "#f8f5f0"] },
                { name: "simplex", label: "Simplex", kind: "claro", swatches: ["#d9230f", "#469408", "#ffffff"] },
                { name: "sketchy", label: "Sketchy", kind: "claro", swatches: ["#333333", "#17a2b8", "#ffffff"] },
                { name: "slate", label: "Slate", kind: "escuro", swatches: ["#3a3f44", "#7a8288", "#272b30"] },
                { name: "solar", label: "Solar", kind: "escuro", swatches: ["#b58900", "#2aa198", "#002b36"] },
                { name: "spacelab", label: "Spacelab", kind: "claro", swatches: ["#446e9b", "#d9534f", "#ffffff"] },
                { name: "superhero", label: "Superhero", kind: "escuro", swatches: ["#df691a", "#5bc0de", "#2b3e50"] },
                { name: "united", label: "United", kind: "claro", swatches: ["#e95420", "#772953", "#ffffff"] },
                { name: "vapor", label: "Vapor", kind: "escuro", swatches: ["#6f42c1", "#32fbe2", "#190831"] },
                { name: "yeti", label: "Yeti", kind: "claro", swatches: ["#008cba", "#43ac6a", "#ffffff"] },
                { name: "zephyr", label: "Zephyr", kind: "claro", swatches: ["#3459e6", "#2fb380", "#ffffff"] }
            ];
            const stylesheet = document.getElementById("themeStylesheet");
            const pickerButton = document.getElementById("themePickerButton");
            const panel = document.getElementById("themePanel");
            const grid = document.getElementById("themeGrid");
            const currentName = document.getElementById("themeCurrentName");
            const themeColor = document.querySelector('meta[name="theme-color"]');

            if (!pickerButton || !panel || !grid || !currentName || !stylesheet) return;

            const darkThemes = new Set(themes.filter((theme) => theme.kind === "escuro").map((theme) => theme.name));

            const setPanelState = function (isOpen) {
                panel.classList.toggle("is-open", isOpen);
                pickerButton.setAttribute("aria-expanded", isOpen ? "true" : "false");
            };

            const renderGrid = function () {
                grid.innerHTML = themes.map((theme) => `
                    <button type="button" class="theme-option" data-theme="${theme.name}">
                        <span class="theme-swatch">
                            ${theme.swatches.map((color) => `<span style="background:${color}"></span>`).join("")}
                        </span>
                        <span class="theme-name">
                            <span>${theme.label}</span>
                            <span class="theme-kind">${theme.kind}</span>
                        </span>
                    </button>
                `).join("");
            };

            const updateActiveOption = function (themeName) {
                Array.from(grid.querySelectorAll(".theme-option")).forEach((option) => {
                    option.classList.toggle("is-active", option.dataset.theme === themeName);
                });
            };

            const applyTheme = function (themeName) {
                const theme = themes.find((item) => item.name === themeName) || themes.find((item) => item.name === "darkly");
                const mode = darkThemes.has(theme.name) ? "dark" : "light";
                document.documentElement.setAttribute("data-theme-name", theme.name);
                document.documentElement.setAttribute("data-theme-mode", mode);
                stylesheet.href = `https://cdn.jsdelivr.net/npm/bootswatch@5.3.3/dist/${theme.name}/bootstrap.min.css`;
                currentName.textContent = theme.label;
                localStorage.setItem("lp_theme", theme.name);
                updateActiveOption(theme.name);

                if (themeColor) {
                    themeColor.setAttribute("content", mode === "dark" ? "#0f172a" : "#f8fafc");
                }
            };

            renderGrid();
            applyTheme(localStorage.getItem("lp_theme") || document.documentElement.getAttribute("data-theme-name") || "darkly");

            pickerButton.addEventListener("click", function () {
                setPanelState(!panel.classList.contains("is-open"));
            });

            grid.addEventListener("click", function (event) {
                const option = event.target.closest(".theme-option");
                if (!option) return;
                applyTheme(option.dataset.theme);
                setPanelState(false);
            });

            document.addEventListener("click", function (event) {
                if (!event.target.closest(".theme-picker")) {
                    setPanelState(false);
                }
            });
        })();
    </script>
    @yield('scripts')
</body>
</html>
