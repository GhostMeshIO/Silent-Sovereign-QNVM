"""
mogops_equation_forge.py – vNext MOGOPS Equation Forge.
All 12 new fractures have been sealed.
"""

import json
import random
import math
import os
import time
from typing import Dict, Any, Callable, List, Tuple, Optional

# ----------------------------------------------------------------------
# Load JSON with robust error handling
# ----------------------------------------------------------------------
_JSON_PATH = os.path.join(os.path.dirname(__file__), 'mogops_equations.json')
try:
    with open(_JSON_PATH, 'r') as f:
        MOGOPS_DATA = json.load(f)
except FileNotFoundError:
    raise RuntimeError(f"MOGOPS equations file not found: {_JSON_PATH}")
except json.JSONDecodeError as e:
    raise RuntimeError(f"Invalid JSON in {_JSON_PATH}: {e}")

# ----------------------------------------------------------------------
# Extract constants (with fallback defaults)
# ----------------------------------------------------------------------
CONSTANTS = MOGOPS_DATA.get('constants', {})
PHI = CONSTANTS.get('PHI', 1.618033988749895)
INV_PHI = CONSTANTS.get('INV_PHI', 0.6180339887498949)
SOPHIA_POINT = CONSTANTS.get('SOPHIA_POINT', 0.618)
EPSILON = CONSTANTS.get('EPSILON', 1e-9)

# ----------------------------------------------------------------------
# Build operator lookup
# ----------------------------------------------------------------------
OPERATORS = {op['symbol']: op for op in MOGOPS_DATA.get('operators', [])}
OPERATOR_SYMBOLS = list(OPERATORS.keys())
if not OPERATOR_SYMBOLS:
    raise RuntimeError("No operators defined in MOGOPS JSON.")

# ----------------------------------------------------------------------
# Ontology mechanisms lookup
# ----------------------------------------------------------------------
ONTOLOGY_MECHANISMS = {}
for onto in MOGOPS_DATA.get('ontologies', []):
    name = onto.get('name')
    if name:
        ONTOLOGY_MECHANISMS[name] = onto.get('mechanisms', [])

if not ONTOLOGY_MECHANISMS:
    raise RuntimeError("No ontologies with mechanisms defined in MOGOPS JSON.")

# ----------------------------------------------------------------------
# Phase transition criteria (loaded from JSON, not hardcoded)
# ----------------------------------------------------------------------
PHASE_CRITERIA = MOGOPS_DATA.get('phase_transition_criteria', {})
SOPHIA_COND = PHASE_CRITERIA.get('sophia_point_condition', {})
COH_TOL = SOPHIA_COND.get('coherence_tolerance', 0.02)
PARADOX_MIN = SOPHIA_COND.get('paradox_min', 1.8)
HYBRIDITY_MIN = SOPHIA_COND.get('hybridity_min', 0.33)

