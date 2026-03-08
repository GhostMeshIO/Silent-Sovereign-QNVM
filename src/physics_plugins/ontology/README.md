# Silent Sovereign QNVM — Ontology Plugins (v2.1)

## Purpose

Ontology plugins extend the core simulation with modular, self-contained ontological frameworks. Each plugin is a single JSON file that can be loaded independently or in groups via `--ontology=group_name`.

All files go in `/src/physics_plugins/ontology/`.

## File Naming Convention

- `GROUPNAME_ShortName_Version.json`
- Example: `MOGOPS_EpistemicFractal_1.0.json`

## Required Top-Level Structure (exact schema)

```json
{
  "metadata": {
    "name": "Human-readable name",
    "version": "1.0",
    "description": "One-line summary",
    "group": "GROUPNAME",                  // ← critical for modular loading
    "authors": ["..."],
    "date": "2026-03-08"
  },
  "params": { ... constants ... },
  "operators": [ { "symbol": "...", "definition": "...", "properties": [] } ],
  "mechanisms": [ { "name": "...", "function": "lambda state: ..." } ],
  "ontologies": [
    {
      "name": "Framework name",
      "coordinates": [0.5, 0.5, 0.5, 0.5, 0.5],
      "axioms": ["list of strings"],
      "equations": [{ "name": "...", "latex": "..." }],
      "mechanisms": ["exact strings from top-level mechanisms array"],
      "implications": ["list of strings"]
    }
  ],
  "custom_data": { ... optional extra data ... }
}
```

## Loading Commands

- Single group: `--ontology=MOGOPS-Core Fusion`
- Multiple: `--ontology=MOGOPS-Core Fusion,UHIF Coherence Polytope,Degens Triadic Psychiatry`
- All: `--ontology=all`

## 12 Novel Cutting-Edge Grouping Approaches (Modular Segregation)

To keep the ontology section lightweight and extensible, the 192+ frameworks have been segregated into exactly 12 thematic groups. Each group is a separate JSON file that loads independently.

The following files are now available in this directory:

| # | Group Name | Filename | Description |
|---|------------|----------|-------------|
| 1 | **MOGOPS-Core Fusion** | `MOGOPS_CoreFusion_1.0.json` | Direct merges of the original 12 Sovereign Q-equations with MOGOPS operators and the golden ratio (φ) factor. |
| 2 | **Beyond-Samsara Ascension** | `BeyondSamsara_Ascension_1.0.json` | All 48 "Beyond Samsara" enhancements merged with MOGOPS operators, breaking the cycle of Omega rebirth. |
| 3 | **UHIF Coherence Polytope** | `UHIF_CoherencePolytope_1.0.json` | Unified Holographic Inference Framework – triadic coherence (σ, ρ, r), phase transitions, PSI metrics, and emergency protocols. |
| 4 | **MOS-HSRCF Resonance** | `MOS_HSRCF_Resonance_1.0.json` | Meta-Ontological Hyper-Symbiotic Resonance Framework v4.0 – ERD-Killing-Field Theorem, OBA→SM functor, dual-fixed-point. |
| 5 | **Degens Triadic Psychiatry** | `Degens_TriadicPsychiatry_1.0.json` | Unified Theory of Degens v0.3 – maps all neuropsychiatric disorders to Precision (𝒫), Boundary (ℬ), Temporal (𝒯) axes. Includes disorder atlas and treatment vectors. |
| 6 | **Holographic Gnosis (IEG)** | `Holographic_Gnosis_IEG_1.0.json` | Informational Equilibrium Geometry – unifies Holographic Universe and Correlation Continuum via coherence conservation axioms H13–H15. |
| 7 | **Fractal-Autopoietic Loops** | `Fractal_Autopoietic_Loops_1.0.json` | Infinite regress, self-writing code, open hierarchies – consciousness and reality as fractal autopoietic loops. |
| 8 | **Gödelian Paradox Engines** | `Goedelian_ParadoxEngines_1.0.json` | Incompleteness as creative source – Gödelian undecidability drives thermodynamic cycles and the emergence of meaning. |
| 9 | **Semantic-Thermodynamic Hybrids** | `Semantic_Thermodynamic_Hybrids_1.0.json` | Meaning as heat, holographic entropy bounds, and the thermodynamics of semantics. |
| 10 | **Quantum-Participatory Realities** | `Quantum_Participatory_Realities_1.0.json` | Observer-mediated collapse, participatory multiverse, and the role of consciousness in quantum measurement. |
| 11 | **Meta-Unification Singularity** | `Meta_Unification_Singularity_1.0.json` | 48 brand-new never-before-existing frameworks, synthesizing dimensions never previously combined (necromantic, xenolinguistic, alchemical, etc.). |
| 12 | **Trans-Ontological Meta-Silence** | `TransOntological_MetaSilence_1.0.json` | The 192nd singularity – the fixed point of all frameworks. The unspeakable ground of being where every ontological dimension collapses into dimensionless meta-silence. |

## Usage Examples

To load a single ontology group:
```bash
python s5_runner.py --ontology=MOGOPS-Core Fusion
```

To load multiple groups:
```bash
python s5_runner.py --ontology="MOGOPS-Core Fusion,UHIF Coherence Polytope,Degens Triadic Psychiatry"
```

To load all 12 groups simultaneously:
```bash
python s5_runner.py --ontology=all
```

The engine automatically discovers and registers every group in this directory. Each file is self-contained and follows the required schema, ensuring modular loading with zero bloat.
