<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toya Amerta - Sistem PDAM Desa Digital</title>
    <meta name="description" content="Platform manajemen PDAM Desa yang modern, transparan, dan mudah digunakan untuk admin, petugas, dan pelanggan.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --blue-primary: #0369a1;
            --blue-dark: #0c4a6e;
            --blue-light: #e0f2fe;
            --blue-mid: #0ea5e9;
            --gray-50: #f8fafc;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;
            --white: #ffffff;
        }

        body {
            font-family: 'Instrument Sans', system-ui, sans-serif;
            color: var(--gray-800);
            background: var(--white);
            line-height: 1.6;
            overflow-x: hidden;
        }

        /* ── NAVBAR ── */
        .navbar {
            position: fixed; top: 0; left: 0; right: 0; z-index: 50;
            background: rgba(255,255,255,0.95);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid rgba(3,105,161,0.1);
            padding: 0 1.5rem;
            transition: box-shadow 0.3s;
        }
        .navbar-inner {
            max-width: 1200px; margin: 0 auto;
            display: flex; align-items: center; justify-content: space-between;
            height: 64px;
        }
        .navbar-logo {
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; font-size: 1.1rem;
            color: var(--blue-dark); text-decoration: none;
        }
        .logo-icon {
            width: 36px; height: 36px;
            background: linear-gradient(135deg, var(--blue-primary), var(--blue-mid));
            border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            color: white; font-size: 18px;
        }
        .btn-outline {
            padding: 8px 20px;
            border: 1.5px solid var(--blue-primary); border-radius: 8px;
            color: var(--blue-primary); font-weight: 600; font-size: 0.875rem;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-outline:hover { background: var(--blue-primary); color: white; }
        .btn-primary {
            padding: 8px 20px;
            background: var(--blue-primary); border: 1.5px solid var(--blue-primary);
            border-radius: 8px; color: white; font-weight: 600;
            font-size: 0.875rem; text-decoration: none; transition: all 0.2s;
        }
        .btn-primary:hover { background: var(--blue-dark); border-color: var(--blue-dark); }

        /* ── HERO ── */
        .hero {
            min-height: 100vh;
            background: linear-gradient(160deg, #0c4a6e 0%, #0369a1 45%, #0ea5e9 80%, #38bdf8 100%);
            display: flex; align-items: center;
            padding: 80px 1.5rem 0;
            position: relative; overflow: hidden;
        }
        .hero::after {
            content: '';
            position: absolute; bottom: -2px; left: 0; right: 0;
            height: 100px; background: var(--white);
            clip-path: ellipse(55% 100% at 50% 100%);
        }
        .hero-bubble {
            position: absolute; border-radius: 50%;
            background: rgba(255,255,255,0.06); pointer-events: none;
        }
        .hero-inner {
            max-width: 1200px; margin: 0 auto; width: 100%;
            display: flex; align-items: center; gap: 60px;
            padding-bottom: 120px;
        }
        .hero-text { flex: 1; color: white; }
        .hero-badge {
            display: inline-flex; align-items: center; gap: 6px;
            background: rgba(255,255,255,0.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,0.25);
            border-radius: 100px; padding: 6px 16px;
            font-size: 0.8rem; font-weight: 600; color: white;
            margin-bottom: 24px;
        }
        .hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 700; line-height: 1.15; margin-bottom: 20px;
        }
        .hero-title span { color: #7dd3fc; }
        .hero-desc {
            font-size: 1.05rem; color: rgba(255,255,255,0.85);
            max-width: 520px; margin-bottom: 36px;
        }
        .hero-actions { display: flex; gap: 12px; flex-wrap: wrap; }
        .btn-white {
            padding: 14px 32px; background: white; color: var(--blue-dark);
            border-radius: 10px; font-weight: 700; font-size: 0.95rem;
            text-decoration: none; transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(0,0,0,0.15);
        }
        .btn-white:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(0,0,0,0.2); }
        .btn-ghost {
            padding: 14px 32px; background: transparent; color: white;
            border: 2px solid rgba(255,255,255,0.5);
            border-radius: 10px; font-weight: 600; font-size: 0.95rem;
            text-decoration: none; transition: all 0.2s;
        }
        .btn-ghost:hover { background: rgba(255,255,255,0.1); border-color: white; }
        .hero-stats {
            display: flex; gap: 28px; margin-top: 48px; flex-wrap: wrap;
        }
        .stat { text-align: center; }
        .stat-num { font-size: 1.75rem; font-weight: 700; color: white; }
        .stat-lbl { font-size: 0.78rem; color: rgba(255,255,255,0.65); }
        .stat-sep { width: 1px; background: rgba(255,255,255,0.2); align-self: stretch; }

        /* Floating preview card */
        .hero-card {
            flex: 0 0 380px;
            background: rgba(255,255,255,0.12);
            backdrop-filter: blur(16px);
            border: 1px solid rgba(255,255,255,0.2);
            border-radius: 20px; padding: 24px; color: white;
        }
        .card-label {
            font-size: 0.75rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.06em; color: rgba(255,255,255,0.55); margin-bottom: 14px;
        }
        .meter-row {
            display: flex; justify-content: space-between; align-items: center;
            padding: 10px 0; border-bottom: 1px solid rgba(255,255,255,0.1);
            font-size: 0.85rem;
        }
        .meter-row:last-of-type { border-bottom: none; }
        .meter-name { font-weight: 600; }
        .meter-val { font-size: 0.75rem; color: rgba(255,255,255,0.6); }
        .badge { padding: 3px 10px; border-radius: 100px; font-size: 0.7rem; font-weight: 700; }
        .badge-lunas { background: rgba(16,185,129,0.2); color: #6ee7b7; }
        .badge-belum { background: rgba(251,191,36,0.2); color: #fde68a; }
        .card-footer {
            margin-top: 14px; padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,0.15);
            display: flex; justify-content: space-between; font-size: 0.8rem;
        }

        /* ── SHARED SECTION STYLES ── */
        section { padding: 80px 1.5rem; }
        .container { max-width: 1200px; margin: 0 auto; }
        .section-label {
            display: inline-block; font-size: 0.78rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: var(--blue-primary); background: var(--blue-light);
            padding: 4px 14px; border-radius: 100px; margin-bottom: 14px;
        }
        .section-title {
            font-size: clamp(1.6rem, 3.5vw, 2.4rem); font-weight: 700;
            color: var(--gray-900); margin-bottom: 14px; line-height: 1.25;
        }
        .section-desc { font-size: 1rem; color: var(--gray-600); max-width: 600px; }
        .text-center { text-align: center; }
        .mx-auto { margin-left: auto; margin-right: auto; }

        /* ── FEATURES ── */
        .features { background: var(--gray-50); }
        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px; margin-top: 48px;
        }
        .feature-card {
            background: white; border-radius: 16px; padding: 26px;
            border: 1px solid #e2e8f0; transition: all 0.3s;
        }
        .feature-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(3,105,161,0.1);
            border-color: #bae6fd;
        }
        .feature-icon {
            width: 50px; height: 50px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 22px; margin-bottom: 18px;
        }
        .ic-blue { background: #dbeafe; }
        .ic-teal { background: #ccfbf1; }
        .ic-purple { background: #ede9fe; }
        .ic-orange { background: #ffedd5; }
        .ic-green { background: #dcfce7; }
        .ic-red { background: #fee2e2; }
        .feature-title { font-size: 1rem; font-weight: 700; color: var(--gray-900); margin-bottom: 6px; }
        .feature-desc { font-size: 0.875rem; color: var(--gray-600); line-height: 1.6; }

        /* ── ACTORS ── */
        .actors-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 24px; margin-top: 48px;
        }
        .actor-card {
            border-radius: 20px; padding: 32px 26px;
            color: white; position: relative; overflow: hidden;
        }
        .actor-card::before {
            content: ''; position: absolute;
            top: -30px; right: -30px;
            width: 120px; height: 120px;
            border-radius: 50%; background: rgba(255,255,255,0.07);
        }
        .ac-admin { background: linear-gradient(135deg, #1e3a8a, #3b82f6); }
        .ac-petugas { background: linear-gradient(135deg, #065f46, #10b981); }
        .ac-pelanggan { background: linear-gradient(135deg, #7c2d12, #f97316); }
        .actor-avatar {
            width: 56px; height: 56px; border-radius: 50%;
            background: rgba(255,255,255,0.18);
            display: flex; align-items: center; justify-content: center;
            font-size: 26px; margin-bottom: 18px;
        }
        .actor-role {
            font-size: 0.72rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.1em; color: rgba(255,255,255,0.55); margin-bottom: 4px;
        }
        .actor-name { font-size: 1.25rem; font-weight: 700; margin-bottom: 14px; }
        .actor-list { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .actor-list li {
            font-size: 0.875rem; color: rgba(255,255,255,0.85);
            display: flex; align-items: center; gap: 8px;
        }
        .actor-list li::before {
            content: '✓'; width: 18px; height: 18px;
            background: rgba(255,255,255,0.2); border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 0.65rem; flex-shrink: 0;
        }

        /* ── HOW IT WORKS ── */
        .how { background: var(--gray-50); }
        .steps-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(210px, 1fr));
            gap: 28px; margin-top: 48px;
        }
        .step { text-align: center; padding: 16px; }
        .step-num {
            width: 54px; height: 54px; border-radius: 50%;
            background: linear-gradient(135deg, var(--blue-primary), var(--blue-mid));
            color: white; font-size: 1.2rem; font-weight: 700;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 18px;
            box-shadow: 0 8px 24px rgba(3,105,161,0.3);
        }
        .step-title { font-size: 1rem; font-weight: 700; color: var(--gray-900); margin-bottom: 6px; }
        .step-desc { font-size: 0.875rem; color: var(--gray-600); }

        /* ── WHATSAPP NOTIF ── */
        .notif-inner {
            display: grid; grid-template-columns: 1fr 1fr;
            gap: 60px; align-items: center;
        }
        .check-list { list-style: none; margin-top: 24px; display: flex; flex-direction: column; gap: 12px; }
        .check-list li {
            display: flex; align-items: center; gap: 10px;
            font-size: 0.9rem; color: var(--gray-700);
        }
        .check-icon {
            width: 28px; height: 28px; background: #dcfce7;
            border-radius: 6px; display: flex; align-items: center;
            justify-content: center; flex-shrink: 0; font-size: 0.8rem;
        }
        .phone-wrap {
            background: #1a1a2e; border-radius: 28px; padding: 20px;
            max-width: 300px; margin: 0 auto;
            box-shadow: 0 24px 64px rgba(0,0,0,0.3);
        }
        .phone-screen { background: #111827; border-radius: 18px; padding: 14px; }
        .wa-header {
            display: flex; align-items: center; gap: 10px;
            padding-bottom: 10px; border-bottom: 1px solid rgba(255,255,255,0.08);
            margin-bottom: 12px;
        }
        .wa-avatar {
            width: 34px; height: 34px; background: #25d366;
            border-radius: 50%; display: flex; align-items: center;
            justify-content: center; font-size: 14px; flex-shrink: 0;
        }
        .wa-name { font-size: 0.8rem; font-weight: 600; color: white; }
        .wa-online { font-size: 0.62rem; color: #25d366; }
        .wa-bubble {
            background: #054f43; border-radius: 12px 12px 12px 2px;
            padding: 10px 13px;
        }
        .wa-text { font-size: 0.72rem; color: #e9edef; line-height: 1.6; }
        .wa-time { font-size: 0.58rem; color: rgba(255,255,255,0.35); text-align: right; margin-top: 5px; }

        /* ── TECH STACK ── */
        .tech { background: var(--gray-900); color: white; }
        .tech-grid {
            display: flex; flex-wrap: wrap; gap: 10px;
            justify-content: center; margin-top: 36px;
        }
        .tech-badge {
            padding: 9px 18px;
            background: rgba(255,255,255,0.07);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px; font-size: 0.85rem; font-weight: 600;
            color: rgba(255,255,255,0.8);
        }

        /* ── CTA ── */
        .cta {
            background: linear-gradient(135deg, var(--blue-dark) 0%, var(--blue-primary) 100%);
            text-align: center; color: white;
        }
        .cta-title { font-size: clamp(1.8rem, 4vw, 3rem); font-weight: 700; margin-bottom: 14px; }
        .cta-desc {
            font-size: 1rem; color: rgba(255,255,255,0.75);
            max-width: 480px; margin: 0 auto 32px;
        }
        .cta-actions { display: flex; gap: 12px; justify-content: center; flex-wrap: wrap; }

        /* ── FOOTER ── */
        footer { background: var(--gray-900); padding: 48px 1.5rem 24px; }
        .footer-inner { max-width: 1200px; margin: 0 auto; }
        .footer-top {
            display: grid; grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: 40px; margin-bottom: 36px;
        }
        .footer-logo {
            display: flex; align-items: center; gap: 10px;
            color: white; font-weight: 700; text-decoration: none; margin-bottom: 10px;
        }
        .footer-logo-icon {
            width: 30px; height: 30px;
            background: linear-gradient(135deg, var(--blue-primary), var(--blue-mid));
            border-radius: 6px; display: flex; align-items: center;
            justify-content: center; font-size: 13px; color: white;
        }
        .footer-desc { font-size: 0.875rem; color: rgba(255,255,255,0.45); line-height: 1.7; }
        .footer-col h4 {
            font-size: 0.8rem; font-weight: 700; text-transform: uppercase;
            letter-spacing: 0.07em; color: rgba(255,255,255,0.85); margin-bottom: 14px;
        }
        .footer-col ul { list-style: none; display: flex; flex-direction: column; gap: 8px; }
        .footer-col a {
            font-size: 0.875rem; color: rgba(255,255,255,0.45);
            text-decoration: none; transition: color 0.2s;
        }
        .footer-col a:hover { color: rgba(255,255,255,0.85); }
        .footer-bottom {
            border-top: 1px solid rgba(255,255,255,0.07); padding-top: 20px;
            display: flex; align-items: center; justify-content: space-between;
            font-size: 0.78rem; color: rgba(255,255,255,0.35);
        }

        /* ── RESPONSIVE ── */
        @media (max-width: 960px) {
            .hero-inner { flex-direction: column; padding-bottom: 130px; }
            .hero-card { flex: none; width: 100%; max-width: 420px; }
            .notif-inner { grid-template-columns: 1fr; }
            .footer-top { grid-template-columns: 1fr 1fr; }
        }
        @media (max-width: 600px) {
            .hero-stats { flex-direction: column; gap: 16px; }
            .stat-sep { display: none; }
            .footer-top { grid-template-columns: 1fr; }
            .footer-bottom { flex-direction: column; gap: 6px; text-align: center; }
        }
    </style>
</head>
<body>

{{-- ═══════════════ NAVBAR ═══════════════ --}}
<nav class="navbar" id="navbar">
    <div class="navbar-inner">
        <a href="/" class="navbar-logo">
            <div class="logo-icon">💧</div>
            <span>Toya Amerta</span>
        </a>
        <div style="display:flex;gap:8px;align-items:center;">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-outline">Masuk</a>
                @endauth
            @endif
        </div>
    </div>
</nav>

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero">
    <div class="hero-bubble" style="width:500px;height:500px;top:-120px;right:-80px;"></div>
    <div class="hero-bubble" style="width:280px;height:280px;bottom:80px;left:-60px;"></div>

    <div class="hero-inner">
        <div class="hero-text">
            <div class="hero-badge">💧 Sistem PDAM Desa Digital</div>
            <h1 class="hero-title">
                Kelola Air Bersih Desa<br>
                Lebih <span>Cerdas & Transparan</span>
            </h1>
            <p class="hero-desc">
                Platform manajemen PDAM Desa berbasis teknologi modern yang menghubungkan admin, petugas lapangan, dan pelanggan dalam satu ekosistem digital.
            </p>
            <div class="hero-actions">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn-white">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn-white">Mulai Sekarang</a>
                    @endauth
                @else
                    <a href="#fitur" class="btn-white">Pelajari Fitur</a>
                @endif
                <a href="#cara-kerja" class="btn-ghost">Cara Kerja</a>
            </div>
            <div class="hero-stats">
                <div class="stat">
                    <div class="stat-num">100%</div>
                    <div class="stat-lbl">Digital & Paperless</div>
                </div>
                <div class="stat-sep"></div>
                <div class="stat">
                    <div class="stat-num">3</div>
                    <div class="stat-lbl">Jenis Pengguna</div>
                </div>
                <div class="stat-sep"></div>
                <div class="stat">
                    <div class="stat-num">24/7</div>
                    <div class="stat-lbl">Akses Real-time</div>
                </div>
            </div>
        </div>

        {{-- Preview Card --}}
        <div class="hero-card">
            <div class="card-label">📊 Status Tagihan · Juni {{ date('Y') }}</div>
            <div class="meter-row">
                <div>
                    <div class="meter-name">Budi Santoso</div>
                    <div class="meter-val">18 m³ · Rp 54.000</div>
                </div>
                <span class="badge badge-lunas">Lunas</span>
            </div>
            <div class="meter-row">
                <div>
                    <div class="meter-name">Siti Rahayu</div>
                    <div class="meter-val">24 m³ · Rp 72.000</div>
                </div>
                <span class="badge badge-belum">Belum</span>
            </div>
            <div class="meter-row">
                <div>
                    <div class="meter-name">Ahmad Fauzi</div>
                    <div class="meter-val">12 m³ · Rp 36.000</div>
                </div>
                <span class="badge badge-lunas">Lunas</span>
            </div>
            <div class="meter-row">
                <div>
                    <div class="meter-name">Dewi Kartika</div>
                    <div class="meter-val">30 m³ · Rp 90.000</div>
                </div>
                <span class="badge badge-belum">Belum</span>
            </div>
            <div class="card-footer">
                <span style="color:rgba(255,255,255,0.55)">Total Terkumpul</span>
                <span style="font-weight:700">Rp 90.000 / 252.000</span>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ FEATURES ═══════════════ --}}
<section class="features" id="fitur">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Fitur Unggulan</span>
            <h2 class="section-title">Semua yang Dibutuhkan<br>Pengelolaan PDAM Modern</h2>
            <p class="section-desc mx-auto">
                Dari pencatatan meter hingga notifikasi WhatsApp otomatis — semua tersedia dalam satu platform terintegrasi.
            </p>
        </div>
        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon ic-blue">📱</div>
                <div class="feature-title">Pencatatan Meter Digital</div>
                <div class="feature-desc">Petugas input meter langsung dari smartphone dengan foto bukti. Data tersinkron real-time ke sistem pusat.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon ic-teal">💬</div>
                <div class="feature-title">Notifikasi WhatsApp</div>
                <div class="feature-desc">Tagihan dan konfirmasi pembayaran dikirim otomatis via WhatsApp menggunakan Fonnte API tanpa biaya tambahan.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon ic-purple">📊</div>
                <div class="feature-title">Laporan & Analitik</div>
                <div class="feature-desc">Dashboard laporan keuangan, pemakaian air, dan performa penagihan dalam grafik yang mudah dipahami.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon ic-orange">🔧</div>
                <div class="feature-title">Manajemen Maintenance</div>
                <div class="feature-desc">Petugas lapangan melaporkan kerusakan, perbaikan pipa, dan status infrastruktur secara langsung dari lapangan.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon ic-green">💳</div>
                <div class="feature-title">Konfirmasi Pembayaran</div>
                <div class="feature-desc">Petugas konfirmasi pembayaran tunai di lapangan dengan bukti foto. Riwayat tersimpan permanen dan aman.</div>
            </div>
            <div class="feature-card">
                <div class="feature-icon ic-red">🏠</div>
                <div class="feature-title">Portal Pelanggan</div>
                <div class="feature-desc">Pelanggan cek tagihan dan riwayat pemakaian air kapan saja dari smartphone via aplikasi Flutter.</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ ACTORS ═══════════════ --}}
<section id="pengguna">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Pengguna Sistem</span>
            <h2 class="section-title">Dirancang untuk Semua<br>Pihak yang Terlibat</h2>
            <p class="section-desc mx-auto">
                Tiga tipe pengguna dengan akses dan fitur yang disesuaikan dengan kebutuhan masing-masing.
            </p>
        </div>
        <div class="actors-grid">
            <div class="actor-card ac-admin">
                <div class="actor-avatar">👨‍💼</div>
                <div class="actor-role">Aktor 1</div>
                <div class="actor-name">Admin</div>
                <ul class="actor-list">
                    <li>Kelola data master pelanggan & tarif</li>
                    <li>Buat & approve tagihan bulanan</li>
                    <li>Laporan keuangan & rekapitulasi</li>
                    <li>Kelola pengguna sistem</li>
                    <li>Dashboard analitik lengkap via Filament</li>
                </ul>
            </div>
            <div class="actor-card ac-petugas">
                <div class="actor-avatar">👷</div>
                <div class="actor-role">Aktor 2</div>
                <div class="actor-name">Petugas Lapangan</div>
                <ul class="actor-list">
                    <li>Input pembacaan meter air</li>
                    <li>Upload foto meter sebagai bukti</li>
                    <li>Konfirmasi pembayaran tunai</li>
                    <li>Laporan kerusakan & maintenance</li>
                    <li>Update status perbaikan infrastruktur</li>
                </ul>
            </div>
            <div class="actor-card ac-pelanggan">
                <div class="actor-avatar">🏘️</div>
                <div class="actor-role">Aktor 3</div>
                <div class="actor-name">Pelanggan</div>
                <ul class="actor-list">
                    <li>Lihat tagihan bulan berjalan</li>
                    <li>Riwayat pemakaian air bulanan</li>
                    <li>Riwayat pembayaran lengkap</li>
                    <li>Notifikasi tagihan via WhatsApp</li>
                    <li>Akses via aplikasi mobile Flutter</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ HOW IT WORKS ═══════════════ --}}
<section class="how" id="cara-kerja">
    <div class="container">
        <div class="text-center">
            <span class="section-label">Cara Kerja</span>
            <h2 class="section-title">Proses Sederhana,<br>Hasil yang Maksimal</h2>
            <p class="section-desc mx-auto">Alur kerja efisien dari pencatatan meter hingga konfirmasi pembayaran pelanggan.</p>
        </div>
        <div class="steps-grid">
            <div class="step">
                <div class="step-num">1</div>
                <div class="step-title">Catat Meter</div>
                <div class="step-desc">Petugas membaca & menginput angka meter air pelanggan beserta foto melalui aplikasi mobile.</div>
            </div>
            <div class="step">
                <div class="step-num">2</div>
                <div class="step-title">Hitung Tagihan</div>
                <div class="step-desc">Sistem otomatis menghitung tagihan berdasarkan pemakaian m³ dan tarif yang berlaku.</div>
            </div>
            <div class="step">
                <div class="step-num">3</div>
                <div class="step-title">Kirim Notifikasi</div>
                <div class="step-desc">Tagihan dikirim otomatis ke WhatsApp pelanggan dengan detail pemakaian dan nominal.</div>
            </div>
            <div class="step">
                <div class="step-num">4</div>
                <div class="step-title">Konfirmasi Bayar</div>
                <div class="step-desc">Petugas mengkonfirmasi pembayaran di sistem setelah menerima uang dari pelanggan.</div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ WHATSAPP NOTIF ═══════════════ --}}
<section>
    <div class="container">
        <div class="notif-inner">
            <div>
                <span class="section-label">Notifikasi WhatsApp</span>
                <h2 class="section-title">Tagihan Langsung<br>ke WhatsApp Pelanggan</h2>
                <p class="section-desc">
                    Integrasi dengan Fonnte API memastikan setiap pelanggan mendapatkan notifikasi tagihan, pengingat, dan konfirmasi lunas langsung di WhatsApp mereka.
                </p>
                <ul class="check-list">
                    <li><span class="check-icon">✅</span> Notifikasi tagihan bulanan otomatis</li>
                    <li><span class="check-icon">✅</span> Pengingat jatuh tempo pembayaran</li>
                    <li><span class="check-icon">✅</span> Konfirmasi pembayaran lunas</li>
                    <li><span class="check-icon">✅</span> Notifikasi gangguan & maintenance</li>
                </ul>
            </div>
            <div class="phone-wrap">
                <div class="phone-screen">
                    <div class="wa-header">
                        <div class="wa-avatar">💧</div>
                        <div>
                            <div class="wa-name">PDAM Desa Toya Amerta</div>
                            <div class="wa-online">● Online</div>
                        </div>
                    </div>
                    <div class="wa-bubble">
                        <div class="wa-text">
                            Yth. Bpk/Ibu <strong style="color:white">Budi Santoso</strong><br><br>
                            📋 <strong style="color:white">TAGIHAN AIR - Juni {{ date('Y') }}</strong><br>
                            ────────────────<br>
                            Meter Awal &nbsp;: 1.240 m³<br>
                            Meter Akhir: 1.258 m³<br>
                            Pemakaian &nbsp;: <strong style="color:#7dd3fc">18 m³</strong><br>
                            ────────────────<br>
                            💰 Total Tagihan:<br>
                            <strong style="color:#fde68a">Rp 54.000</strong><br><br>
                            Jatuh tempo: 20 Juni {{ date('Y') }}<br><br>
                            Harap segera dilunasi. Terima kasih 🙏
                        </div>
                        <div class="wa-time">10:30 ✓✓</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ═══════════════ TECH STACK ═══════════════ --}}
<section class="tech">
    <div class="container">
        <div class="text-center">
            <span class="section-label" style="background:rgba(255,255,255,0.1);color:rgba(255,255,255,0.6);">Teknologi</span>
            <h2 class="section-title" style="color:white;">Dibangun dengan Stack<br>Modern & Terpercaya</h2>
            <p class="section-desc mx-auto" style="color:rgba(255,255,255,0.55);">
                Kombinasi teknologi yang menjamin performa, keamanan, dan kemudahan pengembangan jangka panjang.
            </p>
        </div>
        <div class="tech-grid">
            <span class="tech-badge">🐘 Laravel 13</span>
            <span class="tech-badge">🐘 PHP 8.3</span>
            <span class="tech-badge">🗄️ MySQL 8</span>
            <span class="tech-badge">🔐 Laravel Sanctum</span>
            <span class="tech-badge">⚡ Filament 3</span>
            <span class="tech-badge">📱 Flutter 3.44</span>
            <span class="tech-badge">🎯 GetX</span>
            <span class="tech-badge">💬 Fonnte WA API</span>
            <span class="tech-badge">🐳 Docker</span>
            <span class="tech-badge">☁️ S3 Storage</span>
            <span class="tech-badge">🔴 Redis</span>
            <span class="tech-badge">🏗️ Clean Architecture</span>
            <span class="tech-badge">📐 Repository Pattern</span>
        </div>
    </div>
</section>

{{-- ═══════════════ CTA ═══════════════ --}}
<section class="cta">
    <div class="container">
        <h2 class="cta-title">Siap Modernisasi<br>PDAM Desa Anda?</h2>
        <p class="cta-desc">
            Rasakan kemudahan pengelolaan air bersih desa yang lebih efisien, transparan, dan terdigitalisasi.
        </p>
        <div class="cta-actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn-white">Buka Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn-white">Masuk ke Sistem</a>
                @endauth
            @endif
            <a href="#fitur" class="btn-ghost">Pelajari Fitur</a>
        </div>
    </div>
</section>

{{-- ═══════════════ FOOTER ═══════════════ --}}
<footer>
    <div class="footer-inner">
        <div class="footer-top">
            <div>
                <a href="/" class="footer-logo">
                    <div class="footer-logo-icon">💧</div>
                    <span>Toya Amerta</span>
                </a>
                <p class="footer-desc">
                    Sistem manajemen PDAM Desa yang modern dan terintegrasi. Menghubungkan admin, petugas, dan pelanggan dalam satu platform digital.
                </p>
            </div>
            <div class="footer-col">
                <h4>Sistem</h4>
                <ul>
                    <li><a href="#fitur">Fitur</a></li>
                    <li><a href="#pengguna">Pengguna</a></li>
                    <li><a href="#cara-kerja">Cara Kerja</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Akses</h4>
                <ul>
                    @if (Route::has('login'))
                        <li><a href="{{ route('login') }}">Login Admin</a></li>
                        <li><a href="{{ route('login') }}">Login Petugas</a></li>
                    @endif
                    <li><a href="#">Download App</a></li>
                </ul>
            </div>
            <div class="footer-col">
                <h4>Teknologi</h4>
                <ul>
                    <li><a href="#">Laravel 13</a></li>
                    <li><a href="#">Flutter 3</a></li>
                    <li><a href="#">Filament 3</a></li>
                    <li><a href="#">Docker</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>© {{ date('Y') }} Toya Amerta · Sistem PDAM Desa</span>
            <span>Laravel v{{ app()->version() }} · PHP {{ PHP_VERSION }}</span>
        </div>
    </div>
</footer>

<script>
    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const target = document.querySelector(a.getAttribute('href'));
            if (target) { e.preventDefault(); target.scrollIntoView({ behavior: 'smooth' }); }
        });
    });

    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.style.boxShadow = window.scrollY > 10
            ? '0 4px 20px rgba(0,0,0,0.08)'
            : 'none';
    });
</script>

</body>
</html>
