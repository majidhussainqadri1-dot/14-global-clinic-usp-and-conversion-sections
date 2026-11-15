#!/usr/bin/env python3
"""V12.3 File14 repository source preflight. This cannot verify deployed state."""
from __future__ import annotations
import argparse
import json
import re
import sys
from pathlib import Path

ROOT = Path(__file__).resolve().parents[1]
PREFIX = ROOT / '14-global-clinic-usp-integration' / 'includes'
BLOCKED_IDS = {
    '#19': ('F14-FR-011', 'F14-FUT-11', 'F14-FUT-13', 'F14-FUT-14'),
    '#22': ('F14-FUT-17',),
    '#70': ('F14-FR-011',),
}


def expected_ids():
    return tuple(f'F14-FR-{n:03}' for n in range(1, 17)) + tuple(
        f'F14-NFR-{n:03}' for n in range(1, 11)
    ) + ('F14-CEN-01', 'F14-CEN-02') + tuple(
        f'F14-FUT-{n:02}' for n in range(1, 25)
    )


def function_body(src: str, name: str) -> str:
    """Conservative PHP body scan; missing/incomplete definitions fail closed."""
    m = re.search(r'\bfunction\s+' + re.escape(name) + r'\s*\(', src)
    if not m:
        return ''
    start = src.find('{', m.end())
    if start < 0:
        return ''
    depth = 0
    for i in range(start, len(src)):
        if src[i] == '{':
            depth += 1
        elif src[i] == '}':
            depth -= 1
            if depth == 0:
                return src[start + 1:i]
    return ''


def read_php(path: str) -> str:
    return (PREFIX / path).read_text(encoding='utf-8')


def source_observations() -> dict:
    policy = read_php('class-gcu-future-policy.php')
    outcomes = read_php('class-gcu-owner-outcomes.php')
    baseline = read_php('class-gcu-experiment-baseline.php')
    privacy = read_php('class-gcu-privacy.php')
    adapters = read_php('class-gcu-companion-adapters.php')
    confirmed = function_body(policy, 'owner_confirmation_contract_ready')
    measurement = function_body(outcomes, 'measurement_capabilities')
    restore = function_body(policy, 'experiment_baseline_restoration_ready')
    reconcile = function_body(baseline, 'reconcile_after_stop')
    blocked_cas = bool(re.search(
        r'\bif\s*\(\s*self::experiment_requires_baseline_restore\s*\([^;]+\)\s*\)\s*\{\s*return\s+false\s*;',
        restore
    ))
    return {
        'owner_signed_contract_accepted': bool(confirmed) and not bool(re.search(r'\breturn\s+false\s*;', confirmed)),
        'changed_placement_cas_restore_accepted': bool(restore) and not blocked_cas,
        'owner_booking_aggregate_available': bool(measurement) and not (
            'unavailable_sensitive_rest_aggregate_unapproved' in measurement
            or bool(re.search(r"'booking_owner_outcome_count'\s*=>\s*null", measurement))
        ),
        'privacy_boundary_present': all(s in privacy for s in (
            'measurement_allowed', 'global_privacy_control_requested', 'is_file14_acquisition_route'
        )),
        'authoritative_file00_checks_present': all(s in adapters for s in (
            'file00_assertions', 'SMC_Contracts::assertions',
            "true !== $claim['eligible']", "false !== $claim['suspended']"
        )),
        'baseline_reconciliation_present': bool(reconcile) and 'manual_review' in reconcile,
        'reports_unverified_as_null': bool(re.search(r"'booking_success_rate'\s*=>\s*null", measurement)),
    }


def examine() -> dict:
    ids = expected_ids()
    controls = source_observations()
    outstanding = {
        '#19': not controls['owner_signed_contract_accepted'],
        '#22': not controls['changed_placement_cas_restore_accepted'],
        '#70': not controls['owner_booking_aggregate_available'],
    }
    blockers = {k: list(BLOCKED_IDS[k]) for k, is_open in outstanding.items() if is_open}
    controls_present = all(controls[k] for k in (
        'privacy_boundary_present', 'authoritative_file00_checks_present',
        'baseline_reconciliation_present', 'reports_unverified_as_null'
    ))
    return {
        'plan': 'V12.3', 'file': '14', 'audit_type': 'repository-source-only',
        'requirements_in_scope': len(ids),
        'requirement_families': {'FR': 16, 'NFR': 10, 'CEN': 2, 'FUT': 24},
        'known_unaccepted_cross_owner_issues': blockers,
        'affected_requirement_ids': sorted({r for values in blockers.values() for r in values}),
        'source_fail_closed_controls_present': controls_present,
        'source_observations': controls,
        'source_tests_verified_by_this_script': False,
        'repository_ci_verified_by_this_script': False,
        'deployed_version': None, 'db_version': None, 'migration_state': None,
        'deployment_parity_verified': False, 'staging_accepted': False,
        'live_retested': False, 'production_ready': False,
    }


def main(argv=None) -> int:
    parser = argparse.ArgumentParser(description=__doc__)
    parser.add_argument('--release-gate', action='store_true',
                        help='Fail unless owner and independent deployed verification is complete')
    parser.add_argument('--compact', action='store_true')
    args = parser.parse_args(argv)
    try:
        report = examine()
    except (OSError, UnicodeDecodeError) as exc:
        print(json.dumps({'preflight': 'failed', 'error': str(exc)}), file=sys.stderr)
        return 2
    print(json.dumps(report, ensure_ascii=False,
                     indent=None if args.compact else 2, sort_keys=True))
    if not report['source_fail_closed_controls_present']:
        return 2
    if args.release_gate and (report['known_unaccepted_cross_owner_issues']
                              or not report['production_ready']):
        return 1
    return 0


if __name__ == '__main__':
    raise SystemExit(main())
