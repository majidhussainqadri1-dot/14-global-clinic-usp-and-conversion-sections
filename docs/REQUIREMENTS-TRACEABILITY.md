# File 14 Requirements Traceability

| Requirement | Implementation evidence | Automated evidence | Status |
|---|---|---|---|
| F14-FR-001 Patient value proposition | localized patient block, safe directory destination, emergency limit | policy/contract tests | Coded |
| F14-FR-002 Doctor value proposition | localized doctor block, free/0%/support rules, onboarding destination | policy/contract tests | Coded |
| F14-FR-003 Primary CTAs | File 07 and File 09 versioned destination registry | contract tests | Coded |
| F14-FR-004 How it works | canonical route plus patient/doctor journey | contract tests | Coded |
| F14-FR-005 Trust content | trust block plus claim registry | policy/contract tests | Coded |
| F14-FR-006 Business copy | 0% commission, owner fee-flow boundary, optional support | policy tests | Coded |
| F14-FR-007 Placement registry | native placement entity and File 20 slots | contract tests | Coded |
| F14-FR-008 Reusable blocks | semantic block DTO/API, File 25-compatible classes/tokens | contract tests | Coded |
| F14-FR-009 Destination health | File 07/08/09 owner health and honest degraded state | contract tests | Coded |
| F14-FR-010 Campaign attribution | consented signed first/last 30-day attribution | contract tests | Coded |
| F14-FR-011 Funnel events | staged events, anti-replay, no click-success inference | contract tests | Coded |
| F14-FR-012 Experiment governance | hypothesis/variants/audience/duration/metric/guardrails/sample/privacy plus Founder gate | policy/contract tests | Coded |
| F14-FR-013 Localization | en-US canonical; ur-PK/ar-SA core blocks and RTL | policy/contract tests | Coded |
| F14-FR-014 FAQ governance | versioned FAQ blocks with review due date and claims | contract tests | Coded |
| F14-FR-015 Accessibility | headings, labels, SVG icons, 44px, focus, reduced motion, RTL/mobile | contract tests; staging visual gate | Coded / staging pending |
| F14-FR-016 Claim audit | claim basis/owner/effective/review/expiry/withdrawal event | contract tests | Coded |
| F14-NFR-001 Authorization | capabilities, per-command checks, optimistic concurrency | contract tests | Coded |
| F14-NFR-002 Privacy lifecycle | consent, minimization, retention, export/erase, non-destructive uninstall | contract tests | Coded |
| F14-NFR-003 Reliability | idempotent event write, outbox/inbox, bounded retry/dead letter | contract tests | Coded |
| F14-NFR-004 Performance | bounded queries/limits, conditional assets, no heavy public work | static tests; staging p75/p95 pending | Coded / measurement pending |
| F14-NFR-005 Accessibility | responsive RTL CSS, focus, reduced motion, semantic controls | static tests; human staging pending | Coded / staging pending |
| F14-NFR-006 Observability | health report, queue counts, stale claims, redacted structured logs | contract tests | Coded |
| F14-NFR-007 Migration/rollback | lock, dbDelta, snapshot, read-only legacy inventory, safe repair | contract tests; staging drill pending | Coded / staging pending |
| F14-NFR-008 Operability | System Check, safe mode, repair, queue and runbook | contract tests | Coded |
| F14-NFR-009 Compatibility | PHP 7.4/8.3 CI matrix, WordPress coding contracts | GitHub CI pending | Candidate ready |
| F14-NFR-010 Localization | en-US/ur-PK/ar-SA, RTL and locale fallback | policy/contract tests; staging locale QA pending | Coded / staging pending |
