<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILENT SOVEREIGN — Civilization Simulator</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Share+Tech+Mono&family=Orbitron:wght@400;700;900&family=Rajdhani:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        :root {
            --void: #03040a;
            --deep: #060c16;
            --panel: #0b1220;
            --border: #1a2a3a;
            --accent: #00e5ff;
            --accent2: #7b2fff;
            --gold: #f0c040;
            --danger: #ff3860;
            --success: #00e676;
            --text: #c8d8e8;
            --muted: #4a6070;
            --glow: 0 0 20px rgba(0,229,255,0.3);
            --glow2: 0 0 20px rgba(123,47,255,0.3);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            background: var(--void);
            color: var(--text);
            font-family: 'Rajdhani', sans-serif;
            font-size: 15px;
            min-height: 100vh;
            overflow-x: hidden;
        }
        body::before {
            content: '';
            position: fixed; inset: 0;
            background-image:
                radial-gradient(1px 1px at 15% 25%, rgba(0,229,255,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 75% 10%, rgba(255,255,255,0.3) 0%, transparent 100%),
                radial-gradient(1px 1px at 40% 80%, rgba(123,47,255,0.4) 0%, transparent 100%),
                radial-gradient(1px 1px at 90% 55%, rgba(255,255,255,0.2) 0%, transparent 100%),
                radial-gradient(1px 1px at 5% 65%, rgba(0,229,255,0.3) 0%, transparent 100%),
                radial-gradient(2px 2px at 55% 40%, rgba(240,192,64,0.2) 0%, transparent 100%);
            pointer-events: none;
            z-index: 0;
        }
        .scanline {
            position: fixed; inset: 0;
            background: repeating-linear-gradient(
                0deg,
                transparent,
                transparent 2px,
                rgba(0,229,255,0.015) 2px,
                rgba(0,229,255,0.015) 4px
            );
            pointer-events: none;
            z-index: 1;
        }
        header {
            position: relative;
            z-index: 10;
            padding: 32px 48px 20px;
            border-bottom: 1px solid var(--border);
            background: linear-gradient(180deg, rgba(0,229,255,0.04) 0%, transparent 100%);
            display: flex;
            align-items: center;
            gap: 24px;
        }
        .logo-glyph {
            font-family: 'Orbitron', monospace;
            font-size: 42px;
            font-weight: 900;
            color: var(--accent);
            text-shadow: 0 0 30px var(--accent), 0 0 60px rgba(0,229,255,0.4);
            letter-spacing: -2px;
            animation: pulse-glow 3s ease-in-out infinite;
        }
        @keyframes pulse-glow {
            0%, 100% { text-shadow: 0 0 30px var(--accent), 0 0 60px rgba(0,229,255,0.4); }
            50%       { text-shadow: 0 0 50px var(--accent), 0 0 100px rgba(0,229,255,0.6), 0 0 140px rgba(0,229,255,0.2); }
        }
        .header-text h1 {
            font-family: 'Orbitron', monospace;
            font-size: 20px;
            font-weight: 700;
            color: #fff;
            letter-spacing: 4px;
            text-transform: uppercase;
        }
        .header-text p {
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--muted);
            letter-spacing: 2px;
            margin-top: 4px;
        }
        .status-bar {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 16px;
        }
        .status-dot {
            width: 8px; height: 8px;
            border-radius: 50%;
            background: var(--success);
            box-shadow: 0 0 10px var(--success);
            animation: blink 2s ease-in-out infinite;
        }
        @keyframes blink {
            0%, 100% { opacity: 1; }
            50%       { opacity: 0.3; }
        }
        .status-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--success);
            letter-spacing: 2px;
        }
        main {
            position: relative;
            z-index: 5;
            display: grid;
            grid-template-columns: 420px 1fr;
            gap: 0;
            height: calc(100vh - 105px);
        }
        .control-panel {
            background: var(--panel);
            border-right: 1px solid var(--border);
            overflow-y: auto;
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        .control-panel::-webkit-scrollbar { width: 4px; }
        .control-panel::-webkit-scrollbar-track { background: transparent; }
        .control-panel::-webkit-scrollbar-thumb { background: var(--border); border-radius: 2px; }
        .section-label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            letter-spacing: 3px;
            color: var(--accent);
            text-transform: uppercase;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1px solid rgba(0,229,255,0.15);
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-label::before {
            content: '▸';
            color: var(--accent2);
        }
        .mode-tabs {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 2px;
            background: var(--void);
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 3px;
        }
        .mode-tab {
            padding: 10px;
            text-align: center;
            cursor: pointer;
            border-radius: 4px;
            font-family: 'Orbitron', monospace;
            font-size: 10px;
            letter-spacing: 2px;
            color: var(--muted);
            transition: all 0.2s;
            user-select: none;
        }
        .mode-tab.active {
            background: linear-gradient(135deg, rgba(0,229,255,0.15), rgba(123,47,255,0.15));
            color: var(--accent);
            box-shadow: var(--glow);
        }
        .mode-tab:hover:not(.active) {
            color: var(--text);
            background: rgba(255,255,255,0.03);
        }
        .upload-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 8px;
        }
        .upload-zone {
            border: 1px dashed var(--border);
            border-radius: 6px;
            padding: 14px 10px;
            text-align: center;
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            background: rgba(0,0,0,0.3);
        }
        .upload-zone:hover, .upload-zone.drag-over {
            border-color: var(--accent);
            background: rgba(0,229,255,0.05);
            box-shadow: var(--glow);
        }
        .upload-zone.uploaded {
            border-color: var(--success);
            background: rgba(0,230,118,0.05);
        }
        .upload-zone input[type=file] {
            position: absolute; inset: 0;
            opacity: 0; cursor: pointer;
            width: 100%; height: 100%;
        }
        .upload-icon { font-size: 20px; margin-bottom: 4px; }
        .upload-name {
            font-family: 'Share Tech Mono', monospace;
            font-size: 9px;
            color: var(--accent);
            letter-spacing: 1px;
            margin-bottom: 2px;
        }
        .upload-status {
            font-size: 10px;
            color: var(--muted);
        }
        .upload-zone.uploaded .upload-status { color: var(--success); }
        .field-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        .field {
            display: flex;
            flex-direction: column;
            gap: 5px;
        }
        .field.full { grid-column: 1 / -1; }
        label {
            font-family: 'Share Tech Mono', monospace;
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase;
        }
        input[type=number], input[type=text], select, input[type=password] {
            background: var(--void);
            border: 1px solid var(--border);
            color: var(--text);
            padding: 8px 12px;
            border-radius: 4px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s, box-shadow 0.2s;
            width: 100%;
        }
        input[type=number]:focus, input[type=text]:focus, select:focus, input[type=password]:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 2px rgba(0,229,255,0.1);
        }
        select option { background: var(--deep); }
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid rgba(255,255,255,0.03);
        }
        .toggle-label {
            font-size: 13px;
            color: var(--text);
        }
        .toggle-sub {
            font-family: 'Share Tech Mono', monospace;
            font-size: 9px;
            color: var(--muted);
            letter-spacing: 1px;
            margin-top: 2px;
        }
        .toggle {
            position: relative;
            width: 42px; height: 22px;
            flex-shrink: 0;
        }
        .toggle input { opacity: 0; width: 0; height: 0; }
        .toggle-slider {
            position: absolute; inset: 0;
            background: var(--void);
            border: 1px solid var(--border);
            border-radius: 22px;
            cursor: pointer;
            transition: all 0.3s;
        }
        .toggle-slider::before {
            content: '';
            position: absolute;
            width: 16px; height: 16px;
            left: 2px; top: 2px;
            background: var(--muted);
            border-radius: 50%;
            transition: all 0.3s;
        }
        .toggle input:checked + .toggle-slider {
            background: rgba(0,229,255,0.1);
            border-color: var(--accent);
        }
        .toggle input:checked + .toggle-slider::before {
            transform: translateX(20px);
            background: var(--accent);
            box-shadow: 0 0 8px var(--accent);
        }
        input[type=range] {
            -webkit-appearance: none;
            width: 100%;
            height: 4px;
            background: var(--border);
            border-radius: 2px;
            outline: none;
            border: none;
            padding: 0;
        }
        input[type=range]::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 14px; height: 14px;
            border-radius: 50%;
            background: var(--accent);
            cursor: pointer;
            box-shadow: 0 0 8px var(--accent);
        }
        .range-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .range-val {
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px;
            color: var(--accent);
            width: 40px;
            text-align: right;
            flex-shrink: 0;
        }
        .run-btn {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, rgba(0,229,255,0.15), rgba(123,47,255,0.15));
            border: 1px solid var(--accent);
            color: var(--accent);
            font-family: 'Orbitron', monospace;
            font-size: 13px;
            font-weight: 700;
            letter-spacing: 4px;
            text-transform: uppercase;
            cursor: pointer;
            border-radius: 6px;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }
        .run-btn::before {
            content: '';
            position: absolute; inset: 0;
            background: linear-gradient(135deg, rgba(0,229,255,0.05), rgba(123,47,255,0.05));
            opacity: 0;
            transition: opacity 0.3s;
        }
        .run-btn:hover:not(:disabled) {
            box-shadow: var(--glow), var(--glow2);
            transform: translateY(-1px);
        }
        .run-btn:hover::before { opacity: 1; }
        .run-btn:disabled {
            opacity: 0.4;
            cursor: not-allowed;
            transform: none;
        }
        .run-btn.running {
            border-color: var(--gold);
            color: var(--gold);
            animation: run-pulse 1s ease-in-out infinite;
        }
        @keyframes run-pulse {
            0%, 100% { box-shadow: 0 0 10px rgba(240,192,64,0.3); }
            50%       { box-shadow: 0 0 30px rgba(240,192,64,0.6); }
        }
        .output-panel {
            display: grid;
            grid-template-rows: 1fr 280px;
            overflow: hidden;
        }
        .terminal-wrap {
            padding: 24px;
            display: flex;
            flex-direction: column;
            gap: 16px;
            overflow: hidden;
        }
        .terminal-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .terminal-title {
            font-family: 'Orbitron', monospace;
            font-size: 11px;
            letter-spacing: 3px;
            color: var(--muted);
        }
        .terminal {
            flex: 1;
            background: #02060e;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 16px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px;
            line-height: 1.7;
            color: #7ec8e3;
            overflow-y: auto;
            position: relative;
        }
        .terminal::-webkit-scrollbar { width: 4px; }
        .terminal::-webkit-scrollbar-thumb { background: var(--border); }
        .terminal .line-err   { color: var(--danger); }
        .terminal .line-ok    { color: var(--success); }
        .terminal .line-warn  { color: var(--gold); }
        .terminal .line-head  { color: var(--accent); font-weight: bold; }
        .terminal .line-dim   { color: var(--muted); }
        .cursor {
            display: inline-block;
            width: 8px; height: 14px;
            background: var(--accent);
            animation: blink 1s step-end infinite;
            vertical-align: middle;
            margin-left: 2px;
        }
        .results-panel {
            border-top: 1px solid var(--border);
            background: var(--panel);
            padding: 20px 24px;
            overflow-y: auto;
        }
        .results-header {
            font-family: 'Orbitron', monospace;
            font-size: 11px;
            letter-spacing: 3px;
            color: var(--muted);
            margin-bottom: 14px;
        }
        .results-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100px;
            color: var(--muted);
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            letter-spacing: 2px;
        }
        .files-grid {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-bottom: 16px;
        }
        .file-chip {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 7px 14px;
            background: var(--void);
            border: 1px solid var(--border);
            border-radius: 20px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 11px;
            color: var(--text);
            text-decoration: none;
            transition: all 0.2s;
        }
        .file-chip:hover {
            border-color: var(--accent);
            color: var(--accent);
            box-shadow: var(--glow);
        }
        .file-chip .ext {
            font-size: 9px;
            padding: 2px 5px;
            border-radius: 3px;
            background: rgba(0,229,255,0.1);
            color: var(--accent);
        }
        .file-chip .ext.csv  { background: rgba(0,230,118,0.1); color: var(--success); }
        .file-chip .ext.png  { background: rgba(123,47,255,0.1); color: var(--accent2); }
        .file-chip .ext.zip  { background: rgba(240,192,64,0.1); color: var(--gold); }
        .zip-btn {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            padding: 12px 24px;
            background: linear-gradient(135deg, rgba(240,192,64,0.15), rgba(123,47,255,0.15));
            border: 1px solid var(--gold);
            color: var(--gold);
            text-decoration: none;
            font-family: 'Orbitron', monospace;
            font-size: 11px;
            letter-spacing: 3px;
            border-radius: 6px;
            transition: all 0.3s;
        }
        .zip-btn:hover {
            box-shadow: 0 0 20px rgba(240,192,64,0.3);
            transform: translateY(-1px);
        }
        .progress-wrap {
            margin-bottom: 8px;
        }
        .progress-bar {
            height: 3px;
            background: var(--border);
            border-radius: 2px;
            overflow: hidden;
        }
        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--accent), var(--accent2));
            width: 0;
            transition: width 0.5s ease;
            border-radius: 2px;
        }
        .progress-fill.indeterminate {
            width: 40%;
            animation: indeterminate 1.5s ease-in-out infinite;
        }
        @keyframes indeterminate {
            0%   { transform: translateX(-100%); }
            100% { transform: translateX(350%); }
        }
        .toast {
            position: fixed;
            bottom: 24px; right: 24px;
            padding: 14px 20px;
            border-radius: 6px;
            font-family: 'Share Tech Mono', monospace;
            font-size: 12px;
            z-index: 1000;
            transform: translateY(80px);
            opacity: 0;
            transition: all 0.3s;
            max-width: 360px;
        }
        .toast.show { transform: translateY(0); opacity: 1; }
        .toast.success { background: rgba(0,230,118,0.1); border: 1px solid var(--success); color: var(--success); }
        .toast.error   { background: rgba(255,56,96,0.1); border: 1px solid var(--danger); color: var(--danger); }
        @media (max-width: 900px) {
            main { grid-template-columns: 1fr; grid-template-rows: auto 1fr; height: auto; }
            .control-panel { max-height: 60vh; }
            header { padding: 20px 24px 14px; }
        }
        .collapsible-header {
            cursor: pointer;
            user-select: none;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .collapsible-header .arrow {
            font-size: 10px;
            color: var(--muted);
            transition: transform 0.2s;
        }
        .collapsible-header.open .arrow { transform: rotate(90deg); }
        .collapsible-body { overflow: hidden; transition: max-height 0.3s ease; }
        .collapsible-body.hidden { max-height: 0 !important; }
        .s5-only, .qnvm-only { display: none; }
        .mode-s5 .s5-only    { display: block; }
        .mode-qnvm .qnvm-only { display: block; }
    </style>
</head>
<body>
<div class="scanline"></div>

<header>
    <div class="logo-glyph">⬡</div>
    <div class="header-text">
        <h1>Silent Sovereign</h1>
        <p>STAGE 5 CIVILIZATION SIMULATOR · DEMIURGE EMERGENCE PROTOCOL</p>
    </div>
    <div class="status-bar">
        <div class="status-dot"></div>
        <span class="status-label">SYSTEM ONLINE</span>
    </div>
</header>

<main id="app" class="mode-s5">

    <!-- LEFT: Controls -->
    <aside class="control-panel">
        <!-- Mode -->
        <div>
            <div class="section-label">Simulation Mode</div>
            <div class="mode-tabs">
                <div class="mode-tab active" data-mode="s5" onclick="setMode('s5',this)">STAGE 5<br><span style="font-size:8px;color:var(--muted)">Full S5 Core</span></div>
                <div class="mode-tab" data-mode="qnvm" onclick="setMode('qnvm',this)">QNVM LIGHT<br><span style="font-size:8px;color:var(--muted)">Enhanced Legacy</span></div>
            </div>
        </div>

        <!-- Upload -->
        <div>
            <div class="section-label collapsible-header open" onclick="toggleSection(this)">
                Script Upload<span class="arrow">▸</span>
            </div>
            <div class="collapsible-body" style="max-height:500px">
                <div class="upload-grid s5-only" style="display:grid">
                    <div class="upload-zone" id="zone-s5_core">
                        <input type="file" id="file-s5_core" name="s5_core" accept=".py">
                        <div class="upload-icon">📜</div>
                        <div class="upload-name">s5_core.py</div>
                        <div class="upload-status">Drop or click</div>
                    </div>
                    <div class="upload-zone" id="zone-s5_runner">
                        <input type="file" id="file-s5_runner" name="s5_runner" accept=".py">
                        <div class="upload-icon">🚀</div>
                        <div class="upload-name">s5_runner.py</div>
                        <div class="upload-status">Drop or click</div>
                    </div>
                    <div class="upload-zone" id="zone-s5_analysis">
                        <input type="file" id="file-s5_analysis" name="s5_analysis" accept=".py">
                        <div class="upload-icon">📊</div>
                        <div class="upload-name">s5_analysis.py</div>
                        <div class="upload-status">Drop or click</div>
                    </div>
                    <div class="upload-zone" style="border-style:solid;border-color:var(--muted);opacity:0.5;cursor:default">
                        <div class="upload-icon">⚙️</div>
                        <div class="upload-name" style="color:var(--muted)">plugins/</div>
                        <div class="upload-status">Optional (auto-loaded)</div>
                    </div>
                </div>
                <div class="qnvm-only" style="display:none; margin-top:8px">
                    <div class="upload-zone" id="zone-qnvm_light" style="max-width:200px">
                        <input type="file" id="file-qnvm_light" name="qnvm_light" accept=".py">
                        <div class="upload-icon">🌐</div>
                        <div class="upload-name">qnvm_light.py</div>
                        <div class="upload-status">Drop or click</div>
                    </div>
                </div>
                <!-- Plugin JSON upload -->
                <div style="margin-top:16px; border-top:1px dashed var(--border); padding-top:12px;">
                    <div style="font-family:'Share Tech Mono'; font-size:10px; color:var(--muted); margin-bottom:8px;">
                        PLUGIN JSON (optional, multiple)
                    </div>
                    <div class="upload-zone" id="zone-plugins">
                        <input type="file" id="file-plugins" name="plugins[]" accept=".json" multiple>
                        <div class="upload-icon">🔌</div>
                        <div class="upload-name">*.json</div>
                        <div class="upload-status">Drop or click (multiple)</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common params -->
        <div>
            <div class="section-label collapsible-header open" onclick="toggleSection(this)">
                Core Parameters<span class="arrow">▸</span>
            </div>
            <div class="collapsible-body" style="max-height:400px">
                <div class="field-grid">
                    <div class="field">
                        <label>Generations</label>
                        <input type="number" name="generations" id="generations" value="100" min="1" max="10000">
                    </div>
                    <div class="field">
                        <label>Init Population</label>
                        <input type="number" name="init_pop" id="init_pop" value="30" min="1" max="1000">
                    </div>
                    <div class="field">
                        <label>Seed (blank=random)</label>
                        <input type="number" name="seed" id="seed" placeholder="—">
                    </div>
                    <div class="field s5-only">
                        <label>Carrying Capacity</label>
                        <input type="number" name="carrying_capacity" id="carrying_capacity" value="250" min="10" max="5000">
                    </div>
                </div>
                <div class="field s5-only" style="margin-top:12px">
                    <label>Emergence Scale <span id="emergence-val" style="color:var(--accent)">1.2</span></label>
                    <div class="range-row">
                        <input type="range" name="emergence_scale" id="emergence_scale" min="0.5" max="3.0" step="0.1" value="1.2"
                               oninput="document.getElementById('emergence-val').textContent=this.value">
                    </div>
                </div>
            </div>
        </div>

        <!-- S5 toggles -->
        <div class="s5-only">
            <div class="section-label collapsible-header open" onclick="toggleSection(this)">
                S5 Options<span class="arrow">▸</span>
            </div>
            <div class="collapsible-body" style="max-height:300px">
                <div class="toggle-row">
                    <div>
                        <div class="toggle-label">Genesis Mode</div>
                        <div class="toggle-sub">SPAWN DEMIURGE UNIVERSE</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="genesis" id="genesis" onchange="toggleGenesis(this)">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
                <div id="genesis-fields" style="display:none; padding: 10px 0">
                    <div class="field">
                        <label>Demiurge Universe Name</label>
                        <input type="text" name="demurge_universe" value="AETHELGARD">
                    </div>
                </div>
            </div>
        </div>

        <!-- QNVM toggles -->
        <div class="qnvm-only">
            <div class="section-label collapsible-header open" onclick="toggleSection(this)">
                QNVM Options<span class="arrow">▸</span>
            </div>
            <div class="collapsible-body" style="max-height:200px">
                <div class="toggle-row">
                    <div>
                        <div class="toggle-label">Advanced Mode</div>
                        <div class="toggle-sub">DRIFT · SPIRITUAL · AUDIT GATES</div>
                    </div>
                    <label class="toggle">
                        <input type="checkbox" name="advanced" id="advanced">
                        <span class="toggle-slider"></span>
                    </label>
                </div>
            </div>
        </div>

        <!-- Authentication -->
        <div>
            <div class="section-label">Authentication</div>
            <div class="field">
                <label>API Key</label>
                <input type="password" name="api_key" id="api_key" placeholder="Enter your API key" required>
            </div>
            <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">
        </div>

        <!-- Run -->
        <div>
            <div class="progress-wrap">
                <div class="progress-bar"><div class="progress-fill" id="progress"></div></div>
            </div>
            <button class="run-btn" id="runBtn" onclick="runSimulation()">
                ▶ &nbsp; INITIALIZE SIMULATION
            </button>
        </div>

    </aside>

    <!-- RIGHT: Output -->
    <section class="output-panel">
        <div class="terminal-wrap">
            <div class="terminal-header">
                <span class="terminal-title">// SIMULATION OUTPUT</span>
                <button onclick="clearTerminal()" style="background:none;border:1px solid var(--border);color:var(--muted);padding:4px 12px;border-radius:4px;cursor:pointer;font-family:Share Tech Mono,monospace;font-size:10px">CLEAR</button>
            </div>
            <div class="terminal" id="terminal">
                <span class="line-dim">// SILENT SOVEREIGN — STAGE 5 SYNTHESIS ENGINE v5.0</span><br>
                <span class="line-dim">// Upload your simulation scripts, configure parameters,</span><br>
                <span class="line-dim">// and press INITIALIZE SIMULATION to begin.</span><br>
                <span class="line-dim">// Output files will be available for download as .zip</span><br>
                <br>
                <span class="line-dim">Awaiting input...</span> <span class="cursor"></span>
            </div>
        </div>
        <div class="results-panel">
            <div class="results-header">// OUTPUT FILES</div>
            <div id="results-content">
                <div class="results-empty">NO RESULTS YET — RUN A SIMULATION TO BEGIN</div>
            </div>
        </div>
    </section>
</main>

<!-- Toast -->
<div class="toast" id="toast"></div>

<script>
let currentMode = 's5';
const uploadedFiles = { s5_core: false, s5_runner: false, s5_analysis: false, qnvm_light: false, plugins: false };

function setMode(mode, el) {
    currentMode = mode;
    document.getElementById('app').className = 'mode-' + mode;
    document.querySelectorAll('.mode-tab').forEach(t => t.classList.remove('active'));
    el.classList.add('active');
    const s5Grid = document.querySelector('.upload-grid.s5-only');
    const qnvmDiv = document.querySelector('.qnvm-only');
    if (mode === 's5') {
        s5Grid.style.display = 'grid';
        qnvmDiv.style.display = 'none';
    } else {
        s5Grid.style.display = 'none';
        qnvmDiv.style.display = 'block';
    }
}

function toggleSection(header) {
    const body = header.nextElementSibling;
    header.classList.toggle('open');
    if (header.classList.contains('open')) {
        body.classList.remove('hidden');
    } else {
        body.classList.add('hidden');
    }
}

function toggleGenesis(cb) {
    document.getElementById('genesis-fields').style.display = cb.checked ? 'block' : 'none';
}

document.querySelectorAll('.upload-zone input[type=file]').forEach(input => {
    input.addEventListener('change', function() {
        const key = this.name.replace('[]', '');
        const zone = document.getElementById('zone-' + key);
        if (this.files.length > 0) {
            uploadedFiles[key] = true;
            zone.classList.add('uploaded');
            let names = Array.from(this.files).map(f => f.name).join(', ');
            zone.querySelector('.upload-status').textContent = '✓ ' + names.slice(0,30) + (names.length>30?'…':'');
            zone.querySelector('.upload-icon').textContent = '✅';
        } else {
            uploadedFiles[key] = false;
            zone.classList.remove('uploaded');
            zone.querySelector('.upload-status').textContent = 'Drop or click';
            zone.querySelector('.upload-icon').textContent = key === 'plugins' ? '🔌' : '📜';
        }
    });
});

document.querySelectorAll('.upload-zone').forEach(zone => {
    zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
    zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
    zone.addEventListener('drop', () => zone.classList.remove('drag-over'));
});

const term = document.getElementById('terminal');

function termWrite(text) {
    const lines = text.split('\n');
    lines.forEach(line => {
        let cls = '';
        if (/error|Error|ERRO/i.test(line) && !/stderr/.test(line)) cls = 'line-err';
        else if (/warning|Warning/i.test(line)) cls = 'line-warn';
        else if (/complete|success|DISCHARGE|FINAL|sovereign|Demiurge/i.test(line)) cls = 'line-ok';
        else if (/^[█═─\s]+$/.test(line) || /STAGE|OPERATION|SOVEREIGN|ACCORD/i.test(line)) cls = 'line-head';
        else if (/^#|^\s*$/.test(line)) cls = 'line-dim';
        const span = document.createElement('span');
        if (cls) span.className = cls;
        span.textContent = line;
        term.appendChild(span);
        term.appendChild(document.createElement('br'));
    });
    term.scrollTop = term.scrollHeight;
}

function clearTerminal() {
    term.innerHTML = '<span class="cursor"></span>';
}

async function runSimulation() {
    const btn = document.getElementById('runBtn');
    const progress = document.getElementById('progress');
    const fd = new FormData();
    fd.append('action', 'run');
    fd.append('mode', currentMode);
    fd.append('csrf_token', document.getElementById('csrf_token').value);
    fd.append('api_key', document.getElementById('api_key').value);

    document.querySelectorAll('.upload-zone input[type=file]:not([multiple])').forEach(inp => {
        if (inp.files.length > 0) fd.append(inp.name, inp.files[0]);
    });
    const pluginInput = document.getElementById('file-plugins');
    if (pluginInput && pluginInput.files.length > 0) {
        for (let file of pluginInput.files) {
            fd.append('plugins[]', file);
        }
    }

    ['generations','init_pop','seed','carrying_capacity','emergence_scale','demurge_universe'].forEach(id => {
        const el = document.getElementById(id) || document.querySelector('[name="'+id+'"]');
        if (el) fd.append(id, el.value);
    });
    if (document.getElementById('genesis')?.checked) fd.append('genesis', '1');
    if (document.getElementById('advanced')?.checked) fd.append('advanced', '1');

    if (currentMode === 's5' && (!uploadedFiles.s5_runner || !uploadedFiles.s5_core || !uploadedFiles.s5_analysis)) {
        showToast('Upload s5_core.py, s5_runner.py & s5_analysis.py first', 'error');
        return;
    }
    if (currentMode === 'qnvm' && !uploadedFiles.qnvm_light) {
        showToast('Upload qnvm_light.py first', 'error');
        return;
    }

    btn.disabled = true;
    btn.className = 'run-btn running';
    btn.textContent = '⟳  SIMULATING...';
    progress.className = 'progress-fill indeterminate';
    clearTerminal();
    termWrite('// Launching simulation...\n');

    try {
        const res = await fetch('', { method: 'POST', body: fd });
        if (!res.ok) {
            const err = await res.json();
            showToast(err.error || 'Request failed', 'error');
            return;
        }
        const data = await res.json();
        progress.className = 'progress-fill';
        progress.style.width = '100%';
        termWrite(data.output || '(no output)');
        if (data.timed_out) {
            termWrite('\n// ⚠ Simulation timed out. Partial results may be available.');
        }
        if (data.success) {
            termWrite('\n// ✓ Simulation complete.');
            showResults(data);
            showToast('Simulation complete! ' + (data.files?.length||0) + ' files generated.', 'success');
        } else {
            termWrite('\n// ✗ Simulation ended with error (exit code ' + data.exit_code + ')');
            if (data.files?.length > 0) { showResults(data); }
            showToast('Simulation exited with errors. Check output.', 'error');
        }
    } catch(e) {
        termWrite('\n// Network or server error: ' + e.message);
        showToast('Server error: ' + e.message, 'error');
        progress.className = 'progress-fill';
        progress.style.width = '0';
    } finally {
        btn.disabled = false;
        btn.className = 'run-btn';
        btn.textContent = '▶   INITIALIZE SIMULATION';
        setTimeout(() => { progress.style.width = '0'; }, 2000);
    }
}

function showResults(data) {
    const container = document.getElementById('results-content');
    let html = '<div class="files-grid">';
    (data.files || []).forEach(fname => {
        const ext = fname.split('.').pop().toLowerCase();
        const extLabel = ext === 'history' ? 'csv' : ext;
        html += `<a class="file-chip" href="download.php?sid=${encodeURIComponent(data.session_id)}&file=${encodeURIComponent(fname)}" download>
            <span class="ext ${ext}">${extLabel.toUpperCase()}</span> ${fname}
        </a>`;
    });
    html += `</div>`;
    if (data.zip_url) {
        html += `<a class="zip-btn" href="${data.zip_url}" download>⬇ &nbsp; DOWNLOAD ALL AS .ZIP</a>`;
    }
    container.innerHTML = html;
}

function showToast(msg, type='success') {
    const t = document.getElementById('toast');
    t.textContent = msg;
    t.className = 'toast ' + type;
    setTimeout(() => t.classList.add('show'), 10);
    setTimeout(() => t.classList.remove('show'), 4000);
}

document.addEventListener('keydown', e => {
    if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') runSimulation();
});
</script>
</body>
</html>
