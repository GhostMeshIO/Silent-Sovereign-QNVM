"""
mogops_equation_forge.py – Full MOGOPS Production Algorithm & Equation Forge.
Now loads all equations, operators, and mechanisms from mogops_equations.json.
"""

import json
import random
import math
import os
from typing import Dict, Any, Callable, List, Tuple

# Load the JSON file (assumed to be in same directory)
_JSON_PATH = os.path.join(os.path.dirname(__file__), 'mogops_equations.json')
with open(_JSON_PATH, 'r') as f:
    MOGOPS_DATA = json.load(f)

# Extract constants
PHI = MOGOPS_DATA['constants']['PHI']
INV_PHI = MOGOPS_DATA['constants']['INV_PHI']
SOPHIA_POINT = MOGOPS_DATA['constants']['SOPHIA_POINT']

# Build operator lookup
OPERATORS = {op['symbol']: op for op in MOGOPS_DATA['operators']}

# Build ontology mechanisms lookup
ONTOLOGY_MECHANISMS = {}
for onto in MOGOPS_DATA['ontologies']:
    ONTOLOGY_MECHANISMS[onto['name']] = onto['mechanisms']

# Phase transition criteria (simplified)
PHASE_TRANSITION_CRITERIA = MOGOPS_DATA['phase_transition_criteria']


def forge_enhanced_equation(
    enh_id: int,
    base_eq: str,
    context: Dict[str, Any]
) -> Callable[[Dict[str, Any]], float]:
    """
    MOGOPS Production Algorithm – merges a base equation with operators,
    mechanisms, and the Sophia point, using data loaded from JSON.
    """
    # 1. Deterministic seeding based on enh_id (for reproducibility)
    random.seed(enh_id)

    # 2. Extract context metrics (with defaults)
    P_i = context.get('paradox_intensity', 1.0)
    C   = context.get('coherence', 0.7)
    N   = context.get('novelty', 0.5)
    A   = context.get('alienness', 5.0)
    E_p = context.get('entropic_potential', 250.0)
    coords = context.get('phase_space_coords', (1.0, 1.0, 2.0, 2.0, 0.5))

    # 3. Select operator using weighted probabilities (from algorithm)
    op_weights = [
        0.3 * (1 + P_i),                     # Ĉ
        0.25 * (1 - abs(P_i - 0.5)),         # ∇_O
        0.2 * (1 - P_i),                      # Ω_V
        0.15 * C,                              # Ω_Σ
        0.1 * (1 + math.sin(P_i * math.pi))    # ⊕
    ]
    op_symbols = list(OPERATORS.keys())
    op_symbol = random.choices(op_symbols, weights=op_weights)[0]
    operator_info = OPERATORS[op_symbol]

    # 4. Sample three mechanisms from three randomly chosen ontologies
    ontology_names = list(ONTOLOGY_MECHANISMS.keys())
    chosen_ontologies = random.sample(ontology_names, 3)
    mechanisms = []
    for onto in chosen_ontologies:
        mech = random.choice(ONTOLOGY_MECHANISMS[onto])
        mechanisms.append((onto, mech))

    # 5. Encode paradox type mathematically (simplified)
    paradox_type = context.get('paradox_type', 'meta_ontological')
    encoded_paradox = math.sin(P_i * math.pi) * math.cos(C * math.pi)

    # 6. Compute consequence (simplified)
    consequence = 0.0
    for i, (onto, mech) in enumerate(mechanisms):
        # Here you could use a more sophisticated coherence measure
        onto_coherence = 0.8  # placeholder
        consequence += onto_coherence * (i + 1) * encoded_paradox

    # 7. Check phase transition criteria
    phase_transition = False
    # Parse the condition string (simplified; real implementation would eval)
    if abs(C - SOPHIA_POINT) < 0.02 and P_i > 1.8 and len(mechanisms) == 3:
        phase_transition = True

    # 8. Build merged expression for logging
    merged_expr = f"{base_eq} ⊗ {op_symbol}({mechanisms}) · φ"
    if phase_transition:
        merged_expr += " ⨯ Φ_SOPHIA"

    # 9. Define compute function
    def compute(state: Dict[str, Any]) -> float:
        # Evaluate base equation (simplified)
        base_val = _evaluate_base_equation(base_eq, state)

        # Apply operator effect (simplified)
        op_val = _apply_operator_effect(op_symbol, base_val, state, context)

        # Mechanisms contribution
        mech_val = 0.0
        for onto, mech in mechanisms:
            mech_func = _get_mechanism_function(onto, mech)
            mech_val += mech_func(state)

        # Combine with Sophia point
        result = (op_val + mech_val) * INV_PHI

        # Apply phase transition if active
        if phase_transition:
            # Simple amplification
            result *= (1 + 0.1 * math.sin(P_i * math.pi))

        return result

    return compute


def _evaluate_base_equation(base_eq: str, state: Dict[str, Any]) -> float:
    """Simplified evaluation of base equation strings."""
    if "random" in base_eq:
        return random.random()
    elif "forecast" in base_eq:
        return state.get('drift', 0.0)
    elif "sophia" in base_eq:
        return state.get('sophia_score', 0.5)
    else:
        return 1.0


def _apply_operator_effect(op_symbol: str, base_val: float,
                            state: Dict[str, Any], context: Dict[str, Any]) -> float:
    """Apply a MOGOPS operator's effect (simplified)."""
    # In a full implementation, each operator would have a specific function.
    # Here we just return base_val modified by some factor.
    if op_symbol == 'Ĉ':
        # Creation: increase by novelty
        novelty = context.get('novelty', 0.5)
        return base_val * (1 + novelty)
    elif op_symbol == '∇_O':
        # Entailment gradient: adjust by coherence change
        return base_val * state.get('coherence', 1.0)
    elif op_symbol == 'Ω_V':
        # Via triad: average of three random factors
        return base_val * (random.random() + random.random() + random.random()) / 3
    elif op_symbol == 'Ω_Σ':
        # Encoding bridge: complex phase factor (real part)
        return base_val * math.cos(math.pi / 3)
    elif op_symbol == '⊕':
        # Participatory weave: combine with observer intention (simplified)
        return base_val * (1 + context.get('observer_intention', 0))
    else:
        return base_val


def _get_mechanism_function(ontology: str, mechanism: str) -> Callable:
    """Return a function that computes the effect of a mechanism."""
    # In a real system, you would have a registry mapping (ontology, mechanism)
    # to a callable. Here we return a placeholder.
    def default_mech(state):
        return state.get('coherence', 0.5) * 0.1
    return default_mech


__all__ = ['forge_enhanced_equation', 'PHI', 'INV_PHI', 'SOPHIA_POINT']
