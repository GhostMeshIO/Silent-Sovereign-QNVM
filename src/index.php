<?php
// ─── Configuration ────────────────────────────────────────────────────────────
require_once __DIR__ . '/config.php';

define('UPLOAD_DIR',  __DIR__ . '/uploads/');
define('OUTPUT_DIR',  __DIR__ . '/outputs/');
define('MAX_UPLOAD',  10 * 1024 * 1024);          // 10 MB
define('DEFAULT_PLUGIN', __DIR__ . '/sovereign_emergence.json');

foreach ([UPLOAD_DIR, OUTPUT_DIR] as $d) {
    if (!is_dir($d)) mkdir($d, DIR_PERMISSIONS, true);
}

// ─── Helper: CSRF token generation (same as before) ──────────────────────────
function generate_csrf_token() { /* ... */ }
function verify_csrf_token($token) { /* ... */ }

// ─── Helper: authenticate request via API key ────────────────────────────────
function authenticate() { /* ... */ }

// ─── Helper: run simulation inside secure Docker container ───────────────────
function run_in_docker($cmd, $cwd, $timeout = MAX_EXEC_TIME) {
    // Verify Docker is available
    exec('docker info 2>&1', $docker_out, $docker_ret);
    if ($docker_ret !== 0) {
        return [false, 'Docker is not available or not running. Please install Docker and ensure the web server user has permission.', 1];
    }

    // Build secure Docker command
    $docker_cmd = sprintf(
        'docker run --rm ' .
        '--network none ' .
        '--read-only ' .
        '--cap-drop=ALL ' .
        '--security-opt=no-new-privileges ' .
        '--memory="%s" ' .
        '--cpus="%s" ' .
        '--user="%s" ' .
        '-v "%s":/workspace:rw ' .
        '-w /workspace ' .
        '%s bash -c "%s"',
        DOCKER_MEMORY,
        DOCKER_CPUS,
        DOCKER_USER,
        escapeshellarg($cwd),
        DOCKER_IMAGE,
        escapeshellarg($cmd . ' 2>&1')
    );

    $descriptors = [['pipe','r'],['pipe','w'],['pipe','w']];
    $proc = proc_open($docker_cmd, $descriptors, $pipes, $cwd);
    if (!is_resource($proc)) return [false, 'Failed to launch Docker container.', 1];

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

        $out = stream_get_contents($pipes[1]);
        $err = stream_get_contents($pipes[2]);
        if ($out !== false) $output .= $out;
        if ($err !== false) $errors .= $err;

        usleep(100000);
    }

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

    // Authentication
    if (!authenticate()) {
        http_response_code(401);
        echo json_encode(['success' => false, 'error' => 'Unauthorized.']);
        exit;
    }

    // CSRF
    $token = $_POST['csrf_token'] ?? '';
    if (!verify_csrf_token($token)) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'Invalid CSRF token.']);
        exit;
    }

    $session_id = bin2hex(random_bytes(8));
    $out_dir    = OUTPUT_DIR . $session_id . '/';
    mkdir($out_dir, DIR_PERMISSIONS, true);

    // Create plugin directory
    $plugin_dir = $out_dir . 's5_plugins/';
    if (!is_dir($plugin_dir)) mkdir($plugin_dir, DIR_PERMISSIONS, true);

    // Handle uploaded files – whitelist names
    $required = ['s5_core', 's5_runner', 's5_analysis', 'qnvm_light'];
    $script_map = [];

    foreach ($required as $key) {
        if (!empty($_FILES[$key]['tmp_name']) && $_FILES[$key]['error'] === UPLOAD_ERR_OK) {
            $original_name = $_FILES[$key]['name'];
            if (!in_array($original_name, ALLOWED_SCRIPTS)) {
                echo json_encode(['success' => false, 'error' => "File {$key} name not allowed."]);
                exit;
            }
            $ext = pathinfo($original_name, PATHINFO_EXTENSION);
            if ($ext !== 'py') {
                echo json_encode(['success' => false, 'error' => "File {$key} must be a .py script."]);
                exit;
            }
            $dest = $out_dir . $key . '.py';
            move_uploaded_file($_FILES[$key]['tmp_name'], $dest);
            $script_map[$key] = $dest;
        }
    }

    // ... (plugin JSON upload, unchanged) ...

    // Determine mode and validate required files (unchanged) ...

    // Build command arguments (unchanged) ...

    // Run inside Docker (mandatory)
    list($ok, $output, $exit_code) = run_in_docker($cmd, $out_dir, MAX_EXEC_TIME);

    // ... (collect output files, create ZIP, etc.) ...

    // Success flag: only true if exit code is 0 AND there are output files
    $success = ($exit_code === 0 && count($output_files) > 0);

    echo json_encode([
        'success'      => $success,
        'session_id'   => $session_id,
        'output'       => htmlspecialchars(substr($output, 0, 8000), ENT_QUOTES, 'UTF-8'),
        'files'        => $output_files,
        'zip_url'      => 'download.php?sid=' . urlencode($session_id),
        'exit_code'    => $exit_code,
        'timed_out'    => $exit_code === -1,
    ]);
    exit;
}

// Generate CSRF token for the form
$csrf_token = generate_csrf_token();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SILENT SOVEREIGN — Civilization Simulator</title>
    <!-- (CSS would be here; for brevity, keep as before) -->
    <style>
        /* ... existing styles ... */
    </style>
</head>
<body>
<div class="scanline"></div>

<header>...</header>

<main id="app" class="mode-s5">
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
                <!-- Plugin JSON upload zone -->
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

        <!-- Hidden CSRF token -->
        <input type="hidden" name="csrf_token" id="csrf_token" value="<?= htmlspecialchars($csrf_token) ?>">

        <!-- API key input (or could be header; we'll use a field for simplicity) -->
        <div style="margin-bottom: 10px;">
            <label for="api_key">API Key</label>
            <input type="password" name="api_key" id="api_key" placeholder="Enter your API key" required>
        </div>

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

    <section class="output-panel">
        <div class="terminal-wrap">...</div>
        <div class="results-panel">...</div>
    </section>
</main>

<!-- Toast (unchanged) -->
<div class="toast" id="toast"></div>

<script>
let currentMode = 's5';
const uploadedFiles = { s5_core: false, s5_runner: false, s5_analysis: false, qnvm_light: false, plugins: false };

// Mode switching (unchanged)

// File upload feedback (unchanged)

// Run simulation – now includes API key and CSRF token
async function runSimulation() {
    const btn = document.getElementById('runBtn');
    const progress = document.getElementById('progress');

    const fd = new FormData();
    fd.append('action', 'run');
    fd.append('mode', currentMode);
    fd.append('csrf_token', document.getElementById('csrf_token').value);
    fd.append('api_key', document.getElementById('api_key').value);

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
    // ... (same as before, including generations, init_pop, etc.)

    // Validation (unchanged)

    try {
        const res = await fetch('', { method: 'POST', body: fd });
        const data = await res.json();

        if (!res.ok) {
            showToast(data.error || 'Request failed', 'error');
            return;
        }

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
    } finally {
        btn.disabled = false;
        btn.className = 'run-btn';
        btn.textContent = '▶   INITIALIZE SIMULATION';
        setTimeout(() => { progress.style.width = '0'; }, 2000);
    }
}

// Results display (unchanged)
// Toast (unchanged)
// Keyboard shortcut (unchanged)
</script>
</body>
</html>
