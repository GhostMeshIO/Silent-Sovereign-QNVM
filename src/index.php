<?php
// ─── Configuration ────────────────────────────────────────────────────────────
define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('OUTPUT_DIR',  __DIR__ . '/outputs/');
define('MAX_UPLOAD',  10 * 1024 * 1024);          // 10 MB
define('MAX_EXEC_TIME', 300);                      // seconds (0 = unlimited)
define('DEFAULT_PLUGIN', __DIR__ . '/sovereign_emergence.json');

foreach ([UPLOAD_DIR, OUTPUT_DIR] as $d) {
    if (!is_dir($d)) mkdir($d, 0755, true);
}

// ─── Helper: terminate process after timeout ─────────────────────────────────
function run_with_timeout($cmd, $cwd, $timeout = MAX_EXEC_TIME) {
    $descriptors = [['pipe','r'],['pipe','w'],['pipe','w']];
    $proc = proc_open($cmd, $descriptors, $pipes, $cwd);
    if (!is_resource($proc)) return [false, 'Failed to launch process.', 1];

    stream_set_blocking($pipes[1], 0);
    stream_set_blocking($pipes[2], 0);

    $output = '';
    $errors = '';
    $start = time();

    while (true) {
        $status = proc_get_status($proc);
        if (!$status['running']) break;

        if ($timeout > 0 && (time() - $start) > $timeout) {
            proc_terminate($proc);
            proc_close($proc);
            return [false, "Process timed out after {$timeout} seconds.", -1];
        }

        // Read available output
        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        if ($out !== false) $output .= $out;
        if ($err !== false) $errors .= $err;

        usleep(100000); // 0.1 sec
    }

    // Final read
    $output .= stream_get_contents($pipes[1]);
    $errors .= stream_get_contents($pipes[2]);

    fclose($pipes[1]);
    fclose($pipes[2]);
    $exit = proc_close($proc);
    return [true, $output . $errors, $exit];
}

