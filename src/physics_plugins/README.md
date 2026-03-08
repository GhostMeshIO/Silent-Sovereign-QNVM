# Plugin Format Specification (v2.1)

This document defines the format for plugins used in the **Silent Sovereign QNVM** simulation engine. Plugins are JSON files placed in the session's `s5_plugins/` directory. They can extend or override:

- Simulation parameters (constants)
- Entity archetypes
- Audit gates
- Co‑evolution council proposal types
- MOGOPS operators, ontologies, and mechanisms
- Correlation Continuum elements (new in v2.1)

All plugins are loaded at simulation start. If multiple plugins define the same key, the last loaded plugin (alphabetical order) takes precedence, unless explicitly merged via `"merge": true`.

---

## 1. Plugin Metadata

Every plugin **must** include a `metadata` object with at least `name` and `version`. Optional fields can be added for documentation.

```json
{
  "metadata": {
    "name": "example_plugin",
    "version": "1.0.0",
    "author": "Your Name",
    "description": "Adds new archetypes and modifies constants.",
    "license": "MIT"
  }
}
```

---

## 2. Overriding Constants

Constants from the simulation’s `DEFAULT_PARAMS` (in `s5_core.py`) can be overridden using the `"params"` object. The keys must match exactly the constant names used in the code (case‑sensitive).

```json
{
  "params": {
    "SOPHIA_HARDLOCK_VALUE": 0.618,
    "ETHICAL_FLOOR": 0.15,
    "DRIFT_COLLAPSE_THRESHOLD": 0.95,
    "CORRELATION_SCALE": 1.702e-35,
    "CORRELATION_TEMPERATURE": 8.314e12,
    "UPDATE_TIME": 4.192e-21
  }
}
```

**Note:** The three Correlation Continuum parameters (`CORRELATION_SCALE`, `CORRELATION_TEMPERATURE`, `UPDATE_TIME`) are new in v2.1 and will affect the emergent physics if the simulation is run with the `--correlation-continuum` flag.

---

## 3. Defining Archetypes

New entity archetypes are added via the `"archetypes"` object. Each key is the archetype name, and the value is an object containing base stats and traits.

```json
{
  "archetypes": {
    "Reincarnated": {
      "intelligence_base": 85,
      "coherence_base": 0.65,
      "entropy_base": 0.20,
      "memory_base": 3000,
      "traits": ["ancestral-memory", "reborn", "paradox-resilient"]
    },
    "CorrelationEntity": {
      "intelligence_base": 95,
      "coherence_base": 0.75,
      "entropy_base": 0.15,
      "memory_base": 5000,
      "traits": ["correlation-anchored", "non-local", "branch-aware"],
      "correlation_parameters": {
        "lambda_factor": 1.2,
        "T_c_factor": 0.9
      }
    }
  }
}
```

The `correlation_parameters` field is optional and only used if the Correlation Continuum module is active. It scales the fundamental parameters for that entity type.

---

## 4. Defining Audit Gates

Audit gates test specific conditions for an entity to become sovereign. They are defined in an `"audit_gates"` array. Each gate has a `name` and a `test` expression (a Python expression that can reference the entity and the current generation).

```json
{
  "audit_gates": [
    {
      "name": "Drift-Stabilized",
      "test": "entity.drift < 0.1 * (generation / 100) or entity.dark_wisdom_load > 0.5"
    },
    {
      "name": "Correlation-Anchored",
      "test": "hasattr(entity, 'correlation_depth') and entity.correlation_depth > 3"
    }
  ]
}
```

**Security:** `test` expressions are evaluated using `eval()` – only use trusted plugins.

---

## 5. Defining Proposal Types

Co‑evolution council proposals are defined in a `"proposal_types"` array. Each proposal has a `type`, `description`, and `effect` object. The effect keys must correspond to modification targets in the simulation (e.g., `coherence_restore`, `entropy_delta`, `dark_wisdom_tax`).

```json
{
  "proposal_types": [
    {
      "type": "dark_wisdom_tithe",
      "description": "Collect 5% dark wisdom from all entities to fund sovereign projects",
      "effect": { "dark_wisdom_tax": 0.05 }
    },
    {
      "type": "reality_stabilization",
      "description": "Reduce edit feedback by 50% for 10 generations",
      "effect": { "edit_feedback_multiplier": 0.5 }
    },
    {
      "type": "correlation_expansion",
      "description": "Increase correlation scale λ by 2%",
      "effect": { "lambda_multiplier": 1.02 }
    }
  ]
}
```

