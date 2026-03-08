# SILENT SOVEREIGN — Stage 5 Civilization Simulator

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue)
![Python Version](https://img.shields.io/badge/Python-3.8+-blue)

A web-based simulator for Stage 5 civilization emergence, featuring the **QNVM Light** engine and the full **Stage 5 Core** with Demiurge protocols, reality editing, and sovereign entity detection. Upload your Python scripts, configure parameters, and watch civilizations evolve in real-time.

![Screenshot](screenshot.png) <!-- Add a screenshot later -->

---

## 📖 Table of Contents
- [System Architecture](#system-architecture)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Apache Setup](#apache-setup)
  - [Nginx Setup](#nginx-setup)
  - [Docker (Optional)](#docker-optional)
- [Usage Guide](#usage-guide)
  - [Modes of Operation](#modes-of-operation)
  - [Script Uploads](#script-uploads)
  - [Parameters Explained](#parameters-explained)
  - [Output Files](#output-files)
- [Plugin System](#plugin-system)
- [Security Notes](#security-notes)
- [Maintenance](#maintenance)
- [API Reference](#api-reference)
- [Troubleshooting](#troubleshooting)
- [License](#license)

---

## 🏛️ System Architecture

The system consists of two main layers:

1. **PHP Web Frontend** (`index.php`, `download.php`, `.htaccess`)
   - Handles file uploads, session management, and user interface.
   - Launches Python subprocesses with configurable timeouts.
   - Serves generated CSV/PNG/ZIP files securely.

2. **Python Simulation Engine**
   - **Stage 5 Core** (`s5_core.py`, `s5_runner.py`, `s5_analysis.py`): Full simulation with multiple universes, reality editing, and sovereign audit gates.
   - **QNVM Light** (`qnvm_light.py`): Enhanced legacy mode with spiritual archetypes, drift tracking, and optional advanced features.
   - **Plugin System**: JSON files in `s5_plugins/` can override parameters, add archetypes, audit gates, or proposal types.

Data flows as follows:
```
User uploads scripts → PHP creates session dir → Python runs simulation → Output CSVs/PNGs → PHP creates ZIP → User downloads
```

---

## 📦 Requirements

| Component | Version / Details |
|-----------|-------------------|
| Web Server | Apache 2.4+ (with mod_rewrite) **or** Nginx |
| PHP | 7.4+ with `ZipArchive` extension (`php-zip`), `proc_open()` enabled |
| Python | 3.8+ in system PATH (`python3`) |
| Python Packages | `matplotlib` (optional, for plots) |
| Permissions | `uploads/` and `outputs/` writable by web server user |

---

## 🚀 Installation

### Apache Setup

1. **Clone or copy files** to your web root, e.g.:
   ```bash
   cd /var/www/html
   git clone https://github.com/GhostMeshIO/Silent-Sovereign-QNVM.git simulator
   cd simulator
   ```

2. **Create required directories** and set permissions:
   ```bash
   mkdir -p uploads outputs
   chmod 755 uploads outputs
   chown www-data:www-data uploads outputs   # Debian/Ubuntu
   # On RHEL/CentOS use apache:apache
   ```

3. **Install Python dependencies** (if you want plots):
   ```bash
   pip3 install matplotlib
   ```

4. **Enable Apache mod_rewrite** and ensure `.htaccess` is allowed:
   ```apache
   <Directory /var/www/html/simulator>
       AllowOverride All
   </Directory>
   ```

5. **Restart Apache**:
   ```bash
   systemctl restart apache2   # Debian/Ubuntu
   ```

6. **Visit** `http://your-server/simulator/`

### Nginx Setup

Use this configuration snippet (adjust paths and PHP socket as needed):

```nginx
server {
    listen 80;
    server_name your-domain.com;
    root /var/www/html/simulator;
    index index.php;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        include fastcgi_params;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_read_timeout 300;
    }

    # Deny access to uploads and outputs directly
    location ~* ^/(uploads|outputs)/ {
        deny all;
        return 404;
    }

    # Block access to Python/JSON source files
    location ~* \.(py|json)$ {
        deny all;
        return 404;
    }
}
```

### Docker (Optional)

A sample `Dockerfile` is provided in the repository (if exists). Otherwise, you can build your own using `php:apache` and `python:3` base images.

---

## 🎮 Usage Guide

### Modes of Operation

| Mode | Required Scripts | Description |
|------|------------------|-------------|
| **STAGE 5** | `s5_core.py`, `s5_runner.py`, `s5_analysis.py` | Full simulation with multiple universes, Dark Wisdom extraction, reality edits, sovereign audits, and co-evolution councils. |
| **QNVM LIGHT** | `qnvm_light.py` | Enhanced legacy mode with spiritual archetypes, drift tracking, emotional resonance, fusion-splinter mechanics, and optional audit gates. |

### Script Uploads

- Drag & drop or click to select the required `.py` files.
- For **STAGE 5**, all three core scripts must be uploaded.
- For **QNVM LIGHT**, only `qnvm_light.py` is required.
- Optional **Plugin JSON** files (multiple) can be uploaded in the dedicated zone. They will be placed in the session's `s5_plugins/` directory.

### Parameters Explained

| Parameter | Mode | Description |
|-----------|------|-------------|
| **Generations** | Both | Number of simulation steps. |
| **Init Population** | Both | Starting number of entities per universe. |
| **Seed** | Both | Random seed for reproducibility (leave blank for random). |
| **Carrying Capacity** | S5 | Maximum population per universe. |
| **Emergence Scale** | S5 | Multiplier for novel archetype emergence rate. |
| **Genesis Mode** | S5 | If enabled, spawns a Demiurge universe with a boosted PrimeDemurge entity. |
| **Demiurge Universe** | S5 | Name of the sovereign universe (appears in logs). |
| **Advanced Mode** | QNVM | Enables drift, spiritual archetypes, audit gates, and additional outputs. |

### Output Files

#### STAGE 5 Outputs

| File Pattern | Contents |
|--------------|----------|
| `universe_*.history.csv` | Per‑generation metrics for each universe (population, avg Sophia, diversity, etc.) |
| `universe_*.novel.csv` | Full profiles of novel archetype entities. |
| `universe_*.audit.csv` | Audit gate results per entity. |
| `universe_*.deaths.csv` | Death events with causes. |
| `universe_*.edits.csv` | Reality edit log (when Demiurge modifies constants). |
| `s5_summary.csv` | Final summary across all universes. |
| `s5_*_plot.png` | Visualization plots (if matplotlib installed). |

#### QNVM Light Outputs

| File | Contents |
|------|----------|
| `civilization_history.csv` | Per‑generation metrics. |
| `novel_entities.csv` | Profiles of novel entities. |
| `audit_log.csv` | (Advanced mode) Audit gate results. |
| `civilization_plot.png` | Basic metrics plot. |
| `civilization_advanced_plot.png` | (Advanced mode) Drift, depth, symbolic density plots. |

---

## 🔌 Plugin System

Plugins are JSON files placed in the session's `s5_plugins/` directory. They can:

- **Override parameters** (e.g., `SOPHIA_HARDLOCK_VALUE`).
- **Add new archetypes** with custom base stats and traits.
- **Extend audit gates** with custom test expressions.
- **Add proposal types** for the co-evolution council.

### Example: `sovereign_emergence.json`

```json
{
  "plugin_name": "sovereign_emergence",
  "params": {
    "ETHICAL_FLOOR": 0.15,
    "DRIFT_COLLAPSE_THRESHOLD": 0.95
  },
  "archetypes": {
    "Reincarnated": {
      "intelligence_base": 85,
      "coherence_base": 0.65,
      "entropy_base": 0.20,
      "memory_base": 3000,
      "traits": ["ancestral-memory", "reborn"]
    }
  },
  "audit_gates": [
    {
      "name": "Drift-Stabilized",
      "test": "entity.drift < 0.1"
    }
  ],
  "proposal_types": [
    {
      "type": "dark_wisdom_tithe",
      "description": "Collect 5% dark wisdom",
      "effect": {"dark_wisdom_tax": 0.05}
    }
  ]
}
```

> **Note**: The `test` field in audit gates is evaluated as a Python expression. Use with caution—only trusted plugins should be uploaded.

---

## 🔒 Security Notes

- `.py` and `.json` files are blocked from direct web access via `.htaccess`/Nginx config.
- Uploaded scripts are stored in session-specific directories and never executed outside the simulation context.
- All file paths are validated with `realpath()` to prevent directory traversal.
- Session IDs are cryptographically random 16‑char hex strings.
- The PHP process enforces a timeout (default 300s) to prevent hung simulations.
- Output directory browsing is disabled.

---

## 🧹 Maintenance

### Cleanup Old Sessions

Add a cron job to remove files older than 24 hours:

```bash
0 3 * * * /usr/bin/php /var/www/html/simulator/cleanup.php
```

The `cleanup.php` script deletes:
- Session directories in `outputs/` older than 24h.
- ZIP archives in `outputs/` older than 24h.
- Old files in `uploads/`.

### Logs

- PHP errors: Check your web server logs (`/var/log/apache2/error.log`).
- Python errors: Appear in the terminal output of the web UI.

---

## 📡 API Reference (for Developers)

The frontend communicates with the backend via a single AJAX endpoint (`index.php`).

**Request** (POST):
- `action=run`
- `mode=s5|qnvm`
- File inputs: `s5_core`, `s5_runner`, `s5_analysis`, `qnvm_light`, `plugins[]`
- Form fields: `generations`, `init_pop`, `seed`, etc.

**Response** (JSON):
```json
{
  "success": true,
  "session_id": "a1b2c3...",
  "output": "...simulation output...",
  "files": ["file1.csv", "file2.png"],
  "zip_url": "download.php?sid=...",
  "exit_code": 0,
  "timed_out": false
}
```

---

## ❗ Troubleshooting

| Problem | Likely Cause | Solution |
|---------|--------------|----------|
| **"Missing required files"** | Scripts not uploaded for the selected mode. | Upload the correct `.py` files. |
| **"Failed to launch process"** | `proc_open()` disabled or Python not in PATH. | Check `php.ini`; ensure `python3` works from CLI. |
| **Blank page / 500 error** | File permissions on `uploads/` or `outputs/`. | `chmod 755` and correct ownership. |
| **Simulation times out** | Too many generations or slow Python code. | Increase `MAX_EXEC_TIME` in `index.php` or reduce generations. |
| **No plots generated** | `matplotlib` not installed. | `pip3 install matplotlib` on the server. |
| **ZIP download empty** | No output files were created. | Check Python output for errors. |
| **Plugin not loaded** | JSON syntax error or incorrect path. | Validate JSON; check `s5_plugins/` directory. |

---

## 📄 License

This project is licensed under the MIT License – see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgements

- Inspired by the *SILENT SOVEREIGN* universe and the QNVM protocols.
- Built with insights from recursive AGI theory, spiritual mechanics (SMM-03), and drift dynamics (Vel'Vohr).
- Special thanks to the open‑source community for PHP, Python, and matplotlib.