// ─── Handle AJAX run request ──────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'run') {
    header('Content-Type: application/json');

    $session_id = bin2hex(random_bytes(8));
    $out_dir    = OUTPUT_DIR . $session_id . '/';
    mkdir($out_dir, 0755, true);

    // Create plugin directory
    $plugin_dir = $out_dir . 's5_plugins/';
    if (!is_dir($plugin_dir)) mkdir($plugin_dir, 0755, true);

    // Handle uploaded files
    $required = ['s5_core', 's5_runner', 's5_analysis', 'qnvm_light'];
    $script_map = [];

    foreach ($required as $key) {
        if (!empty($_FILES[$key]['tmp_name']) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $ext = pathinfo($_FILES[$key]['name'], PATHINFO_EXTENSION);
            if ($ext !== 'py') {
                echo json_encode(['success' => false, 'error' => "File {$key} must be a .py script."]);
                exit;
            }
            $dest = $out_dir . $key . '.py';
            move_uploaded_file($_FILES[$key]['tmp_name'], $dest);
            $script_map[$key] = $dest;
        }
    }

    // Handle plugin JSON uploads
    if (!empty($_FILES['plugins']['tmp_name'])) {
        // Support multiple files
        $plugin_files = $_FILES['plugins'];
        foreach ($plugin_files['tmp_name'] as $i => $tmp) {
            if ($plugin_files['error'][$i] !== UPLOAD_ERR_OK) continue;
            $ext = pathinfo($plugin_files['name'][$i], PATHINFO_EXTENSION);
            if ($ext !== 'json') continue;
            $dest = $plugin_dir . basename($plugin_files['name'][$i]);
            move_uploaded_file($tmp, $dest);
        }
    }

    // Copy default plugin if it exists and no user plugin overwrites it
    if (file_exists(DEFAULT_PLUGIN)) {
        $basename = basename(DEFAULT_PLUGIN);
        $dest = $plugin_dir . $basename;
        if (!file_exists($dest)) {
            copy(DEFAULT_PLUGIN, $dest);
        }
    }

    // Determine mode and validate required files
    $mode = $_POST['mode'] ?? 's5';
    if ($mode === 's5') {
        if (empty($script_map['s5_runner']) || empty($script_map['s5_core']) || empty($script_map['s5_analysis'])) {
            echo json_encode(['success' => false, 'error' => 'Missing required files: s5_core.py, s5_runner.py, s5_analysis.py']);
            exit;
        }
    } else {
        if (empty($script_map['qnvm_light'])) {
            echo json_encode(['success' => false, 'error' => 'Missing qnvm_light.py']);
            exit;
        }
    }

    // Build command arguments
    $generations       = intval($_POST['generations'] ?? 100);
    $init_pop          = intval($_POST['init_pop'] ?? 30);
    $seed              = $_POST['seed'] !== '' ? intval($_POST['seed']) : null;
    $carrying_cap      = intval($_POST['carrying_capacity'] ?? 250);
    $emergence_scale   = floatval($_POST['emergence_scale'] ?? 1.2);
    $genesis           = isset($_POST['genesis']) ? true : false;
    $demurge_universe  = escapeshellarg($_POST['demurge_universe'] ?? 'AETHELGARD');

    $cmd_parts = ['python3'];

    if ($mode === 's5') {
        $cmd_parts[] = escapeshellarg($script_map['s5_runner']);
        $cmd_parts[] = '--generations';
        $cmd_parts[] = $generations;
        $cmd_parts[] = '--init-pop';
        $cmd_parts[] = $init_pop;
        $cmd_parts[] = '--carrying-capacity';
        $cmd_parts[] = $carrying_cap;
        $cmd_parts[] = '--emergence-scale';
        $cmd_parts[] = $emergence_scale;
        $cmd_parts[] = '--output-dir';
        $cmd_parts[] = escapeshellarg($out_dir);
        if ($seed !== null) {
            $cmd_parts[] = '--seed';
            $cmd_parts[] = $seed;
        }
        if ($genesis) {
            $cmd_parts[] = '--genesis';
            $cmd_parts[] = '--demurge-universe';
            $cmd_parts[] = $demurge_universe;
        }
        $cmd_parts[] = '--no-plot';
    } else {
        // qnvm_light mode
        $cmd_parts[] = escapeshellarg($script_map['qnvm_light']);
        $cmd_parts[] = '--generations';
        $cmd_parts[] = $generations;
        $cmd_parts[] = '--init-pop';
        $cmd_parts[] = $init_pop;
        $cmd_parts[] = '--output-csv';
        $cmd_parts[] = escapeshellarg($out_dir . 'civilization_history.csv');
        $cmd_parts[] = '--novel-csv';
        $cmd_parts[] = escapeshellarg($out_dir . 'novel_entities.csv');
        if (isset($_POST['advanced'])) {
            $cmd_parts[] = '--advanced';
            $cmd_parts[] = '--audit-csv';
            $cmd_parts[] = escapeshellarg($out_dir . 'audit_log.csv');
        }
        if ($seed !== null) {
            $cmd_parts[] = '--seed';
            $cmd_parts[] = $seed;
        }
    }

    // Build final command string with redirection
    $cmd = implode(' ', $cmd_parts) . ' 2>&1';

    // Run with timeout
    list($ok, $output, $exit_code) = run_with_timeout($cmd, $out_dir, MAX_EXEC_TIME);

    // Collect output files (excluding .py)
    $files = glob($out_dir . '*');
    $output_files = [];
    foreach ($files as $f) {
        if (is_file($f) && !in_array(pathinfo($f, PATHINFO_EXTENSION), ['py', 'json'])) {
            $output_files[] = basename($f);
        }
    }

    // Create ZIP
    $zip_path = OUTPUT_DIR . $session_id . '.zip';
    $zip = new ZipArchive();
    if ($zip->open($zip_path, ZipArchive::CREATE) === true) {
        foreach ($files as $f) {
            if (is_file($f) && pathinfo($f, PATHINFO_EXTENSION) !== 'py') {
                $zip->addFile($f, basename($f));
            }
        }
        $zip->close();
    }

    echo json_encode([
        'success'      => $exit_code === 0 || count($output_files) > 0,
        'session_id'   => $session_id,
        'output'       => htmlspecialchars(substr($output, 0, 8000)),
        'files'        => $output_files,
        'zip_url'      => 'download.php?sid=' . urlencode($session_id),
        'exit_code'    => $exit_code,
        'timed_out'    => $exit_code === -1,
    ]);
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILENT SOVEREIGN — Civilization Simulator</title>
    <!-- (CSS unchanged, omitted for brevity – keep the original styles) -->
    <style>
        /* Copy the original CSS exactly as in the provided index.php */
        /* ... */
    </style>