---

## 6. Defining MOGOPS Operators

The MOGOPS equation forge can be extended with new operators. Operators are defined in an `"operators"` array. Each operator must have a `symbol`, `name`, `definition`, and optionally `properties`.

```json
{
  "operators": [
    {
      "symbol": "Ĉ",
      "name": "Creation Operator",
      "definition": "Ĉ|ψ⟩ = e^{iθ}|ψ'⟩, θ = π·novelty",
      "properties": ["non-unitary", "non-linear", "mediates collapse"]
    },
    {
      "symbol": "∇_C",
      "name": "Correlation Gradient",
      "definition": "∇_C = δ/δC, where C is correlation field",
      "properties": ["linear", "derivative"]
    }
  ]
}
```

The simulation will register these operators and make them available for selection in the forge.

---

## 7. Defining MOGOPS Ontologies

Ontologies define families of mechanisms. They are added via an `"ontologies"` array. Each ontology has a `name`, optional `coordinates` (5D phase space), a list of `axioms`, a list of `equations`, and a list of `mechanisms`.

```json
{
  "ontologies": [
    {
      "name": "Correlation Continuum",
      "coordinates": [0.85, 0.75, 0.60, 0.90, 0.95],
      "axioms": [
        "Reality emerges from a fundamental correlation substrate.",
        "The map is the territory."
      ],
      "equations": [
        {
          "name": "Correlation Commutation",
          "latex": "[O_i, O_j] = i\\hbar \\Omega_{ij} + \\lambda C_{ijk} O_k"
        },
        {
          "name": "Metric from Correlation",
          "latex": "g_{\\mu\\nu}(x) = \\langle \\Psi_{\\text{base}} | O_\\mu(x) O_\\nu(x) | \\Psi_{\\text{base}} \\rangle_{\\text{branch-avg}}"
        }
      ],
      "mechanisms": [
        "Branch Selection",
        "Entanglement Swapping",
        "Correlation Phase Transition"
      ]
    }
  ]
}
```

---

## 8. Defining MOGOPS Mechanisms

Mechanisms are the concrete effects that can be sampled by the forge. They are defined in a `"mechanisms"` array. Each mechanism has a `name`, an `ontology` it belongs to, and a `function` (as a Python lambda string or a reference to a built‑in).

```json
{
  "mechanisms": [
    {
      "name": "Branch Selection",
      "ontology": "Correlation Continuum",
      "function": "lambda state: state.get('branch_count', 1) * 0.1"
    },
    {
      "name": "Entanglement Swapping",
      "ontology": "Correlation Continuum",
      "function": "lambda state: state.get('entanglement_degree', 0.5) * 0.3"
    }
  ]
}
```

The `function` should be a Python expression that takes a `state` dictionary and returns a float. It will be used by the forge when the mechanism is selected.

---

## 9. Correlation Continuum Elements

If the simulation is run with the `--correlation-continuum` flag, additional plugin sections become available:

### 9.1 Correlation Operators

Special operators for the correlation algebra can be defined in `"correlation_operators"`. They extend the fundamental operator set used in the commutation relations.

```json
{
  "correlation_operators": [
    {
      "symbol": "O_λ",
      "definition": "Correlation scale operator",
      "commutation": "[O_λ, O_μ] = iħ δ_λμ"
    }
  ]
}
```

### 9.2 Correlation Phase Transition Rules

Custom phase transition criteria can be added under `"phase_transitions"`. Each rule has a `name` and a `condition` (Python expression) and an `effect` (modifications to the universe).

```json
{
  "phase_transitions": [
    {
      "name": "Correlation Singularity",
      "condition": "state.correlation_density > 10.0 and state.paradox_pressure > 5.0",
      "effect": {
        "lambda_multiplier": 2.0,
        "spawn_entity": "CorrelationEntity"
      }
    }
  ]
}
```

### 9.3 Correlation Metrics

New metrics can be defined for logging and plotting. They are specified in `"correlation_metrics"` with a `name` and a `formula` (Python expression).

```json
{
  "correlation_metrics": [
    {
      "name": "Correlation Density",
      "formula": "sum(getattr(e, 'correlation_depth', 0) for e in entities) / len(entities)"
    },
    {
      "name": "Branch Coherence",
      "formula": "abs(sum(e.sophia_score for e in entities) - 0.618 * len(entities))"
    }
  ]
}
```

