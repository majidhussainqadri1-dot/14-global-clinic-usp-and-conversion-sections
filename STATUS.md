# File 14 Status — v1.3.0 Central-Plan Harmonization

## Repository coding status

- Governing freeze: freshly supplied consolidated central governing plan + fresh `SSH-F14-PLAN-2026-v1.0`.
- Baseline branch before this pass: `complete/file-14-plan-v1.0.0` at `2625e1f902d40f62107c0f17670711a0414badca`.
- Software candidate: `1.3.0`.
- File 14-owned FR/NFR implementation: coded in repository candidate.
- Fresh central-plan deltas implemented: exact `#087A4E` primary token; File 20 navigation ownership; no inline event handlers; no duplicate `main` landmark; complete en-US/ur-PK/ar-SA File 14 chrome; RTL/LTR direction; GPC; route-scoped attribution; Save-Data/reduced-data; stronger reflow/forced-colors; pinned CI actions; obsolete bootstrap removal.
- Deterministic package target: `14-global-clinic-usp-integration-1.3.0.zip` plus SHA-256.

## Truth-status boundary

Repository coding, packaging and automated QA are not staging/live evidence. The following remain external release gates until independently proven in the target environment:

- Hostinger-equivalent fresh install and upgrade/migration acceptance.
- Real File 00/07/08/09/20/24/25 integration and degraded-state journeys.
- 320–1920px, 400% zoom, keyboard, screen-reader, English LTR and Urdu/Arabic RTL human acceptance.
- Measured p75/p95 performance and provider/queue/cache/DB failure tests.
- Backup restore and rollback rehearsal.
- Founder visual/copy/functional acceptance.
- Controlled production deployment, live smoke test, monitoring and parity confirmation.

No staging, live-deployed or operational-complete claim is made by this repository status file.