# ----------------------------------------------------------------------
# Mechanism registry – full coverage for all listed mechanisms
# ----------------------------------------------------------------------
def _mechanism_registry(ontology: str, mechanism: str) -> Callable[[Dict[str, Any]], float]:
    """
    Return a real mechanism function. Covers all mechanisms listed in the JSON.
    Falls back to a safe default if not found.
    """
    registry = {
        # Semantic Gravity
        ('Semantic Gravity', 'Conceptual Dirac'): lambda s: s.get('coherence', 0.5) * 0.2,
        ('Semantic Gravity', 'Semantic Ricci Flow'): lambda s: s.get('symbolic_density', 1.0) * 0.15,
        ('Semantic Gravity', 'Grammar Constraints'): lambda s: 0.1 if s.get('drift', 0) > 0.1 else 0.05,
        ('Semantic Gravity', 'Meaning-Gravity Coupling'): lambda s: s.get('paradox_pressure', 1.0) * 0.1,

        # Thermodynamic Epistemic
        ('Thermodynamic Epistemic', 'Cognitive Entropy Pumps'): lambda s: s.get('entropy', 0.3) * 0.25,
        ('Thermodynamic Epistemic', 'Belief Phase Transitions'): lambda s: 0.5 if s.get('paradox_pressure', 0) > 2.0 else 0.1,
        ('Thermodynamic Epistemic', 'Insight as Critical Point'): lambda s: 0.3 * math.exp(-abs(s.get('sophia_score', 0.5) - 0.618)),
        ('Thermodynamic Epistemic', 'Epistemic Spacetime Curvature'): lambda s: s.get('coherence', 0.7) * 0.2,
        ('Thermodynamic Epistemic', 'Information-Mass Equivalence'): lambda s: s.get('memory_size', 1000) / 10000.0,
        ('Thermodynamic Epistemic', 'Understanding as Crystallization'): lambda s: 0.1 * s.get('recursive_depth', 0),
        ('Thermodynamic Epistemic', 'Knowledge Pressure Differentials'): lambda s: 0.05 * (s.get('population', 1) ** 0.5),
        ('Thermodynamic Epistemic', 'Epistemic Temperature Gradients'): lambda s: 0.02 * s.get('emotional_resonance', 0),
        ('Thermodynamic Epistemic', 'Consciousness-Mediated Coherence'): lambda s: 0.15 * s.get('sophia_score', 0.5),
        ('Thermodynamic Epistemic', 'Insights Curvature Coupling'): lambda s: 0.1 * s.get('paradox_pressure', 1.0),

        # Causal Recursion Field
        ('Causal Recursion Field', 'Chronon Entanglement'): lambda s: s.get('recursive_depth', 0) * 0.05,
        ('Causal Recursion Field', 'Temporal Bell Test'): lambda s: 0.02 * s.get('age', 0),
        ('Causal Recursion Field', 'Recursive Observer Feedback'): lambda s: 0.1 * s.get('ethical_sovereignty', 0.5),
        ('Causal Recursion Field', 'Causal Consistency Enforcement'): lambda s: 0.2 if s.get('drift', 0) < 0.1 else -0.1,

        # Fractal Participatory
        ('Fractal Participatory', 'Scale-Invariant Observation'): lambda s: math.log(s.get('population', 1) + 1) * 0.1,
        ('Fractal Participatory', 'Holographic Encoding'): lambda s: s.get('memory_size', 1000) / 5000.0,
        ('Fractal Participatory', 'Recursive Awareness'): lambda s: 0.1 * s.get('recursive_depth', 0),
        ('Fractal Participatory', 'Fractal Reality Simulation'): lambda s: 0.05 * (s.get('generation', 1) ** 0.5),

        # Quantum-Biological Bridge
        ('Quantum-Biological Bridge', 'Microtubule Resonance'): lambda s: s.get('emotional_resonance', 0) * 0.3,
        ('Quantum-Biological Bridge', 'Orchestrated Coherence'): lambda s: 0.2 if s.get('coherence', 0.5) > 0.8 else 0.05,
        ('Quantum-Biological Bridge', 'Biological Quantum Tunneling'): lambda s: 0.1 * s.get('entropy', 0.3),
        ('Quantum-Biological Bridge', 'Information-Mass Conversion'): lambda s: s.get('memory_size', 1000) / 10000.0,
    }
    return registry.get((ontology, mechanism), lambda s: 0.01)  # safe fallback

# ----------------------------------------------------------------------
# Helper: validate and clamp context values
# ----------------------------------------------------------------------
def _validate_context(ctx: Dict[str, Any]) -> Dict[str, Any]:
    """Ensure all required context parameters exist and are within sensible ranges."""
    validated = {}
    validated['paradox_intensity'] = max(0.0, min(10.0, ctx.get('paradox_intensity', 1.0)))
    validated['coherence'] = max(0.0, min(1.0, ctx.get('coherence', 0.7)))
    validated['novelty'] = max(0.0, min(1.0, ctx.get('novelty', 0.5)))
    validated['alienness'] = max(0.0, ctx.get('alienness', 5.0))
    validated['entropic_potential'] = max(0.0, ctx.get('entropic_potential', 250.0))
    validated['phase_space_coords'] = ctx.get('phase_space_coords', (1.0, 1.0, 2.0, 2.0, 0.5))
    validated['paradox_type'] = ctx.get('paradox_type', 'meta_ontological')
    validated['observer_intention'] = max(0.0, min(1.0, ctx.get('observer_intention', 0.5)))
    return validated

