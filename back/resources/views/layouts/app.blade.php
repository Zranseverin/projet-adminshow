<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="{{ asset('logo-librairie-dominion.png') }}?v=1">
    <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Chapitre') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Lora:wght@400;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --navy: #1C2B3A;
            --ink: #2E3F50;
            --sand: #F7F3EE;
            --cream: #FDFAF6;
            --amber: #C8832A;
            --amber-light: #F4E4CC;
            --red: #C23B2E;
            --red-bg: #FEF0F0;
            --green: #2E6B3E;
            --green-bg: #E8F4EC;
            --blue: #2563EB;
            --blue-bg: #EFF6FF;
            --purple: #7C3AED;
            --purple-bg: #F5F3FF;
            --border: #DDD8D0;
            --bg: #F4F6F9;
            --text: #2E3F50;
            --muted: #7A8694;
            --light: #B0BAC4;
            --white: #FFFFFF;
            --shadow: 0 1px 6px rgba(28, 43, 58, .08);
            --shadow-md: 0 4px 16px rgba(28, 43, 58, .1);
            --font-serif: 'Lora', Georgia, serif;
            --font-sans: 'Inter', system-ui, sans-serif;
            --radius: 6px;
            --radius-lg: 10px;
            --sidebar: 240px;
        }

        body {
            min-height: 100vh;
            display: flex;
            background: var(--bg);
            color: var(--text);
            font-family: var(--font-sans);
        }

        a { color: inherit; text-decoration: none; }
        button, input, select { font: inherit; }
        svg { flex-shrink: 0; }

        .sidebar {
            width: var(--sidebar);
            min-width: var(--sidebar);
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
            display: flex;
            flex-direction: column;
            background: var(--navy);
        }
        .sb-brand {
            min-height: 126px;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px 16px;
            border-bottom: 1px solid rgba(255, 255, 255, .08);
        }
        .sb-brand-logo { width: 104px; height: 104px; display: block; }
        .sb-brand-logo img { width: 100%; height: 100%; display: block; object-fit: contain; }        .sb-logo {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--white);
            font-family: var(--font-serif);
            font-size: 18px;
            font-weight: 600;
        }
        .sb-logo svg { color: var(--amber); }
        .sb-badge {
            padding: 2px 7px;
            border-radius: 10px;
            background: var(--amber);
            color: var(--white);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: .5px;
        }
        .sb-nav { flex: 1; padding: 14px 10px; }
        .sb-section {
            padding: 10px 10px 6px;
            color: rgba(255, 255, 255, .3);
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        .sb-item {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 2px;
            padding: 9px 12px;
            border-radius: var(--radius);
            color: rgba(255, 255, 255, .68);
            font-size: 13.5px;
            font-weight: 500;
            transition: background .15s, color .15s;
        }
        .sb-item:hover, .sb-item.active {
            background: rgba(200, 131, 42, .2);
            color: var(--white);
        }
        .sb-item.active svg { color: var(--amber); }
        .sb-item svg { color: rgba(255, 255, 255, .4); }
        .sb-badge-count {
            margin-left: auto;
            padding: 1px 6px;
            border-radius: 10px;
            background: var(--amber);
            color: var(--white);
            font-size: 10px;
            font-weight: 700;
        }
        .sb-footer {
            padding: 14px;
            border-top: 1px solid rgba(255, 255, 255, .08);
        }
        .sb-user { display: flex; align-items: center; gap: 10px; }
        .sb-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--amber);
            color: var(--white);
            font-size: 12px;
            font-weight: 700;
        }
        .sb-name { color: var(--white); font-size: 13px; font-weight: 600; }
        .sb-role { color: rgba(255, 255, 255, .4); font-size: 11px; }

        .main {
            flex: 1;
            min-width: 0;
            display: flex;
            flex-direction: column;
        }
        .topbar {
            height: 60px;
            position: sticky;
            top: 0;
            z-index: 100;
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 0 28px;
            border-bottom: 1px solid var(--border);
            background: var(--white);
            box-shadow: var(--shadow);
        }
        .account-menu { position: relative; }
        .account-menu summary { min-height: 42px; display: flex; align-items: center; gap: 9px; padding: 4px 8px; border-radius: var(--radius); cursor: pointer; list-style: none; }
        .account-menu summary::-webkit-details-marker { display: none; }
        .account-menu summary:hover { background: var(--sand); }
        .account-avatar { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 50%; color: var(--white); background: var(--amber); font-size: 11px; font-weight: 700; }
        .account-copy { min-width: 105px; display: flex; flex-direction: column; line-height: 1.25; }
        .account-copy strong { max-width: 150px; overflow: hidden; color: var(--navy); font-size: 11.5px; text-overflow: ellipsis; white-space: nowrap; }
        .account-copy small { color: var(--muted); font-size: 9.5px; }
        .account-dropdown { width: 245px; position: absolute; top: calc(100% + 9px); right: 0; z-index: 200; padding: 7px; border: 1px solid var(--border); border-radius: 7px; background: var(--white); box-shadow: var(--shadow-md); }
        .account-dropdown-head { display: flex; flex-direction: column; gap: 3px; padding: 10px 11px 12px; border-bottom: 1px solid var(--border); }
        .account-dropdown-head strong { color: var(--navy); font-size: 12px; }
        .account-dropdown-head span { overflow: hidden; color: var(--muted); font-size: 10.5px; text-overflow: ellipsis; }
        .account-dropdown a, .account-dropdown button { width: 100%; min-height: 36px; display: flex; align-items: center; gap: 9px; padding: 8px 10px; border: 0; border-radius: 5px; color: var(--text); background: transparent; cursor: pointer; font-size: 11.5px; text-align: left; }
        .account-dropdown a:hover, .account-dropdown button:hover { background: var(--sand); color: var(--navy); }
        .account-dropdown form { margin-top: 5px; padding-top: 5px; border-top: 1px solid var(--border); }
        .account-dropdown form button { color: var(--red); }        .topbar-title {
            flex: 1;
            color: var(--navy);
            font-size: 16px;
            font-weight: 600;
        }
        .topbar-actions { display: flex; align-items: center; gap: 10px; }
        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 7px;
            min-height: 34px;
            padding: 8px 16px;
            border: 0;
            border-radius: var(--radius);
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: background .15s, color .15s, border-color .15s;
        }
        .btn-sm { padding: 6px 12px; font-size: 12px; }
        .btn-amber { background: var(--amber); color: var(--white); }
        .btn-amber:hover { background: #B5721F; }
        .btn-ghost {
            border: 1.5px solid var(--border);
            background: transparent;
            color: var(--muted);
        }
        .btn-ghost:hover { border-color: var(--navy); color: var(--navy); }

        .content { flex: 1; padding: 28px; }
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 28px;
        }
        .stat-card {
            position: relative;
            overflow: hidden;
            padding: 20px;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--white);
            box-shadow: var(--shadow);
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: -12px;
            right: -12px;
            width: 70px;
            height: 70px;
            border-radius: 50%;
            opacity: .08;
        }
        .stat-card.blue::before { background: var(--blue); }
        .stat-card.amber::before { background: var(--amber); }
        .stat-card.green::before { background: var(--green); }
        .stat-card.purple::before { background: var(--purple); }
        .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 12px;
        }
        .stat-icon.blue { background: var(--blue-bg); color: var(--blue); }
        .stat-icon.amber { background: var(--amber-light); color: var(--amber); }
        .stat-icon.green { background: var(--green-bg); color: var(--green); }
        .stat-icon.purple { background: var(--purple-bg); color: var(--purple); }
        .stat-value {
            color: var(--navy);
            font-family: var(--font-serif);
            font-size: 28px;
            font-weight: 600;
            line-height: 1;
        }
        .stat-label { margin-top: 4px; color: var(--muted); font-size: 13px; }
        .stat-delta {
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 8px;
            font-size: 12px;
        }
        .stat-delta.up { color: var(--green); }
        .stat-delta.down { color: var(--red); }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }
        .card {
            overflow: hidden;
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            background: var(--white);
            box-shadow: var(--shadow);
        }
        .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding: 16px 20px;
            border-bottom: 1px solid var(--border);
        }
        .card-title { color: var(--navy); font-size: 15px; font-weight: 600; }
        .card-body { padding: 20px; }
        .mini-chart {
            height: 165px;
            display: flex;
            align-items: flex-end;
            gap: 10px;
            padding-top: 16px;
        }
        .bar {
            flex: 1;
            min-width: 18px;
            border-radius: 5px 5px 0 0;
            background: var(--amber);
            opacity: .8;
        }
        .donut-wrap { display: flex; align-items: center; gap: 18px; }
        .donut {
            width: 126px;
            aspect-ratio: 1;
            border-radius: 50%;
            background: conic-gradient(var(--amber) 0 42%, var(--blue) 42% 68%, var(--green) 68% 87%, var(--purple) 87% 100%);
            position: relative;
        }
        .donut::after {
            content: '';
            position: absolute;
            inset: 22px;
            border-radius: 50%;
            background: var(--white);
        }
        .donut-legend { display: flex; flex-direction: column; gap: 8px; }
        .legend-item { display: flex; align-items: center; gap: 8px; color: var(--muted); font-size: 12px; }
        .legend-dot { width: 10px; height: 10px; border-radius: 50%; }
        .activity-item {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
        }
        .activity-item:last-child { border-bottom: 0; }
        .activity-icon {
            width: 34px;
            height: 34px;
            border-radius: var(--radius);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .activity-text { flex: 1; color: var(--ink); font-size: 13px; line-height: 1.4; }
        .activity-text strong { color: var(--navy); }
        .activity-time { color: var(--muted); font-size: 11.5px; }

        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th {
            padding: 11px 14px;
            border-bottom: 2px solid var(--border);
            color: var(--muted);
            font-size: 12px;
            font-weight: 600;
            letter-spacing: .5px;
            text-align: left;
            text-transform: uppercase;
            white-space: nowrap;
        }
        td {
            padding: 12px 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-size: 13.5px;
            vertical-align: middle;
        }
        tr:last-child td { border-bottom: 0; }
        tbody tr:hover { background: var(--sand); }
        .book-cell { display: flex; align-items: center; gap: 12px; }
        .book-cover {
            width: 38px;
            height: 52px;
            border-radius: 2px 6px 6px 2px;
            box-shadow: -2px 2px 8px rgba(28, 43, 58, .15);
        }
        .book-title { margin-bottom: 2px; color: var(--navy); font-size: 14px; font-weight: 600; }
        .book-author { color: var(--muted); font-size: 12px; }
        .badge {
            display: inline-block;
            padding: 3px 9px;
            border-radius: 12px;
            font-size: 11.5px;
            font-weight: 600;
            white-space: nowrap;
        }
        .badge-stock { background: var(--green-bg); color: var(--green); }
        .badge-low { background: var(--red-bg); color: var(--red); }
        .badge-format { background: var(--amber-light); color: var(--amber); }
        .badge-muted { background: var(--sand); color: var(--muted); }
        .toolbar { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
        .search-field {
            min-width: 260px;
            flex: 1;
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 12px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--muted);
        }
        .search-field input { width: 100%; border: 0; outline: 0; color: var(--text); background: transparent; font-size: 13px; }
        .select-filter,
        .form-control {
            width: 100%;
            min-height: 38px;
            padding: 8px 11px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            background: var(--white);
            color: var(--text);
            font-size: 13px;
            outline: 0;
        }
        textarea.form-control { min-height: 112px; resize: vertical; }
        .form-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; }
        .form-field.full { grid-column: 1 / -1; }
        .form-label { display: block; margin-bottom: 6px; color: var(--navy); font-size: 12px; font-weight: 700; }
        .form-help { margin-top: 5px; color: var(--muted); font-size: 11.5px; }
        .form-error { margin-top: 5px; color: var(--red); font-size: 12px; }
        .checkbox-row { display: flex; align-items: center; gap: 8px; min-height: 38px; color: var(--text); font-size: 13px; }
        .action-row { display: flex; align-items: center; justify-content: flex-end; gap: 9px; padding-top: 16px; }
        .table-actions { display: flex; align-items: center; gap: 6px; white-space: nowrap; }
        .btn-danger { border: 1.5px solid var(--red); background: transparent; color: var(--red); }
        .btn-danger:hover { background: var(--red-bg); }
        .flash { margin-bottom: 16px; padding: 12px 14px; border-radius: var(--radius); color: var(--green); background: var(--green-bg); font-size: 13px; font-weight: 600; }
        .empty-state { padding: 34px 20px; color: var(--muted); text-align: center; font-size: 13px; }
        .pagination-wrap { padding: 14px 20px; border-top: 1px solid var(--border); }
        .detail-grid { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px; }
        .detail-item { padding: 12px; border: 1px solid var(--border); border-radius: var(--radius); background: var(--cream); }
        .detail-label { margin-bottom: 4px; color: var(--muted); font-size: 11px; font-weight: 700; text-transform: uppercase; }
        .detail-value { color: var(--navy); font-size: 13.5px; font-weight: 600; }
        .cover-thumb {
            width: 38px;
            height: 52px;
            overflow: hidden;
            border-radius: 2px 6px 6px 2px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
            box-shadow: -2px 2px 8px rgba(28, 43, 58, .15);
        }
        .cover-thumb img { width: 100%; height: 100%; object-fit: cover; }

        @media (max-width: 1100px) {
            .stats-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .dashboard-grid { grid-template-columns: 1fr; }
            .form-grid, .detail-grid { grid-template-columns: 1fr; }
        }
        @media (max-width: 768px) {
            body { display: block; }
            .sidebar { display: none; }
            .topbar { padding: 0 16px; }
            .account-copy { display: none; }
            .topbar-actions .btn-ghost { display: none; }
            .content { padding: 18px; }
            .stats-grid { grid-template-columns: 1fr; }
        }
    </style>
    @stack('styles')
</head>
<body>
    @include('partials.sidebar')

    <main class="main">
        @include('partials.header')

        <div class="content">
            @yield('content')
        </div>
    </main>

    @stack('scripts')
</body>
</html>
