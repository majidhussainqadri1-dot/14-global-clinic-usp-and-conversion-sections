# Operations and Runbook

## Pre-release

1. Run `bash scripts/quality.sh`.
2. Run `bash scripts/build.sh` and verify the ZIP checksum.
3. Confirm exact branch head, manifest and source/package parity.
4. On isolated staging: verified backup/restore proof, fresh install, upgrade from legacy v0.1.1, repeated activation, concurrent activation, route tests, File 07/08/09 destination contracts, File 20 slots, File 25 visual acceptance, cache purge, privacy export/erase, event retry/dead-letter and rollback.
5. Complete keyboard, focus, 200% zoom, 320–1920px, Urdu/Arabic RTL, reduced-motion and contrast checks.
6. Obtain explicit Founder acceptance before production.

## System Check

Settings → Global Clinic USP shows schema/version, safe mode, missing tables, stale claims, destination health and queue state. Safe repair is owner-scoped: it recreates missing native schema/capabilities/content but never overwrites WordPress pages or companion-module records.

## Incident controls

- Enter safe mode to return 503/noindex on File 14 routes without mutating owner destinations.
- Withdraw a misleading claim immediately through the claim command; cache is purged and `ClinicUSPClaimWithdrawn.v1` is emitted.
- Inspect retry/dead outbox counts; correct the consumer and rerun the worker.
- Restore plugin-owned options from the captured snapshot. Database rollback must be performed from a verified staging/production backup, not guessed from application code.