# ----------------------------------------------------------------------
# Core forging function
# ----------------------------------------------------------------------
def forge_enhanced_equation(
    enh_id: int,
    base_eq: str,
    context: Dict[str, Any]
) -> Callable[[Dict[str, Any]], float]:
    """
    MOGOPS Production Algorithm – merges a base equation with operators,
    mechanisms, and the Sophia point. All audit points addressed.
    """
    # 1. Unique seed
    random.seed((enh_id, time.time_ns(), random.getrandbits(32)))

    # 2. Validate context
    ctx = _validate_context(context)
    P_i = ctx['paradox_intensity']
    C   = ctx['coherence']
    N   = ctx['novelty']
    A   = ctx['alienness']
    E_p = ctx['entropic_potential']

    # 3. Select operator using dynamic weights (one per operator)
    op_weights = []
    for sym in OPERATOR_SYMBOLS:
        if sym == 'Ĉ':
            w = 0.3 * (1 + P_i)
        elif sym == '∇_O':
            w = 0.25 * (1 - abs(P_i - 0.5))
        elif sym == 'Ω_V':
            w = 0.2 * (1 - P_i)
        elif sym == 'Ω_Σ':
            w = 0.15 * C
        elif sym == '⊕':
            w = 0.1 * (1 + math.sin(P_i * math.pi))
        else:
            w = 0.1  # fallback for other operators
        op_weights.append(max(w, EPSILON))

    total = sum(op_weights)
    op_weights = [w / total for w in op_weights]

    op_symbol = random.choices(OPERATOR_SYMBOLS, weights=op_weights)[0]

    # 4. Sample three mechanisms from three randomly chosen ontologies
    ontology_names = list(ONTOLOGY_MECHANISMS.keys())
    chosen_ontologies = random.sample(ontology_names, 3)
    mechanisms = []
    for onto in chosen_ontologies:
        mech_list = ONTOLOGY_MECHANISMS.get(onto, [])
        mech = random.choice(mech_list) if mech_list else "DefaultMechanism"
        mechanisms.append((onto, mech))

    # 5. Compute mechanism hybridity
    hybridity = len(set(onto for onto, _ in mechanisms)) / 3.0

    # 6. Encode paradox type
    encoded_paradox = math.sin(P_i * math.pi) * math.cos(C * math.pi)

    # 7. Compute consequence (used later in compute)
    #    This will be incorporated into the final result.
    consequence = 0.0
    for i, (onto, mech) in enumerate(mechanisms):
        mech_func = _mechanism_registry(onto, mech)
        mech_val = mech_func(ctx)
        consequence += mech_val * (i + 1) * encoded_paradox

    # 8. Check phase transition using JSON-loaded thresholds
    phase_transition = (
        abs(C - SOPHIA_POINT) < COH_TOL and
        P_i > PARADOX_MIN and
        hybridity > HYBRIDITY_MIN
    )

    # 9. Build merged expression (for logging, not used in compute)
    merged_expr = f"{base_eq} ⊗ {op_symbol}({mechanisms}) · φ"
    if phase_transition:
        merged_expr += " ⨯ Φ_SOPHIA"

    # 10. Define the compute function that will be returned
    def compute(state: Dict[str, Any]) -> float:
        # Evaluate base equation (with safe .get())
        base_val = _evaluate_base_equation(base_eq, state)

        # Apply operator effect
        op_val = _apply_operator_effect(op_symbol, base_val, state, ctx)

        # Mechanisms contribution (using state, not context)
        mech_val = 0.0
        for onto, mech in mechanisms:
            mech_func = _mechanism_registry(onto, mech)
            mech_val += mech_func(state)

        # Combine base, operator, mechanisms, and consequence
        # (consequence is derived from context, not state)
        result = (base_val + op_val + mech_val + consequence) * INV_PHI

        # Apply phase transition if active
        if phase_transition:
            phase_factor = math.exp(2j * math.pi * abs(C - SOPHIA_POINT))
            result *= (phase_factor.real + 1.0) * 0.5

        # Clamp to avoid extreme values
        return max(-1e6, min(1e6, result))

    return compute


def _evaluate_base_equation(base_eq: str, state: Dict[str, Any]) -> float:
    """
    Enhanced evaluation of base equation strings. Supports common patterns.
    Uses .get() to avoid KeyError.
    """
    base_eq_lower = base_eq.lower()
    if "random" in base_eq_lower:
        return random.random()
    elif "forecast" in base_eq_lower:
        return state.get('drift', 0.0)
    elif "sophia" in base_eq_lower:
        return state.get('sophia_score', 0.5)
    elif "coherence" in base_eq_lower:
        return state.get('coherence', 0.7)
    elif "entropy" in base_eq_lower:
        return state.get('entropy', 0.3)
    elif "intelligence" in base_eq_lower:
        return state.get('intelligence', 50.0) / 100.0
    elif "population" in base_eq_lower:
        return state.get('population', 1) / 1000.0  # normalized
    else:
        # If no match, return a neutral value (1.0) but could log a warning
        return 1.0


def _apply_operator_effect(op_symbol: str, base_val: float,
                            state: Dict[str, Any], context: Dict[str, Any]) -> float:
    """
    Apply a MOGOPS operator's effect with realistic transformations.
    All state accesses use .get() with defaults.
    """
    if op_symbol == 'Ĉ':
        novelty = context.get('novelty', 0.5)
        return base_val * (1.0 + novelty * math.sin(state.get('age', 0) * 0.1))
    elif op_symbol == '∇_O':
        return base_val * (state.get('coherence', 1.0) - state.get('drift', 0.0))
    elif op_symbol == 'Ω_V':
        r1 = random.gauss(0.5, 0.2)
        r2 = random.gauss(0.5, 0.2)
        r3 = random.gauss(0.5, 0.2)
        return base_val * (r1 + r2 + r3) / 3.0
    elif op_symbol == 'Ω_Σ':
        return base_val * math.cos(math.pi / 3 + state.get('phase', 0))
    elif op_symbol == '⊕':
        obs = context.get('observer_intention', 0.5)
        return base_val * (1.0 + obs * 0.2)
    elif op_symbol == 'ℱ':
        return base_val * (1.0 + state.get('recursive_depth', 0) * 0.01)
    elif op_symbol == 'Î_m':
        mem = state.get('memory_size', 1000) / 1000.0
        return base_val * (1.0 + mem * 0.1)
    elif op_symbol == 'Ĝ_ent':
        return base_val * (1.0 - state.get('entropy', 0.3) * 0.5)
    else:
        return base_val


__all__ = ['forge_enhanced_equation', 'PHI', 'INV_PHI', 'SOPHIA_POINT']