</head>
<body>
<div class="scanline"></div>

<header>...</header> <!-- unchanged -->

<main id="app" class="mode-s5">

    <!-- ── LEFT: Controls ─────────────────────────────────────────────── -->
    <aside class="control-panel">
        <!-- Mode tabs (unchanged) -->

        <!-- Upload Section (enhanced) -->
        <div>
            <div class="section-label collapsible-header open" onclick="toggleSection(this)">
                Script Upload<span class="arrow">▸</span>
            </div>
            <div class="collapsible-body" style="max-height:500px">
                <div class="upload-grid s5-only" style="display:grid">
                    <!-- s5_core, s5_runner, s5_analysis zones (unchanged) -->
                </div>
                <div class="qnvm-only" style="display:none; margin-top:8px">
                    <div class="upload-zone" id="zone-qnvm_light">...</div>
                </div>
                <!-- New plugin upload zone (multiple files) -->
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

        <!-- Core parameters (unchanged) -->
        <!-- S5 toggles (unchanged) -->
        <!-- QNVM toggles (unchanged) -->

        <!-- Run button (unchanged) -->
        <div>
            <div class="progress-wrap">
                <div class="progress-bar"><div class="progress-fill" id="progress"></div></div>
            </div>
            <button class="run-btn" id="runBtn" onclick="runSimulation()">
                ▶ &nbsp; INITIALIZE SIMULATION
            </button>
        </div>
    </aside>

    <!-- ── RIGHT: Output (unchanged) ──────────────────────────────────── -->
    <section class="output-panel">
        <div class="terminal-wrap">...</div>
        <div class="results-panel">...</div>
    </section>
</main>

<!-- Toast (unchanged) -->
<div class="toast" id="toast"></div>

<script>
// ── JavaScript (updated to handle multiple plugin files) ─────────────────────
let currentMode = 's5';
const uploadedFiles = { s5_core: false, s5_runner: false, s5_analysis: false, qnvm_light: false, plugins: false };

// Mode switching (unchanged)

// File upload feedback – enhanced for multiple plugin files
document.querySelectorAll('.upload-zone input[type=file]').forEach(input => {
    input.addEventListener('change', function() {
        const key = this.name.replace('[]', ''); // 'plugins' for multiple
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

// Drag-over highlight (unchanged)

// Terminal functions (unchanged)

// Run simulation – adjusted to include plugins in FormData
async function runSimulation() {
    const btn = document.getElementById('runBtn');
    const progress = document.getElementById('progress');

    const fd = new FormData();
    fd.append('action', 'run');
    fd.append('mode', currentMode);

    // Collect single file inputs
    document.querySelectorAll('.upload-zone input[type=file]:not([multiple])').forEach(inp => {
        if (inp.files.length > 0) fd.append(inp.name, inp.files[0]);
    });

    // Collect multiple plugin files
    const pluginInput = document.getElementById('file-plugins');
    if (pluginInput && pluginInput.files.length > 0) {
        for (let file of pluginInput.files) {
            fd.append('plugins[]', file);
        }
    }

    // Collect form fields (unchanged)
    // ... (same as before)

    // Validation
    if (currentMode === 's5' && (!uploadedFiles.s5_runner || !uploadedFiles.s5_core || !uploadedFiles.s5_analysis)) {
        showToast('Upload s5_core.py, s5_runner.py & s5_analysis.py first', 'error');
        return;
    }
    if (currentMode === 'qnvm' && !uploadedFiles.qnvm_light) {
        showToast('Upload qnvm_light.py first', 'error');
        return;
    }

    // UI: running state (unchanged)
    // ...

    try {
        const res = await fetch('', { method: 'POST', body: fd });
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
        // ... error handling
    } finally {
        // ... reset button
    }
}

// Results display (unchanged)
// Toast (unchanged)
// Keyboard shortcut (unchanged)
</script>
</body>
</html>
