# File 14 Architecture — Global Clinic USP and Conversion Integration

## Canonical boundary

File 14 owns approved value-proposition copy, semantic reusable blocks, placements, claim evidence, ethical conversion events, experiments, and destination-readiness contracts. It never owns or directly writes doctor identity/profile (Files 03/07/09), clinic/appointment truth (File 08), membership/capabilities (File 00), shell/navigation (File 20), visual-system ownership (File 25), payments, or clinical records.

## Runtime shape

- WordPress plugin folder: `14-global-clinic-usp-integration`
- Text domain: `global-clinic-usp-integration`
- PHP namespace/prefix law: `GCU_`
- Public routes: `/global-clinic/`, `/find-a-global-doctor/`, `/start-your-global-clinic/`, `/clinic/how-it-works/`
- Versioned REST API: `gcu/v1`
- Native data: claims, content blocks, placements, experiments, privacy-minimized funnel events, audit log, event outbox and event inbox.

## Ownership law

All File 14 writes pass through `GCU_Repository`. Companion modules integrate through versioned filters/actions and safe destination contracts. File 14 never searches or modifies another plugin’s database tables. Owner-destination failure produces an honest degraded state and cannot infer a booking, application, verification, payment, or clinical result.

## State machines

- Copy: `draft → policy_review → founder_approved → active → superseded/withdrawn`
- Placement: `planned → preview → active → paused/expired`
- Experiment: `proposed → approved → running → stopped → analyzed → adopted/rejected`

Transitions require native capability, current state, expected row version, required fields and audit reason. Lost updates fail with a stable 409 machine code.

## Reliability

Events are first persisted in `gcu_event_outbox`; immediate delivery is attempted, then bounded exponential retry is performed by an hourly worker. Five failures produce a dead-letter state visible in System Check. Inbound dependency events are deduplicated by `gcu_event_inbox`.
