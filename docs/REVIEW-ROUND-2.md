# Fresh Adversarial Review and Fix Round 2

Attack/failure focus: IDOR, stale writes, duplicate events, open redirect, cross-module ownership, delayed cron, absent destination, deceptive conversion, tracking without consent, small-number disclosure, RTL/mobile/accessibility and destructive uninstall.

Corrections and verified controls:

- Expected row version blocks lost updates; invalid state transitions fail closed.
- Event IDs are idempotent; public event tokens are HMAC-signed, single-use and rate-limited.
- Destination URLs are same-origin and owner-registered; unavailable owners produce 503/noindex and no inferred action.
- Measurement is absent without consent; attribution is minimized, signed and expires in 30 days; funnel output suppresses totals below 10.
- Experiments cannot start without approval, future end date, variants, metric, guardrails, sample policy and privacy policy.
- Claim withdrawal is immediate, audited and evented.
- File 14 never creates doctor/clinic/appointment/payment/clinical records.
- Green responsive UI, icons, 44px controls, focus, zoom/RTL and reduced-motion contracts are present.
- Uninstall is non-destructive by default.

Result: zero known unresolved repository-code blockers. Hostinger-equivalent staging and Founder acceptance remain external release gates and are not misreported as complete.
