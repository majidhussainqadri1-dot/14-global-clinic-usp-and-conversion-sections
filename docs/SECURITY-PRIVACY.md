# Security, Privacy and Safety Controls

- Separate authentication and authorization: every mutation re-checks a File 14 capability and object/state context.
- Nonces protect admin mutations; REST mutation permissions are re-evaluated at action time.
- Public measurement requires explicit consent, a single-use signed event token and a privacy-minimized rate-limit bucket.
- Attribution stores only bounded first/last source, medium, campaign and ref values in an HMAC-signed, HttpOnly, SameSite=Lax cookie for 30 days.
- WordPress privacy export/erasure hooks cover browser attribution; no health, identity evidence, message, payment or doctor-profile detail is accepted in funnel events.
- CTA destinations are same-origin and owner-registered; no open redirect is permitted.
- Claim statements carry basis, owner, effective date, review deadline, expiry and withdrawal workflow.
- Public copy explicitly prohibits emergency reliance, cure/outcome/income guarantees, instant verification and paid ranking/visibility.
- Audit records store hashes instead of whole sensitive before/after payloads; structured logs redact common sensitive keys.
- Uninstall is non-destructive unless both `GCU_ALLOW_PURGE` and the explicit purge option are enabled.
