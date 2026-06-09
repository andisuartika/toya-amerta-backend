<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Toya Amerta — Pengelolaan Air Bersih Lingkungan Sangket</title>
    <meta name="description" content="Sistem pengelolaan air bersih PDAM swadaya Lingkungan Sangket. Transparan, akuntabel, dan mudah diakses oleh seluruh warga.">

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700&display=swap" rel="stylesheet" />

    @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
        @vite(['resources/scss/app.scss', 'resources/js/app.js'])
    @endif

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --primary:      #1565C0;
            --primary-dk:   #0D47A1;
            --primary-lt:   #E3F2FD;
            --primary-mid:  #1976D2;
            --secondary:    #26C6DA;
            --secondary-dk: #00ACC1;
            --secondary-lt: #E0F7FA;
            --accent:       #00897B;
            --accent-dk:    #00695C;
            --accent-lt:    #E0F2F1;
            --bg:           #F0F4F8;
            --surface:      #FFFFFF;
            --error:        #D32F2F;
            --success:      #388E3C;
            --warning:      #F57C00;
            --text:         #1A1A2E;
            --text-2:       #546E7A;
            --text-3:       #78909C;
            --line:         #CFD8DC;
            --line-lt:      #ECEFF1;
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Instrument Sans', system-ui, -apple-system, sans-serif;
            color: var(--text);
            background: var(--bg);
            line-height: 1.6;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased;
        }

        /* ─── UTILS ─── */
        .container { max-width: 1120px; margin: 0 auto; padding: 0 1.5rem; }
        .text-center { text-align: center; }

        /* ─── NAVBAR ─── */
        .nav {
            position: fixed; top: 0; left: 0; right: 0; z-index: 100;
            background: rgba(255,255,255,0.92);
            backdrop-filter: blur(16px) saturate(1.8);
            -webkit-backdrop-filter: blur(16px) saturate(1.8);
            border-bottom: 1px solid var(--line-lt);
            transition: box-shadow .25s;
        }
        .nav.scrolled { box-shadow: 0 2px 24px rgba(21,101,192,.08); }
        .nav-inner {
            max-width: 1120px; margin: 0 auto; padding: 0 1.5rem;
            height: 62px; display: flex; align-items: center; justify-content: space-between;
        }
        .nav-brand {
            display: flex; align-items: center; gap: 10px;
            font-weight: 700; font-size: .95rem;
            color: var(--primary-dk); text-decoration: none;
        }
        .nav-logo {
            width: 36px; height: 36px; border-radius: 10px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
        }
        .nav-logo svg { width: 18px; height: 18px; fill: white; }
        .nav-tagline {
            font-size: .72rem; font-weight: 500; color: var(--text-2);
            margin-left: 2px; display: none;
        }
        @media (min-width: 640px) { .nav-tagline { display: block; } }
        .nav-actions { display: flex; align-items: center; gap: 8px; }

        /* ─── BUTTONS ─── */
        .btn {
            display: inline-flex; align-items: center; gap: 6px;
            font-weight: 600; font-size: .85rem; text-decoration: none;
            border-radius: 8px; transition: all .18s; cursor: pointer; border: none;
            white-space: nowrap; line-height: 1;
        }
        .btn-sm  { padding: 8px 18px; }
        .btn-md  { padding: 11px 24px; font-size: .9rem; }
        .btn-lg  { padding: 14px 30px; font-size: .95rem; }

        .btn-ghost { color: var(--text-2); background: transparent; }
        .btn-ghost:hover { color: var(--text); background: var(--bg); }

        .btn-outline-primary {
            color: var(--primary); background: transparent;
            border: 1.5px solid var(--primary);
        }
        .btn-outline-primary:hover { background: var(--primary-lt); }

        .btn-primary { color: white; background: var(--primary); }
        .btn-primary:hover { background: var(--primary-dk); box-shadow: 0 4px 16px rgba(21,101,192,.3); }

        .btn-secondary { color: var(--primary-dk); background: var(--secondary); }
        .btn-secondary:hover { background: var(--secondary-dk); color: white; }

        .btn-white {
            color: var(--primary-dk); background: white;
            box-shadow: 0 2px 8px rgba(0,0,0,.1), 0 6px 24px rgba(0,0,0,.06);
        }
        .btn-white:hover { transform: translateY(-1px); box-shadow: 0 4px 16px rgba(0,0,0,.14), 0 8px 32px rgba(0,0,0,.08); }

        .btn-white-ghost {
            color: rgba(255,255,255,.85); background: rgba(255,255,255,.1);
            border: 1.5px solid rgba(255,255,255,.3);
        }
        .btn-white-ghost:hover { background: rgba(255,255,255,.18); color: white; border-color: rgba(255,255,255,.6); }

        /* ─── HERO ─── */
        .hero {
            min-height: 100svh;
            background: linear-gradient(150deg, var(--primary-dk) 0%, var(--primary) 40%, #1A7FCC 70%, #0097A7 100%);
            display: flex; align-items: center;
            padding: 62px 0 0; position: relative; overflow: hidden;
        }
        .hero-grid {
            position: absolute; inset: 0; pointer-events: none;
            background-image:
                linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 52px 52px;
        }
        .hero-glow {
            position: absolute; border-radius: 50%; pointer-events: none;
            background: rgba(38,198,218,.18); filter: blur(80px);
        }
        .hero::after {
            content: ''; position: absolute; bottom: 0; left: 0; right: 0;
            height: 90px;
            background: linear-gradient(to bottom, transparent, var(--bg));
            pointer-events: none;
        }
        .hero-inner {
            width: 100%; display: flex; align-items: center; gap: 56px;
            padding-bottom: 110px; position: relative; z-index: 1;
        }
        .hero-text { flex: 1; color: white; }

        .hero-chip {
            display: inline-flex; align-items: center; gap: 8px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.18);
            border-radius: 100px; padding: 5px 14px 5px 8px;
            font-size: .78rem; font-weight: 600;
            color: rgba(255,255,255,.8); margin-bottom: 28px;
        }
        .chip-badge {
            background: var(--secondary); color: var(--primary-dk);
            font-size: .65rem; font-weight: 800; letter-spacing: .04em;
            text-transform: uppercase; padding: 2px 8px; border-radius: 100px;
        }

        .hero-title {
            font-size: clamp(2.2rem, 5vw, 3.6rem);
            font-weight: 700; line-height: 1.1; letter-spacing: -.025em;
            margin-bottom: 8px; color: white;
        }
        .hero-title-sub {
            font-size: clamp(1.1rem, 2.5vw, 1.5rem);
            font-weight: 500; color: rgba(255,255,255,.55);
            letter-spacing: -.01em; margin-bottom: 20px;
        }
        .hero-desc {
            font-size: .98rem; color: rgba(255,255,255,.6);
            max-width: 500px; line-height: 1.75; margin-bottom: 36px;
        }
        .hero-cta { display: flex; gap: 10px; flex-wrap: wrap; }

        .hero-stats {
            display: flex; align-items: center; gap: 0;
            margin-top: 48px; padding-top: 32px;
            border-top: 1px solid rgba(255,255,255,.12);
            flex-wrap: wrap; gap: 0;
        }
        .stat-block { flex: 1; min-width: 100px; padding: 0 20px 0 0; }
        .stat-block + .stat-block {
            border-left: 1px solid rgba(255,255,255,.12); padding-left: 20px;
        }
        .stat-val { font-size: 1.6rem; font-weight: 700; color: white; letter-spacing: -.03em; }
        .stat-val span { font-size: 1rem; font-weight: 500; }
        .stat-key { font-size: .72rem; color: rgba(255,255,255,.4); margin-top: 2px; }

        /* Hero panel */
        .hero-panel {
            flex: 0 0 340px;
            background: rgba(255,255,255,.1);
            border: 1px solid rgba(255,255,255,.15);
            border-radius: 18px; overflow: hidden;
            backdrop-filter: blur(12px);
        }
        .hp-head {
            padding: 14px 18px; border-bottom: 1px solid rgba(255,255,255,.1);
            display: flex; align-items: center; justify-content: space-between;
        }
        .hp-title { font-size: .7rem; font-weight: 700; text-transform: uppercase; letter-spacing: .08em; color: rgba(255,255,255,.45); }
        .hp-live {
            display: flex; align-items: center; gap: 5px;
            font-size: .68rem; font-weight: 700; color: var(--secondary);
        }
        .hp-live-dot { width: 6px; height: 6px; border-radius: 50%; background: var(--secondary); animation: pulse 2s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }

        .hp-row {
            padding: 12px 18px; border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; justify-content: space-between;
        }
        .hp-row:last-child { border-bottom: none; }
        .hp-name { font-size: .83rem; font-weight: 600; color: rgba(255,255,255,.85); }
        .hp-meta { font-size: .7rem; color: rgba(255,255,255,.35); margin-top: 2px; }
        .hp-right { text-align: right; }
        .s-pill { display: inline-block; font-size: .65rem; font-weight: 700; padding: 2px 9px; border-radius: 100px; }
        .s-lunas { background: rgba(56,142,60,.2); color: #81C784; }
        .s-belum { background: rgba(245,124,0,.18); color: #FFB74D; }
        .hp-amt { font-size: .78rem; font-weight: 600; color: rgba(255,255,255,.5); margin-top: 4px; }

        .hp-foot {
            padding: 12px 18px; border-top: 1px solid rgba(255,255,255,.1);
            display: flex; justify-content: space-between; align-items: center;
        }
        .hp-fl { font-size: .7rem; color: rgba(255,255,255,.35); }
        .hp-fv { font-size: .83rem; font-weight: 700; color: rgba(255,255,255,.8); }
        .hp-fv-muted { color: rgba(255,255,255,.25); }

        /* ─── SECTIONS ─── */
        .sec { padding: 96px 0; }
        .sec-bg { background: var(--bg); }
        .sec-white { background: var(--surface); }
        .sec-primary { background: var(--primary-dk); }
        .sec-gradient { background: linear-gradient(135deg, var(--primary-dk) 0%, var(--primary) 60%, #0097A7 100%); }

        .eyebrow {
            display: inline-block; font-size: .7rem; font-weight: 700;
            text-transform: uppercase; letter-spacing: .1em;
            color: var(--primary); margin-bottom: 12px;
        }
        .eyebrow-cyan { color: var(--secondary-dk); }
        .eyebrow-teal { color: var(--accent); }
        .eyebrow-white { color: rgba(255,255,255,.4); }

        .sec-title {
            font-size: clamp(1.8rem, 3.5vw, 2.5rem); font-weight: 700;
            letter-spacing: -.02em; line-height: 1.2;
            color: var(--text); margin-bottom: 14px;
        }
        .sec-title-white { color: white; }
        .sec-body {
            font-size: .95rem; color: var(--text-2);
            max-width: 560px; line-height: 1.75;
        }
        .sec-body-white { color: rgba(255,255,255,.5); }

        /* ─── INFO STRIP ─── */
        .info-strip {
            background: var(--primary-lt); border-bottom: 1px solid #BBDEFB;
            padding: 10px 0;
        }
        .info-strip-inner {
            display: flex; align-items: center; justify-content: center;
            gap: 32px; flex-wrap: wrap;
        }
        .info-item {
            display: flex; align-items: center; gap: 7px;
            font-size: .8rem; color: var(--primary); font-weight: 500;
        }
        .info-item svg { width: 14px; height: 14px; stroke: var(--primary); fill: none; stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; }

        /* ─── ABOUT ─── */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 72px; align-items: center; }
        .about-card {
            background: var(--surface); border-radius: 20px; padding: 36px;
            border: 1px solid var(--line-lt);
            box-shadow: 0 4px 24px rgba(21,101,192,.06);
        }
        .about-icon-row { display: flex; gap: 12px; margin-bottom: 28px; }
        .about-icon {
            width: 48px; height: 48px; border-radius: 14px;
            display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .ai-primary { background: var(--primary-lt); }
        .ai-secondary { background: var(--secondary-lt); }
        .ai-accent { background: var(--accent-lt); }
        .about-icon svg { width: 22px; height: 22px; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; fill: none; }
        .ai-primary svg { stroke: var(--primary); }
        .ai-secondary svg { stroke: var(--secondary-dk); }
        .ai-accent svg { stroke: var(--accent); }

        .about-stat-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1px; background: var(--line-lt); border-radius: 12px; overflow: hidden; margin-top: 24px; }
        .about-stat { background: var(--surface); padding: 18px; }
        .as-val { font-size: 1.6rem; font-weight: 700; color: var(--primary); letter-spacing: -.02em; }
        .as-key { font-size: .75rem; color: var(--text-2); margin-top: 3px; }

        /* ─── FEATURES ─── */
        .feat-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 1px; background: var(--line);
            border: 1px solid var(--line); border-radius: 16px;
            overflow: hidden; margin-top: 56px;
        }
        .feat-cell {
            background: var(--surface); padding: 32px 28px; transition: background .2s;
        }
        .feat-cell:hover { background: var(--primary-lt); }
        .feat-icon {
            width: 44px; height: 44px; border-radius: 12px;
            display: flex; align-items: center; justify-content: center; margin-bottom: 20px;
        }
        .fi-blue { background: var(--primary-lt); }
        .fi-cyan { background: var(--secondary-lt); }
        .fi-teal { background: var(--accent-lt); }
        .fi-warn { background: #FFF3E0; }
        .feat-icon svg { width: 20px; height: 20px; stroke-width: 1.75; stroke-linecap: round; stroke-linejoin: round; fill: none; }
        .fi-blue svg { stroke: var(--primary); }
        .fi-cyan svg { stroke: var(--secondary-dk); }
        .fi-teal svg { stroke: var(--accent); }
        .fi-warn svg { stroke: var(--warning); }
        .feat-name { font-size: .95rem; font-weight: 700; color: var(--text); margin-bottom: 7px; }
        .feat-desc { font-size: .85rem; color: var(--text-2); line-height: 1.65; }

        /* ─── ACTORS ─── */
        .actor-grid {
            display: grid; grid-template-columns: repeat(3, 1fr);
            gap: 20px; margin-top: 56px;
        }
        .actor-card {
            border-radius: 16px; padding: 32px 28px;
            border: 1px solid var(--line);
            background: var(--surface); transition: all .22s;
        }
        .actor-card:hover {
            border-color: var(--primary);
            box-shadow: 0 8px 32px rgba(21,101,192,.1);
            transform: translateY(-3px);
        }
        .actor-num { font-size: .68rem; font-weight: 700; letter-spacing: .1em; text-transform: uppercase; color: var(--text-3); margin-bottom: 18px; }
        .actor-av { width: 50px; height: 50px; border-radius: 14px; display: flex; align-items: center; justify-content: center; margin-bottom: 16px; font-size: 22px; }
        .av-p { background: var(--primary-lt); }
        .av-s { background: var(--secondary-lt); }
        .av-a { background: var(--accent-lt); }
        .actor-name { font-size: 1.05rem; font-weight: 700; color: var(--text); margin-bottom: 4px; }
        .actor-sub { font-size: .78rem; color: var(--text-3); margin-bottom: 18px; }
        .actor-list { list-style: none; display: flex; flex-direction: column; gap: 9px; }
        .actor-list li {
            display: flex; align-items: flex-start; gap: 9px;
            font-size: .84rem; color: var(--text-2); line-height: 1.5;
        }
        .al-dot { width: 5px; height: 5px; border-radius: 50%; background: var(--primary); flex-shrink: 0; margin-top: 8px; opacity: .5; }

        /* ─── STEPS ─── */
        .steps-row {
            display: grid; grid-template-columns: repeat(4, 1fr);
            gap: 0; margin-top: 60px; position: relative;
        }
        .steps-row::before {
            content: ''; position: absolute; top: 26px; left: calc(12.5% + 1px); right: calc(12.5% + 1px);
            height: 1.5px; background: var(--line); z-index: 0;
        }
        .step-col { text-align: center; padding: 0 16px; position: relative; z-index: 1; }
        .step-num {
            width: 52px; height: 52px; border-radius: 50%;
            background: var(--surface); border: 2px solid var(--line);
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 20px; font-size: .9rem; font-weight: 700;
            color: var(--primary); transition: all .2s;
        }
        .step-col:hover .step-num {
            background: var(--primary); color: white; border-color: var(--primary);
            box-shadow: 0 6px 20px rgba(21,101,192,.3);
        }
        .step-name { font-size: .9rem; font-weight: 700; color: var(--text); margin-bottom: 6px; }
        .step-desc { font-size: .82rem; color: var(--text-2); line-height: 1.65; }

        /* ─── WHATSAPP ─── */
        .wa-wrap { display: grid; grid-template-columns: 1fr 1fr; gap: 80px; align-items: center; }
        .check-items { margin-top: 28px; display: flex; flex-direction: column; gap: 12px; }
        .check-item { display: flex; align-items: center; gap: 12px; font-size: .88rem; color: var(--text); }
        .chk {
            width: 22px; height: 22px; border-radius: 7px;
            background: var(--accent-lt); flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
        }
        .chk svg { width: 12px; height: 12px; stroke: var(--accent); fill: none; stroke-width: 2.5; stroke-linecap: round; stroke-linejoin: round; }

        .phone-outer {
            background: #1a1e2b; border-radius: 32px; padding: 14px;
            max-width: 272px; margin: 0 auto;
            box-shadow: 0 32px 80px rgba(21,101,192,.2), 0 0 0 1px rgba(255,255,255,.06);
        }
        .phone-notch { width: 72px; height: 5px; background: #2a2f3e; border-radius: 100px; margin: 0 auto 12px; }
        .phone-scr { background: #0d1117; border-radius: 20px; overflow: hidden; }
        .wa-bar { background: #1a2332; padding: 10px 13px; display: flex; align-items: center; gap: 9px; }
        .wa-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); display: flex; align-items: center; justify-content: center; font-size: 13px; }
        .wa-nm { font-size: .73rem; font-weight: 600; color: white; }
        .wa-st { font-size: .58rem; color: var(--secondary); }
        .wa-msgs { padding: 12px; }
        .wa-bbl { background: #054f43; border-radius: 12px 12px 12px 3px; padding: 10px 12px; }
        .wa-txt { font-size: .67rem; color: #e9edef; line-height: 1.7; }
        .wa-txt strong { color: white; }
        .wa-txt .hl-cyan { color: var(--secondary); font-weight: 600; }
        .wa-txt .hl-gold { color: #FFB74D; font-weight: 700; }
        .wa-ts { font-size: .57rem; color: rgba(255,255,255,.3); text-align: right; margin-top: 4px; }

        /* ─── TRANSPARANSI ─── */
        .trans-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-top: 56px; }
        .trans-card {
            border-radius: 16px; padding: 30px 26px;
            background: rgba(255,255,255,.07);
            border: 1px solid rgba(255,255,255,.1); transition: all .2s;
        }
        .trans-card:hover { background: rgba(255,255,255,.11); }
        .trans-num { font-size: 2.2rem; font-weight: 700; letter-spacing: -.03em; margin-bottom: 6px; }
        .tn-blue { color: var(--secondary); }
        .tn-teal { color: #4DB6AC; }
        .tn-amber { color: #FFD54F; }
        .trans-label { font-size: .85rem; font-weight: 600; color: rgba(255,255,255,.7); margin-bottom: 6px; }
        .trans-desc { font-size: .8rem; color: rgba(255,255,255,.35); line-height: 1.6; }

        /* ─── TECH ─── */
        .tech-chips { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-top: 40px; }
        .tech-chip {
            padding: 8px 16px;
            background: var(--surface); border: 1.5px solid var(--line);
            border-radius: 8px; font-size: .82rem; font-weight: 500;
            color: var(--text-2); transition: all .18s;
        }
        .tech-chip:hover { border-color: var(--primary); color: var(--primary); background: var(--primary-lt); }

        /* ─── CTA ─── */
        .cta-section {
            padding: 100px 0; text-align: center;
            background: linear-gradient(135deg, var(--primary-dk) 0%, var(--primary) 50%, #0097A7 100%);
            position: relative; overflow: hidden;
        }
        .cta-section::before {
            content: ''; position: absolute; inset: 0;
            background-image: linear-gradient(rgba(255,255,255,.04) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,.04) 1px, transparent 1px);
            background-size: 48px 48px; pointer-events: none;
        }
        .cta-inner { position: relative; z-index: 1; }
        .cta-title { font-size: clamp(1.9rem, 4vw, 3rem); font-weight: 700; color: white; letter-spacing: -.025em; margin-bottom: 16px; }
        .cta-sub { font-size: .95rem; color: rgba(255,255,255,.5); max-width: 440px; margin: 0 auto 36px; line-height: 1.7; }
        .cta-btns { display: flex; gap: 10px; justify-content: center; flex-wrap: wrap; }

        /* ─── FOOTER ─── */
        footer { background: var(--text); border-top: 3px solid var(--primary); padding: 60px 0 28px; }
        .footer-grid { display: grid; grid-template-columns: 2.2fr 1fr 1fr 1fr; gap: 48px; margin-bottom: 52px; }
        .ft-brand { display: flex; align-items: center; gap: 10px; text-decoration: none; margin-bottom: 14px; }
        .ft-logo {
            width: 34px; height: 34px; border-radius: 9px;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            display: flex; align-items: center; justify-content: center;
        }
        .ft-logo svg { width: 16px; height: 16px; fill: white; }
        .ft-name { font-size: .95rem; font-weight: 700; color: white; }
        .ft-desc { font-size: .82rem; color: rgba(255,255,255,.3); line-height: 1.75; margin-bottom: 16px; }
        .ft-address { font-size: .78rem; color: rgba(255,255,255,.25); line-height: 1.7; }
        .ft-col h5 { font-size: .68rem; font-weight: 700; text-transform: uppercase; letter-spacing: .1em; color: rgba(255,255,255,.3); margin-bottom: 16px; }
        .ft-col ul { list-style: none; display: flex; flex-direction: column; gap: 9px; }
        .ft-col a { font-size: .83rem; color: rgba(255,255,255,.35); text-decoration: none; transition: color .15s; }
        .ft-col a:hover { color: rgba(255,255,255,.75); }
        .footer-bar {
            border-top: 1px solid rgba(255,255,255,.06); padding-top: 24px;
            display: flex; align-items: center; justify-content: space-between;
            font-size: .74rem; color: rgba(255,255,255,.2); flex-wrap: wrap; gap: 8px;
        }

        /* ─── RESPONSIVE ─── */
        @media (max-width: 1024px) {
            .feat-grid { grid-template-columns: repeat(2, 1fr); }
            .actor-grid { grid-template-columns: repeat(2, 1fr); }
            .footer-grid { grid-template-columns: 1fr 1fr; }
            .trans-grid { grid-template-columns: repeat(2, 1fr); }
        }
        @media (max-width: 860px) {
            .hero-inner { flex-direction: column; padding-bottom: 120px; }
            .hero-panel { flex: none; width: 100%; max-width: 380px; }
            .about-grid { grid-template-columns: 1fr; gap: 40px; }
            .wa-wrap { grid-template-columns: 1fr; gap: 48px; }
            .steps-row { grid-template-columns: 1fr 1fr; gap: 32px; }
            .steps-row::before { display: none; }
        }
        @media (max-width: 600px) {
            .feat-grid { grid-template-columns: 1fr; }
            .actor-grid { grid-template-columns: 1fr; }
            .trans-grid { grid-template-columns: 1fr; }
            .footer-grid { grid-template-columns: 1fr; }
            .footer-bar { flex-direction: column; text-align: center; }
            .steps-row { grid-template-columns: 1fr; }
            .info-strip-inner { flex-direction: column; gap: 8px; }
        }
    </style>
</head>
<body>

{{-- INFO STRIP --}}
<div class="info-strip">
    <div class="container">
        <div class="info-strip-inner">
            <div class="info-item">
                <svg viewBox="0 0 24 24"><path d="M21 10c0 7-9 13-9 13S3 17 3 10a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                Lingkungan Sangket, Kelurahan Sukasada, Kabupaten Buleleng
            </div>
            <div class="info-item">
                <svg viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Layanan 06.00 — 18.00 WITA
            </div>
            <div class="info-item">
                <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7a2 2 0 011.72 2.05z"/></svg>
                Pengaduan via WhatsApp
            </div>
        </div>
    </div>
</div>

{{-- NAVBAR --}}
<nav class="nav" id="nav">
    <div class="nav-inner">
        <a href="/" class="nav-brand">
            <div class="nav-logo">
                <svg viewBox="0 0 24 24"><path d="M12 2C6 8 4 11 4 14a8 8 0 0016 0c0-3-2-6-8-12z"/></svg>
            </div>
            <div>
                <div>Toya Amerta</div>
                <div class="nav-tagline">Pengelolaan Air · Lingkungan Sangket</div>
            </div>
        </a>
        <div class="nav-actions">
            @if (Route::has('login'))
                @auth
                    <a href="{{ url('/dashboard') }}" class="btn btn-sm btn-primary">Dashboard</a>
                @else
                    <a href="{{ route('login') }}" class="btn btn-sm btn-ghost">Masuk</a>
                    <a href="{{ route('login') }}" class="btn btn-sm btn-primary">Portal Warga</a>
                @endauth
            @endif
        </div>
    </div>
</nav>

{{-- HERO --}}
<section class="hero">
    <div class="hero-grid"></div>
    <div class="hero-glow" style="width:400px;height:400px;top:-60px;right:20%;"></div>
    <div class="hero-glow" style="width:250px;height:250px;bottom:120px;left:10%;opacity:.6;"></div>

    <div class="container">
        <div class="hero-inner">
            <div class="hero-text">
                <div class="hero-chip">
                    <span class="chip-badge">Swadaya</span>
                    PDAM Lingkungan Sangket
                </div>
                <h1 class="hero-title">Toya Amerta</h1>
                <div class="hero-title-sub">Pengelolaan Air Bersih<br>Lingkungan Sangket</div>
                <p class="hero-desc">
                    Sistem pengelolaan air bersih berbasis swadaya masyarakat Lingkungan Sangket. Transparan, akuntabel, dan mudah diakses oleh seluruh warga dan pengelola.
                </p>
                <div class="hero-cta">
                    @if (Route::has('login'))
                        @auth
                            <a href="{{ url('/dashboard') }}" class="btn btn-lg btn-white">Buka Dashboard</a>
                        @else
                            <a href="{{ route('login') }}" class="btn btn-lg btn-white">Masuk ke Portal</a>
                        @endauth
                    @else
                        <a href="#tentang" class="btn btn-lg btn-white">Tentang Kami</a>
                    @endif
                    <a href="#fitur" class="btn btn-lg btn-white-ghost">Lihat Fitur</a>
                </div>
                <div class="hero-stats">
                    <div class="stat-block">
                        <div class="stat-val">100<span>%</span></div>
                        <div class="stat-key">Dikelola Warga</div>
                    </div>
                    <div class="stat-block">
                        <div class="stat-val">24<span>/7</span></div>
                        <div class="stat-key">Akses Informasi</div>
                    </div>
                    <div class="stat-block">
                        <div class="stat-val">0</div>
                        <div class="stat-key">Biaya Tersembunyi</div>
                    </div>
                </div>
            </div>

            {{-- Preview Panel --}}
            <div class="hero-panel">
                <div class="hp-head">
                    <span class="hp-title">Tagihan Aktif — {{ date('F Y') }}</span>
                    <span class="hp-live"><span class="hp-live-dot"></span>Live</span>
                </div>
                <div>
                    <div class="hp-row">
                        <div>
                            <div class="hp-name">I Wayan Sudiarsa</div>
                            <div class="hp-meta">18 m³ · No. 042</div>
                        </div>
                        <div class="hp-right">
                            <span class="s-pill s-lunas">Lunas</span>
                            <div class="hp-amt">Rp 54.000</div>
                        </div>
                    </div>
                    <div class="hp-row">
                        <div>
                            <div class="hp-name">Ni Made Suartini</div>
                            <div class="hp-meta">24 m³ · No. 078</div>
                        </div>
                        <div class="hp-right">
                            <span class="s-pill s-belum">Belum</span>
                            <div class="hp-amt">Rp 72.000</div>
                        </div>
                    </div>
                    <div class="hp-row">
                        <div>
                            <div class="hp-name">I Nyoman Kariasa</div>
                            <div class="hp-meta">12 m³ · No. 031</div>
                        </div>
                        <div class="hp-right">
                            <span class="s-pill s-lunas">Lunas</span>
                            <div class="hp-amt">Rp 36.000</div>
                        </div>
                    </div>
                    <div class="hp-row">
                        <div>
                            <div class="hp-name">Ni Ketut Wirani</div>
                            <div class="hp-meta">30 m³ · No. 055</div>
                        </div>
                        <div class="hp-right">
                            <span class="s-pill s-belum">Belum</span>
                            <div class="hp-amt">Rp 90.000</div>
                        </div>
                    </div>
                </div>
                <div class="hp-foot">
                    <span class="hp-fl">Terkumpul bulan ini</span>
                    <span class="hp-fv">Rp 90.000 <span class="hp-fv-muted">/ 252.000</span></span>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TENTANG --}}
<section class="sec sec-white" id="tentang">
    <div class="container">
        <div class="about-grid">
            <div>
                <div class="eyebrow eyebrow-teal">Tentang Toya Amerta</div>
                <h2 class="sec-title">Pengelolaan Air Bersih<br>Swadaya Warga Sangket</h2>
                <p class="sec-body">
                    Toya Amerta adalah lembaga pengelolaan air bersih berbasis swadaya masyarakat di Lingkungan Sangket, Desa Buleleng. Dibentuk atas inisiatif warga untuk memastikan ketersediaan air bersih yang adil, terjangkau, dan berkelanjutan bagi seluruh keluarga di lingkungan.
                </p>
                <p class="sec-body" style="margin-top: 14px;">
                    Dengan sistem digital ini, setiap tagihan, pembayaran, dan laporan keuangan dapat dipantau secara transparan oleh seluruh warga — mewujudkan pengelolaan yang bersih dan akuntabel.
                </p>
                <div class="about-stat-grid" style="margin-top: 32px;">
                    <div class="about-stat">
                        <div class="as-val">150+</div>
                        <div class="as-key">KK Terlayani</div>
                    </div>
                    <div class="about-stat">
                        <div class="as-val">5+ Th</div>
                        <div class="as-key">Melayani Warga</div>
                    </div>
                    <div class="about-stat">
                        <div class="as-val">100%</div>
                        <div class="as-key">Transparan</div>
                    </div>
                    <div class="about-stat">
                        <div class="as-val">Rp 0</div>
                        <div class="as-key">Biaya Tersembunyi</div>
                    </div>
                </div>
            </div>
            <div class="about-card">
                <div class="about-icon-row">
                    <div class="about-icon ai-primary">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6 8 4 11 4 14a8 8 0 0016 0c0-3-2-6-8-12z"/></svg>
                    </div>
                    <div>
                        <div style="font-weight:700;color:var(--text);margin-bottom:4px;">Visi</div>
                        <div style="font-size:.85rem;color:var(--text-2);line-height:1.65;">Mewujudkan pengelolaan air bersih yang adil, terjangkau, dan berkelanjutan bagi seluruh warga Lingkungan Sangket.</div>
                    </div>
                </div>
                <div class="about-icon-row">
                    <div class="about-icon ai-secondary">
                        <svg viewBox="0 0 24 24"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <div>
                        <div style="font-weight:700;color:var(--text);margin-bottom:4px;">Misi</div>
                        <div style="font-size:.85rem;color:var(--text-2);line-height:1.65;">Mengelola distribusi air bersih secara efisien, transparan, dan akuntabel berbasis teknologi digital.</div>
                    </div>
                </div>
                <div class="about-icon-row" style="margin-bottom:0">
                    <div class="about-icon ai-accent">
                        <svg viewBox="0 0 24 24"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87"/><path d="M16 3.13a4 4 0 010 7.75"/></svg>
                    </div>
                    <div>
                        <div style="font-weight:700;color:var(--text);margin-bottom:4px;">Nilai</div>
                        <div style="font-size:.85rem;color:var(--text-2);line-height:1.65;">Gotong royong, keterbukaan, dan keberlanjutan sebagai pondasi pengelolaan bersama.</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- FEATURES --}}
<section class="sec sec-bg" id="fitur">
    <div class="container">
        <div class="text-center">
            <div class="eyebrow">Fitur Sistem</div>
            <h2 class="sec-title">Semua Kebutuhan Pengelolaan<br>dalam Satu Platform</h2>
            <p class="sec-body" style="margin: 0 auto;">Dari pencatatan meter hingga notifikasi WhatsApp otomatis — dirancang khusus untuk kebutuhan PDAM swadaya desa.</p>
        </div>
        <div class="feat-grid">
            <div class="feat-cell">
                <div class="feat-icon fi-cyan">
                    <svg viewBox="0 0 24 24"><rect x="5" y="2" width="14" height="20" rx="2"/><line x1="9" y1="7" x2="15" y2="7"/><line x1="9" y1="11" x2="15" y2="11"/><line x1="9" y1="15" x2="12" y2="15"/></svg>
                </div>
                <div class="feat-name">Pencatatan Meter Digital</div>
                <div class="feat-desc">Petugas mencatat angka meter langsung dari smartphone, lengkap dengan foto bukti yang tersimpan otomatis di sistem.</div>
            </div>
            <div class="feat-cell">
                <div class="feat-icon fi-teal">
                    <svg viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.68A2 2 0 012 .18h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7a2 2 0 011.72 2.05z"/></svg>
                </div>
                <div class="feat-name">Notifikasi WhatsApp Otomatis</div>
                <div class="feat-desc">Tagihan bulanan dikirim langsung ke WhatsApp warga. Tidak perlu datang ke kantor untuk tahu jumlah tagihan.</div>
            </div>
            <div class="feat-cell">
                <div class="feat-icon fi-blue">
                    <svg viewBox="0 0 24 24"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
                </div>
                <div class="feat-name">Laporan Keuangan Terbuka</div>
                <div class="feat-desc">Seluruh pemasukan dan pengeluaran tercatat rapi dan dapat diakses — mendukung pengelolaan yang transparan kepada warga.</div>
            </div>
            <div class="feat-cell">
                <div class="feat-icon fi-warn">
                    <svg viewBox="0 0 24 24"><path d="M14.7 6.3a1 1 0 00-1.4 0l-8 8a1 1 0 000 1.4l2 2a1 1 0 001.4 0l8-8a1 1 0 000-1.4l-2-2z"/><path d="M4 20l.7-2.3 1.6 1.6z"/></svg>
                </div>
                <div class="feat-name">Laporan Gangguan & Perbaikan</div>
                <div class="feat-desc">Warga dan petugas dapat melaporkan kerusakan pipa, kebocoran, atau gangguan distribusi air secara langsung.</div>
            </div>
            <div class="feat-cell">
                <div class="feat-icon fi-cyan">
                    <svg viewBox="0 0 24 24"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                </div>
                <div class="feat-name">Konfirmasi Pembayaran Tunai</div>
                <div class="feat-desc">Petugas mengkonfirmasi pembayaran langsung di sistem saat menerima iuran dari warga. Bukti tersimpan digital.</div>
            </div>
            <div class="feat-cell">
                <div class="feat-icon fi-blue">
                    <svg viewBox="0 0 24 24"><path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>
                </div>
                <div class="feat-name">Portal Mandiri Warga</div>
                <div class="feat-desc">Setiap warga dapat melihat tagihan, riwayat pemakaian air, dan status pembayaran kapan saja melalui aplikasi.</div>
            </div>
        </div>
    </div>
</section>

{{-- ACTORS --}}
<section class="sec sec-white" id="pengguna">
    <div class="container">
        <div class="text-center">
            <div class="eyebrow eyebrow-cyan">Pengguna Sistem</div>
            <h2 class="sec-title">Dirancang untuk Semua<br>Pihak yang Terlibat</h2>
            <p class="sec-body" style="margin: 0 auto;">Tiga kelompok pengguna dengan akses yang disesuaikan — dari pengurus hingga warga pelanggan.</p>
        </div>
        <div class="actor-grid">
            <div class="actor-card">
                <div class="actor-num">01 — Pengurus</div>
                <div class="actor-av av-p">👨‍💼</div>
                <div class="actor-name">Admin / Pengurus</div>
                <div class="actor-sub">Ketua & bendahara pengelola air</div>
                <ul class="actor-list">
                    <li><span class="al-dot"></span>Kelola data warga &amp; tarif iuran</li>
                    <li><span class="al-dot"></span>Buat &amp; approve tagihan bulanan</li>
                    <li><span class="al-dot"></span>Laporan keuangan &amp; rekapitulasi</li>
                    <li><span class="al-dot"></span>Pantau status pembayaran seluruh warga</li>
                    <li><span class="al-dot"></span>Dashboard analitik &amp; grafik kas</li>
                </ul>
            </div>
            <div class="actor-card">
                <div class="actor-num">02 — Petugas</div>
                <div class="actor-av av-s">👷</div>
                <div class="actor-name">Petugas Lapangan</div>
                <div class="actor-sub">Pembaca meter &amp; penarik iuran</div>
                <ul class="actor-list">
                    <li><span class="al-dot"></span>Input pembacaan meter air tiap bulan</li>
                    <li><span class="al-dot"></span>Upload foto meter sebagai bukti valid</li>
                    <li><span class="al-dot"></span>Konfirmasi penerimaan iuran tunai</li>
                    <li><span class="al-dot"></span>Laporan kerusakan &amp; kebocoran pipa</li>
                    <li><span class="al-dot"></span>Update status perbaikan infrastruktur</li>
                </ul>
            </div>
            <div class="actor-card">
                <div class="actor-num">03 — Warga</div>
                <div class="actor-av av-a">🏡</div>
                <div class="actor-name">Warga Pelanggan</div>
                <div class="actor-sub">Seluruh KK yang terdaftar</div>
                <ul class="actor-list">
                    <li><span class="al-dot"></span>Cek tagihan bulan berjalan</li>
                    <li><span class="al-dot"></span>Riwayat pemakaian air bulanan</li>
                    <li><span class="al-dot"></span>Riwayat pembayaran lengkap</li>
                    <li><span class="al-dot"></span>Notifikasi tagihan via WhatsApp</li>
                    <li><span class="al-dot"></span>Laporan gangguan distribusi air</li>
                </ul>
            </div>
        </div>
    </div>
</section>

{{-- HOW IT WORKS --}}
<section class="sec sec-bg" id="cara-kerja">
    <div class="container">
        <div class="text-center">
            <div class="eyebrow">Alur Kerja</div>
            <h2 class="sec-title">Proses Sederhana,<br>Hasil Maksimal</h2>
            <p class="sec-body" style="margin: 0 auto;">Dari pembacaan meter hingga konfirmasi pembayaran — semua terekam digital dan dapat dipantau warga.</p>
        </div>
        <div class="steps-row">
            <div class="step-col">
                <div class="step-num">1</div>
                <div class="step-name">Baca Meter</div>
                <div class="step-desc">Petugas membaca &amp; menginput angka meter air di rumah warga beserta foto bukti.</div>
            </div>
            <div class="step-col">
                <div class="step-num">2</div>
                <div class="step-name">Hitung Tagihan</div>
                <div class="step-desc">Sistem otomatis menghitung tagihan berdasarkan pemakaian m³ dan tarif yang berlaku.</div>
            </div>
            <div class="step-col">
                <div class="step-num">3</div>
                <div class="step-name">Notif WhatsApp</div>
                <div class="step-desc">Tagihan dikirim otomatis ke WhatsApp warga — detail pemakaian dan jumlah yang harus dibayar.</div>
            </div>
            <div class="step-col">
                <div class="step-num">4</div>
                <div class="step-name">Konfirmasi Bayar</div>
                <div class="step-desc">Setelah iuran diterima, petugas mengkonfirmasi di sistem. Status warga langsung update ke Lunas.</div>
            </div>
        </div>
    </div>
</section>

{{-- WHATSAPP --}}
<section class="sec sec-white">
    <div class="container">
        <div class="wa-wrap">
            <div>
                <div class="eyebrow eyebrow-teal">Notifikasi WhatsApp</div>
                <h2 class="sec-title">Tagihan Langsung<br>ke WhatsApp Warga</h2>
                <p class="sec-body">Setiap warga mendapat notifikasi tagihan air bulanan, pengingat jatuh tempo, dan konfirmasi lunas langsung di WhatsApp — tanpa perlu datang ke kantor atau menunggu surat tagihan.</p>
                <div class="check-items">
                    <div class="check-item">
                        <span class="chk"><svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg></span>
                        Notifikasi tagihan bulanan otomatis
                    </div>
                    <div class="check-item">
                        <span class="chk"><svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg></span>
                        Pengingat menjelang jatuh tempo
                    </div>
                    <div class="check-item">
                        <span class="chk"><svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg></span>
                        Konfirmasi pembayaran lunas
                    </div>
                    <div class="check-item">
                        <span class="chk"><svg viewBox="0 0 12 12"><polyline points="2 6 5 9 10 3"/></svg></span>
                        Pemberitahuan gangguan distribusi air
                    </div>
                </div>
            </div>
            <div class="phone-outer">
                <div class="phone-notch"></div>
                <div class="phone-scr">
                    <div class="wa-bar">
                        <div class="wa-av">💧</div>
                        <div>
                            <div class="wa-nm">PDAM Toya Amerta</div>
                            <div class="wa-st">● Online</div>
                        </div>
                    </div>
                    <div class="wa-msgs">
                        <div class="wa-bbl">
                            <div class="wa-txt">
                                Yth. Bpk <strong>I Wayan Sudiarsa</strong><br><br>
                                <strong>📋 TAGIHAN AIR BERSIH</strong><br>
                                <strong>{{ date('F Y') }} — Lk. Sangket</strong><br>
                                ──────────────────<br>
                                Meter Awal &nbsp;: 1.240 m³<br>
                                Meter Akhir : 1.258 m³<br>
                                Pemakaian &nbsp;: <span class="hl-cyan">18 m³</span><br>
                                ──────────────────<br>
                                Total Tagihan:<br>
                                <span class="hl-gold">Rp 54.000,-</span><br><br>
                                Jatuh tempo: 20 {{ date('F Y') }}<br><br>
                                Mohon segera dilunasi.<br>
                                Terima kasih 🙏
                            </div>
                            <div class="wa-ts">08.15 ✓✓</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- TRANSPARANSI --}}
<section class="sec sec-primary">
    <div class="container">
        <div class="text-center">
            <div class="eyebrow eyebrow-white">Komitmen Kami</div>
            <h2 class="sec-title sec-title-white">Pengelolaan yang Transparan<br>untuk Warga Sangket</h2>
            <p class="sec-body sec-body-white" style="margin: 0 auto;">Setiap rupiah iuran warga dikelola secara terbuka — dapat dipantau kapan saja, oleh siapa saja.</p>
        </div>
        <div class="trans-grid">
            <div class="trans-card">
                <div class="trans-num tn-blue">Rp 0</div>
                <div class="trans-label">Biaya Tersembunyi</div>
                <div class="trans-desc">Tidak ada pungutan di luar tarif yang telah disepakati bersama dalam musyawarah warga.</div>
            </div>
            <div class="trans-card">
                <div class="trans-num tn-teal">100%</div>
                <div class="trans-label">Laporan Terbuka</div>
                <div class="trans-desc">Seluruh catatan keuangan bulanan dapat diakses warga melalui portal digital kapan saja.</div>
            </div>
            <div class="trans-card">
                <div class="trans-num tn-amber">Bulanan</div>
                <div class="trans-label">Rekap Kas Rutin</div>
                <div class="trans-desc">Rekapitulasi kas dan laporan operasional diterbitkan setiap bulan untuk pertanggungjawaban kepada warga.</div>
            </div>
        </div>
    </div>
</section>

{{-- TECH STACK --}}
<section class="sec sec-bg">
    <div class="container text-center">
        <div class="eyebrow">Teknologi</div>
        <h2 class="sec-title">Dibangun dengan Teknologi<br>Modern &amp; Terpercaya</h2>
        <p class="sec-body" style="margin: 0 auto;">Kombinasi teknologi yang menjamin keamanan data warga, kemudahan akses, dan keandalan sistem jangka panjang.</p>
        <div class="tech-chips">
            <span class="tech-chip">Laravel 13</span>
            <span class="tech-chip">PHP 8.3</span>
            <span class="tech-chip">MySQL 8</span>
            <span class="tech-chip">Filament 3</span>
            <span class="tech-chip">Flutter 3</span>
            <span class="tech-chip">Fonnte WhatsApp API</span>
            <span class="tech-chip">Laravel Sanctum</span>
            <span class="tech-chip">GetX</span>
            <span class="tech-chip">Redis</span>
            <span class="tech-chip">Docker</span>
            <span class="tech-chip">S3 Storage</span>
            <span class="tech-chip">Clean Architecture</span>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="cta-section">
    <div class="container">
        <div class="cta-inner">
            <h2 class="cta-title">Bersama Kelola Air Bersih<br>Lingkungan Sangket</h2>
            <p class="cta-sub">Bergabung dalam sistem pengelolaan air swadaya yang transparan dan akuntabel untuk kesejahteraan bersama.</p>
            <div class="cta-btns">
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="btn btn-lg btn-white">Buka Dashboard</a>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-lg btn-white">Masuk ke Portal</a>
                    @endauth
                @endif
                <a href="#tentang" class="btn btn-lg btn-white-ghost">Pelajari Lebih Lanjut</a>
            </div>
        </div>
    </div>
</section>

{{-- FOOTER --}}
<footer>
    <div class="container">
        <div class="footer-grid">
            <div>
                <a href="/" class="ft-brand">
                    <div class="ft-logo">
                        <svg viewBox="0 0 24 24"><path d="M12 2C6 8 4 11 4 14a8 8 0 0016 0c0-3-2-6-8-12z"/></svg>
                    </div>
                    <span class="ft-name">Toya Amerta</span>
                </a>
                <p class="ft-desc">Sistem pengelolaan air bersih berbasis swadaya masyarakat Lingkungan Sangket — transparan, akuntabel, dan berkelanjutan.</p>
                <p class="ft-address">
                    Lingkungan Sangket, Desa Buleleng<br>
                    Kabupaten Buleleng, Bali
                </p>
            </div>
            <div class="ft-col">
                <h5>Informasi</h5>
                <ul>
                    <li><a href="#tentang">Tentang Kami</a></li>
                    <li><a href="#fitur">Fitur Sistem</a></li>
                    <li><a href="#pengguna">Pengguna</a></li>
                    <li><a href="#cara-kerja">Alur Kerja</a></li>
                </ul>
            </div>
            <div class="ft-col">
                <h5>Akses Portal</h5>
                <ul>
                    @if (Route::has('login'))
                        <li><a href="{{ route('login') }}">Login Pengurus</a></li>
                        <li><a href="{{ route('login') }}">Login Petugas</a></li>
                    @endif
                    <li><a href="#">Download App Warga</a></li>
                    <li><a href="#">Cek Tagihan</a></li>
                </ul>
            </div>
            <div class="ft-col">
                <h5>Pengaduan</h5>
                <ul>
                    <li><a href="#">Lapor Gangguan Air</a></li>
                    <li><a href="#">Lapor Kebocoran Pipa</a></li>
                    <li><a href="#">Hubungi Pengurus</a></li>
                    <li><a href="#">WhatsApp Petugas</a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bar">
            <span>© {{ date('Y') }} Toya Amerta — Pengelolaan Air Bersih Lingkungan Sangket</span>
            <span>Laravel v{{ app()->version() }} · PHP {{ PHP_VERSION }}</span>
        </div>
    </div>
</footer>

<script>
    const nav = document.getElementById('nav');
    window.addEventListener('scroll', () => {
        nav.classList.toggle('scrolled', window.scrollY > 8);
    }, { passive: true });

    document.querySelectorAll('a[href^="#"]').forEach(a => {
        a.addEventListener('click', e => {
            const t = document.querySelector(a.getAttribute('href'));
            if (t) { e.preventDefault(); t.scrollIntoView({ behavior: 'smooth' }); }
        });
    });
</script>

</body>
</html>
