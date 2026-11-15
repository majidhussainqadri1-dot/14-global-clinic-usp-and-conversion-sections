#!/usr/bin/env python3
"""Guard V12.3 requirement identity and safe release truth."""
import importlib.util
import unittest
from pathlib import Path

SRC = Path(__file__).resolve().parents[1] / 'scripts' / 'v123_source_gate.py'
spec = importlib.util.spec_from_file_location('v123_source_gate', SRC)
gate = importlib.util.module_from_spec(spec)
spec.loader.exec_module(gate)


class V123SourceGateTests(unittest.TestCase):
    def test_exact_52_requirements(self):
        ids = gate.expected_ids()
        self.assertEqual(52, len(ids))
        self.assertEqual(52, len(set(ids)))
        self.assertEqual((16, 10, 2, 24), tuple(
            sum(s.startswith(p) for s in ids)
            for p in ('F14-FR-', 'F14-NFR-', 'F14-CEN-', 'F14-FUT-')
        ))

    def test_missing_function_fails_closed(self):
        self.assertEqual('', gate.function_body('<?php class X{}', 'missing'))
        self.assertEqual('', gate.function_body('function broken() { return false;', 'broken'))

    def test_live_evidence_cannot_be_inferred(self):
        r = gate.examine()
        self.assertFalse(r['production_ready'])
        self.assertFalse(r['live_retested'])
        self.assertFalse(r['deployment_parity_verified'])
        self.assertIsNone(r['db_version'])
        self.assertIsNone(r['deployed_version'])
        self.assertEqual(52, r['requirements_in_scope'])

    def test_cross_owner_blockers_are_explicit(self):
        r = gate.examine()
        self.assertEqual({'#19', '#22', '#70'}, set(r['known_unaccepted_cross_owner_issues']))
        self.assertEqual(5, len(r['affected_requirement_ids']))
        self.assertTrue(r['source_fail_closed_controls_present'])
        self.assertTrue(r['source_observations']['reports_unverified_as_null'])

    def test_public_tracking_not_unblocked(self):
        r = gate.examine()
        self.assertFalse(r['source_observations']['owner_booking_aggregate_available'])
        self.assertFalse(r['source_observations']['owner_signed_contract_accepted'])
        self.assertFalse(r['source_observations']['changed_placement_cas_restore_accepted'])


if __name__ == '__main__':
    unittest.main()