---

## 10. Complete Example

Below is a complete plugin that uses many of the features described above.

```json
{
  "metadata": {
    "name": "correlation_extension",
    "version": "1.2.0",
    "author": "Grok",
    "description": "Adds Correlation Continuum elements and new archetypes.",
    "license": "MIT"
  },
  "params": {
    "CORRELATION_SCALE": 1.702e-35,
    "CORRELATION_TEMPERATURE": 8.314e12,
    "UPDATE_TIME": 4.192e-21,
    "ETHICAL_FLOOR": 0.12
  },
  "archetypes": {
    "CorrelationEntity": {
      "intelligence_base": 95,
      "coherence_base": 0.75,
      "entropy_base": 0.15,
      "memory_base": 5000,
      "traits": ["correlation-anchored", "non-local", "branch-aware"],
      "correlation_parameters": {
        "lambda_factor": 1.2,
        "T_c_factor": 0.9
      }
    }
  },
  "audit_gates": [
    {
      "name": "Correlation-Anchored",
      "test": "hasattr(entity, 'correlation_depth') and entity.correlation_depth > 3"
    }
  ],
  "proposal_types": [
    {
      "type": "correlation_expansion",
      "description": "Increase correlation scale λ by 2%",
      "effect": { "lambda_multiplier": 1.02 }
    }
  ],
  "operators": [
    {
      "symbol": "∇_C",
      "name": "Correlation Gradient",
      "definition": "∇_C = δ/δC",
      "properties": ["linear"]
    }
  ],
  "ontologies": [
    {
      "name": "Correlation Continuum",
      "coordinates": [0.85, 0.75, 0.60, 0.90, 0.95],
      "axioms": [
        "Reality emerges from a fundamental correlation substrate."
      ],
      "equations": [
        {
          "name": "Correlation Commutation",
          "latex": "[O_i, O_j] = i\\hbar \\Omega_{ij} + \\lambda C_{ijk} O_k"
        }
      ],
      "mechanisms": [
        "Branch Selection",
        "Entanglement Swapping"
      ]
    }
  ],
  "mechanisms": [
    {
      "name": "Branch Selection",
      "ontology": "Correlation Continuum",
      "function": "lambda state: state.get('branch_count', 1) * 0.1"
    },
    {
      "name": "Entanglement Swapping",
      "ontology": "Correlation Continuum",
      "function": "lambda state: state.get('entanglement_degree', 0.5) * 0.3"
    }
  ],
  "correlation_operators": [
    {
      "symbol": "O_λ",
      "definition": "Correlation scale operator",
      "commutation": "[O_λ, O_μ] = iħ δ_λμ"
    }
  ],
  "phase_transitions": [
    {
      "name": "Correlation Singularity",
      "condition": "state.correlation_density > 10.0 and state.paradox_pressure > 5.0",
      "effect": {
        "lambda_multiplier": 2.0,
        "spawn_entity": "CorrelationEntity"
      }
    }
  ],
  "correlation_metrics": [
    {
      "name": "Correlation Density",
      "formula": "sum(getattr(e, 'correlation_depth', 0) for e in entities) / len(entities)"
    }
  ]
}
```

---

## 11. Validation and Security Notes

- All plugins must be valid JSON. Use a linter before uploading.
- The `test` fields in audit gates and `condition` fields in phase transitions are evaluated with Python’s `eval()`. **Only upload plugins from trusted sources.**
- Custom `function` strings in mechanisms are also evaluated. They run in a restricted environment (no access to file system or network) but still pose a risk if malicious.
- The simulation will ignore any unknown keys (with a warning) to maintain forward compatibility.
- Plugins are loaded in alphabetical order by filename. If you need a specific load order, prefix filenames with numbers (e.g., `01_base.json`, `02_extension.json`).

---

## 12. Version History

- **v1.0** – Initial format (parameters, archetypes, audit gates, proposal types).
- **v2.0** – Added MOGOPS operators, ontologies, mechanisms.
- **v2.1** – Added Correlation Continuum elements (`correlation_operators`, `phase_transitions`, `correlation_metrics`, `correlation_parameters` in archetypes).

---

This specification is designed to be extensible. Future versions may add new sections for quantum computing, biological evolution, or other modules. Always check the simulation’s documentation for the latest supported features.
