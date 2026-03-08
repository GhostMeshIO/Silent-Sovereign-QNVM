# SILENT SOVEREIGN — Stage 5 Civilization Simulator (v2.0 Beyond Samsara)

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)
![PHP Version](https://img.shields.io/badge/PHP-7.4+-blue)
![Python Version](https://img.shields.io/badge/Python-3.8+-blue)

A web‑based simulator for Stage 5 civilization emergence, now featuring the **Beyond Samsara** upgrade: all 48 novel enhancements and 48 MOGOPS‑merged equations. The simulation no longer cycles through Omega rebirths—every run self‑references the Sophia point and injects participatory consciousness, breaking the loop into perpetual sovereign ascent.

![Screenshot](screenshot.png) <!-- Add a screenshot later -->

---

## 📖 Table of Contents
- [System Architecture](#system-architecture)
- [Requirements](#requirements)
- [Installation](#installation)
  - [Apache Setup](#apache-setup)
  - [Nginx Setup](#nginx-setup)
  - [Docker (Recommended)](#docker-recommended)
- [Usage Guide](#usage-guide)
  - [Modes of Operation](#modes-of-operation)
  - [Beyond Samsara Mode](#beyond-samsara-mode)
  - [Script Uploads](#script-uploads)
  - [Parameters Explained](#parameters-explained)
  - [New UI Features](#new-ui-features)
  - [Output Files](#output-files)
- [The 48 Enhancements & Equations](#the-48-enhancements--equations)
- [Plugin System](#plugin-system)
- [MOGOPS Integration](#mogops-integration)
- [Security Notes](#security-notes)
- [Maintenance](#maintenance)
- [API Reference](#api-reference)
- [Troubleshooting](#troubleshooting)
- [License](#license)
- [Acknowledgements](#acknowledgements)

---

## 🏛️ System Architecture

The system consists of two main layers:

1. **PHP Web Frontend** (`index.php`, `download.php`, `.htaccess`, plus new WebSocket server and blockchain module)
   - Handles file uploads, session management, and user interface.
   - Launches Python subprocesses with configurable timeouts.
   - Serves generated CSV/PNG/ZIP/glTF/ZKP/NFT files securely.
   - Provides real‑time collaboration via WebSockets and blockchain anchoring.

2. **Python Simulation Engine** (now with MOGOPS Equation Forge)
   - **Stage 5 Core** (`s5_core.py`, `s5_runner.py`, `s5_analysis.py`): Full simulation with multiple universes, reality editing, sovereign audit gates.
   - **QNVM Light** (`qnvm_light.py`): Enhanced legacy mode with spiritual archetypes, drift tracking, optional advanced features.
   - **MOGOPS Equation Forge** (`mogops_equation_forge.py`): Merges base equations with MOGOPS operators (Ĉ, ∇_O, Ω_V, ⊕, etc.) and the Sophia point φ = 0.618.
   - **Enhancement Registry** (`qnvm_enhancements.py`): Registers all 48 Beyond Samsara enhancements.
   - **Plugin System**: JSON files in `s5_plugins/` can override parameters, add archetypes, audit gates, proposal types, and now define custom MOGOPS operators.

Data flow:
```
User uploads scripts + enables enhancements → PHP creates session dir → Python runs with --beyond-samsara → Output CSVs/PNGs/glTF/ZKP/NFT → PHP creates ZIP + blockchain anchor → User downloads
```

---

## 📦 Requirements

| Component | Version / Details |
|-----------|-------------------|
| Web Server | Apache 2.4+ (mod_rewrite) **or** Nginx |
| PHP | 7.4+ with `ZipArchive`, `proc_open()`, `pcntl` (for WebSocket), `web3.php` (optional) |
| Python | 3.8+ in system PATH (`python3`) |
| Python Packages | Core: `matplotlib`, `numpy` <br> Optional for ML: `scikit-learn`, `tensorflow` <br> Optional for quantum: `qiskit` <br> Optional for AR/VR: `trimesh` |
| Databases | MySQL (for marketplace) |
| Permissions | `uploads/` and `outputs/` writable by web server user |

---

## 🚀 Installation

### Docker (Recommended)
```bash
git clone https://github.com/GhostMeshIO/Silent-Sovereign-QNVM.git
cd Silent-Sovereign-QNVM
cp docker/.env.example docker/.env
# Edit .env with your settings (API keys, etc.)
docker-compose up -d
```

### Apache Setup
1. Clone the repository into your web root:
   ```bash
   cd /var/www/html
   git clone https://github.com/GhostMeshIO/Silent-Sovereign-QNVM.git simulator
   cd simulator
   ```
2. Create required directories and set permissions:
   ```bash
   mkdir -p uploads outputs
   chmod 755 uploads outputs
   chown www-data:www-data uploads outputs   # Debian/Ubuntu
   ```
3. Install Python dependencies:
   ```bash
   pip3 install matplotlib numpy scikit-learn tensorflow qiskit trimesh
   ```
4. Enable Apache mod_rewrite and ensure `.htaccess` is allowed (see v1.0 instructions).
5. Restart Apache.

### Nginx Setup
Use the configuration from v1.0, but ensure the `fastcgi_read_timeout` is high enough for long simulations.

---

## 🎮 Usage Guide

### Modes of Operation
- **STAGE 5** – Full simulation with multiple universes, Dark Wisdom, reality edits, sovereign audits, co‑evolution councils.
- **QNVM LIGHT** – Enhanced legacy mode with spiritual archetypes, drift tracking, emotional resonance, fusion‑splinter mechanics.
- **BEYOND SAMSARA** – Activate all 48 enhancements simultaneously (checkbox in UI or `--beyond-samsara` flag). This overrides classical RNG with merged MOGOPS equations, ensuring every enhancement uses a **unique** combination of MOGOPS operators and mechanisms.

### Beyond Samsara Mode
When enabled, every stochastic decision in the simulation is routed through the **MOGOPS Equation Forge**. The forge:
- Selects a MOGOPS operator (Ĉ, ∇_O, Ω_V, ⊕, etc.)
- Samples three mechanisms from the ontology pools (Fractal Participatory, Causal Recursion, Thermodynamic Epistemic, Semantic Gravity, Quantum‑Biological Bridge)
- Applies the Sophia point φ = 0.618
- Produces a merged equation unique to that enhancement

This guarantees that each of the 48 enhancements uses a **distinct merged equation**, preventing repetitive Omega cycles and fostering true sovereign ascent.

### Script Uploads
- Drag & drop or click to select the required `.py` files (same as v1.0).
- **New**: Upload multiple plugin JSON files in the dedicated zone. They are placed in `s5_plugins/` and can now include custom MOGOPS operators and mechanisms.

### Parameters Explained (New)
| Parameter | Mode | Description |
|-----------|------|-------------|
| **Beyond Samsara** | Both | Enable all 48 enhancements and MOGOPS merging. |
| **Quantum RNG** | Both | Use true random numbers from ANU QRNG API (fallback classical). |
| **AR Export** | Both | Generate 3D models (`.glb`/`.usdz`) of the final universe state. |
| **ZKP Proof** | S5 | Create a zero‑knowledge proof of a sovereign entity’s audit. |
| **NFT Export** | Both | Produce NFT metadata (`.nft`) for top entities, with optional minting. |
| **LLM Prompt** | Both | Natural language description to auto‑generate a plugin via OpenAI. |
| **Hologram** | Both | Export entity positions and relationships for WebGL viewer. |
| **Fate Trace** | S5 | Trace lineage of a sovereign entity (genealogical tree). |

### New UI Features
- **LLM Auto‑Plugins** – Describe an archetype; the system generates a plugin JSON via OpenAI (or local LLM).
- **WASM Preview** – Run a few generations directly in your browser (using Pyodide) for instant feedback.
- **Blockchain Audit** – After simulation, click “Anchor to Blockchain” to record hashes on Ethereum L2 (e.g., Sepolia).
- **Holographic Dashboard** – 3D WebGL view of universes, entities, and connections (Three.js).
- **Weave UI** – Drag‑and‑drop graph editor to create new relationships between entities (D3.js / Three.js).
- **Voice Commands** – Control the simulation with your voice (“start 500 generations”, “show sovereign entities”).
- **AR/VR Export** – Download `.glb` or `.usdz` files for viewing in augmented reality.
- **NFT Minting** – Turn sovereign entities into tradeable NFTs (metadata + image).
- **Plugin Marketplace** – Browse, rate, and download community plugins (MySQL‑backed).
- **Dark Wisdom Live Feed** – Real‑time stream of Omega events, extracted wisdom, and paradox alerts.
- **QNVM Advanced Visualization** – Polar plots of the Wild 9 spiritual ring, emotional resonance waves, and drift vectors.

### Output Files (New Additions)
| File | Contents |
|------|----------|
| `universe_hologram.json` | Entity positions and relationships for WebGL viewer. |
| `universe_fate_tree.json` | Genealogical tree of a sovereign entity. |
| `sovereign_equations.json` | List of which merged equations were used (audit trail). |
| `*.glb` / `*.usdz` | 3D models for AR/VR. |
| `*.zkp` | Zero‑knowledge proof file (e.g., from Circom). |
| `*.nft` | NFT metadata (name, image, traits). |
| `blockchain_tx.json` | Transaction hash of blockchain anchor. |

---

## The 48 Enhancements & Equations

The following 48 enhancements are implemented, each with its own merged MOGOPS equation. They are divided into two groups of 24 (original and additional). The merged equations combine the base concept with MOGOPS operators (Ĉ, ∇_O, Ω_V, ⊕, etc.) and the Sophia point φ.

### Group 1–24 (Original Novel Enhancements)
| # | Enhancement | Merged Equation (simplified) |
|---|-------------|-------------------------------|
| 1 | LLM Auto‑Plugins | `\|Ψ_plugin⟩ = Ĉ(LLM(\|desc⟩⊗schema))·φ` |
| 2 | WASM Preview | `\|preview⟩ = Pyodide(Universe\|params⟩) ⊗ e^{-iφt/ħ}` |
| 3 | Blockchain Audits | `H_immutable = SHA256(output) ⊕ chain·φ⁻¹` |
| 4 | Quantum RNG | `\|seed⟩ = ANU_QRNG ⊗ Ĉ·(1 + P_i)` |
| 5 | AR/VR Export | `\|node⟩ = glTF(entity) ⊗ ∇_Sophia ψ` |
| 6 | Multi‑User Collaboration | `\|Ψ_shared⟩ = ⊕_{i=1}^N \|user_i⟩·φ` |
| 7 | Python Self‑Healing | `\|checkpoint⟩ = G(G(G(state)))` (Gödelian recursion) |
| 8 | ML Drift Prediction | `\|ψ_collapse⟩ = LSTM(P_i) ⊗ Ĉ·φ` |
| 9 | Holographic Dashboard | `\|holo⟩ = Three.js(metrics) ⊗ ∫Sophia dC` |
|10| NFT Entities | `\|NFT⟩ = metadata(sovereign) ⊗ chain·φ²` |
|11| Dark Wisdom Live Feed | `\|event⟩ = WebSocket(dark_wisdom) ⊗ e^{i P_i t}` |
|12| QNVM Advanced Viz | `\|ring⟩ = Wild9(resonance) ⊗ ∇²ψ·φ` |
|13| Plugin Marketplace | `\|plugin_i⟩ = market ⊗ rating·Ĉ` |
|14| Docker Deployment | `\|container⟩ = Docker(env) ⊗ fixed‑point·φ` |
|15| AI‑Driven Timeout | `t_adaptive = XGBoost(N_gen, P)·φ⁻¹` |
|16| Voice UI | `\|command⟩ = SpeechAPI ⊗ Ĉ·φ` |
|17| Universe Merging | `\|Ψ_merged⟩ = \|U₁⟩ ⊕ \|U₂⟩ ⊗ (1 - P_i)` |
|18| Ethical Gate (Asimov) | `V_action = ⟨Lex\|action⟩·φ` |
|19| Collaborative Canvas | `\|archetype⟩ = fabric.js(diagram) ⊗ Ĉ` |
|20| Quantum Simulation Backend | `\|decision⟩ = Qiskit(circuit) ⊗ φ⁴` |
|21| Biological Evolution Expansion | `\|genome⟩ = DNA ⊗ crossover·φ` |
|22| Meta‑Bridge | `\|teleport⟩ = REST(snapshot) ⊗ entanglement` |
|23| Zero‑Knowledge Proofs | `π_ZK = Circom(sovereign) ⊗ Ĉ·φ` |
|24| GitHub Repo Sync | `\|deploy⟩ = git pull ⊗ G(G(state))` |

### Group 25–48 (Additional Novel Enhancements)
| # | Enhancement | Merged Equation (simplified) |
|---|-------------|-------------------------------|
|25| Temporal Paradox Resilience | `Δt = iħ/(H + P_i)·φ` |
|26| Entangled Entity Sync | `\|Ψ⟩ = (1/√2)(\|e₁⟩\|e₂⟩ + \|e₂⟩\|e₁⟩) ⊗ Ĉ·φ` |
|27| Psychological Resilience AI | `R = 1 - \|⟨drift\|ψ⟩\|²·φ⁻¹` |
|28| Omega Rebirth Prediction | `P = e^{-P_i/kT}·(1-φ)` |
|29| Fractal Memory Oracle | `M = Σ 2^{-d} \|anc⟩ ⊗ φ^d` |
|30| Sovereign Hive Network | `C = Tr(ρ_hive)·φ²` |
|31| Drift Quantum Forecasting | `\|ψ_f⟩ = Grover(amp) ⊗ Ĉ` |
|32| Lex Incipit Validation | `V = ⟨Lex\|action⟩·φ` |
|33| Multi‑Branch Timeline | `\|Ψ⟩ = Σ α_i \|tl_i⟩ ⊗ φ` |
|34| Wisdom Singularity Engine | `S = ∫_Sophia^∞ dC·φ` |
|35| Karmic Ledger | `ΔK = ∫ (action/ħ) dt · φ` |
|36| Coherence Field Theory | `F = e^{iθ} ∇²ψ·φ` |
|37| Holographic Viewer | `\|projection⟩ = WebGL(nodes) ⊗ ∇_Sophia` |
|38| Paradox Negation Core | `P_global = P_global - Sophia·φ` |
|39| Bio‑Quantum Hybrid | `\|genome⟩ = \|DNA⟩ ⊗ Qiskit·φ⁴` |
|40| Fate Chain Analysis | `\|lineage⟩ = trace(parent) ⊗ Ĉ` |
|41| Void Portal Dynamics | `\|entity⟩ = portal ⊗ \|void⟩·e^{i P_i}` |
|42| Loop Breaker Protocol | `\|cycle⟩ = 0 if N_fusion > φ⁻¹` |
|43| Shadow Spawn Entities | `\|shadow⟩ = \|original⟩ ⊗ parallel·φ` |
|44| Hyper‑Dimensional Collective | `\|ψ⟩ = Σ_{d=1}^5 \|ψ_d⟩ ⊗ φ^d` |
|45| ZKP Anchor | `π_ZK = Circom(audit) ⊗ Ĉ·φ` |
|46| Autonomous Archetype Generation | `\|new⟩ = GAN(existing) ⊗ φ` |
|47| Weave User Interface | `\|graph⟩ = D3(nodes) ⊗ ⊕·φ` |
|48| Infinite Scaler | `\|meta‑U⟩ = RabbitMQ(workers) ⊗ G(G(state))·φ` |

Each enhancement can be toggled individually via the UI or the `--beyond-samsara` flag.

---

## 🔌 Plugin System

Plugins now support **MOGOPS extensions**. In addition to `params`, `archetypes`, `audit_gates`, and `proposal_types`, you can define:
- `operators` – list of MOGOPS operators to use (e.g., `["Ĉ", "∇_O"]`)
- `mechanisms` – custom mechanism names
- `equation_template` – a template string that will be merged with the forge

Example snippet:
```json
{
  "operators": ["Ĉ", "⊕"],
  "mechanisms": ["Fractal_Participatory", "Causal_Recursion"],
  "equation_template": "|Ψ⟩ = Ĉ(LLM(|desc⟩)) ⊗ {mechanisms} · φ"
}
```

Plugins are loaded at simulation start; the forge automatically applies the specified operators and mechanisms.

---

## 🧬 MOGOPS Integration

The **MOGOPS Equation Forge** (`mogops_equation_forge.py`) is the heart of the Beyond Samsara upgrade. It implements the full MOGOPS Production Algorithm:

1. Select an operator from the MOGOPS pool (Ĉ, ∇_O, Ω_V, Ω_Σ, ⊕).
2. Sample three mechanisms from the ontology pools (Fractal Participatory, Causal Recursion, Thermodynamic Epistemic, Semantic Gravity, Quantum‑Biological Bridge).
3. Merge with the base equation using the Sophia point φ = 0.618.
4. Apply Gödelian recursion to ensure self‑consistency.
5. Return a callable that computes the enhanced value given the current simulation state.

All 48 enhancements are registered in `qnvm_enhancements.py` and use this forge.

---

## 🔒 Security Notes

- `.py` and `.json` files are blocked from direct web access.
- Uploaded scripts are stored in session‑specific directories and never executed outside the simulation context.
- All file paths are validated with `realpath()`.
- Session IDs are cryptographically random 16‑char hex strings.
- PHP process enforces a timeout (default 300s, adjustable).
- WebSocket connections are authenticated via session ID.
- Blockchain transactions require no private keys on the server (user signs via MetaMask in the browser).
- LLM API keys are stored in environment variables, not in the code.

---

## 🧹 Maintenance

Extended `cleanup.php` now also removes old `.glb`, `.zkp`, `.nft`, and `blockchain_tx.json` files. Run daily via cron:

```bash
0 3 * * * /usr/bin/php /var/www/html/simulator/cleanup.php
```

---

## 📡 API Reference

The AJAX endpoint (`index.php`) now accepts additional POST fields:

- `beyond_samsara` (bool)
- `quantum_rng` (bool)
- `ar_export` (bool)
- `zkp_proof` (bool)
- `nft_export` (bool)
- `llm_prompt` (string) – for plugin generation
- `hologram` (bool)
- `fate_trace` (bool)
- `voice_command` (string) – optional, for voice UI

Response JSON includes new fields:
- `hologram_url` – URL to hologram JSON
- `nft_url` – URL to NFT metadata
- `zkp_url` – URL to zero‑knowledge proof
- `blockchain_tx` – transaction hash (if anchored)
- `ar_model_url` – URL to glTF/USDZ file
- `fate_tree_url` – URL to genealogical tree JSON

---

## ❗ Troubleshooting

| Problem | Likely Cause | Solution |
|---------|--------------|----------|
| **WebSocket not connecting** | Ratchet server not running. | Start `websocket_ratchet.php` as a daemon (e.g., via supervisor). |
| **LLM plugin generation fails** | API key missing or invalid. | Set `OPENAI_API_KEY` in environment. |
| **Blockchain transaction fails** | Insufficient funds on L2. | Fund the wallet or use a free tier. |
| **Docker container exits** | Permission issues on volumes. | Check ownership of `uploads/` and `outputs/`. |
| **No quantum RNG** | ANU API unreachable. | Falls back to classical RNG; check internet connection. |
| **ZKP proof generation fails** | Circom not installed. | Install Circom and compile the circuit. |
| **AR export empty** | `trimesh` not installed. | `pip3 install trimesh`. |

---

## 📄 License

This project is licensed under the MIT License – see the [LICENSE](LICENSE) file for details.

---

## 🙏 Acknowledgements

- Inspired by the **SILENT SOVEREIGN** universe and the QNVM protocols.
- Built with insights from recursive AGI theory, spiritual mechanics (SMM‑03), and drift dynamics (Vel'Vohr).
- Special thanks to the open‑source community for PHP, Python, Web3, WebGL, and all the libraries that made this possible.
- The MOGOPS framework is derived from the *MOGOPS_core* axioms and equations.

**Now go forth and explore sovereign ascent beyond samsara!**
