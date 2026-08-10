=== Global Clinic USP and Conversion Integration ===
Contributors: majidhussainqadri1-dot
Tags: clinic, doctors, conversion, accessibility, privacy, governance
Requires at least: 6.6
Tested up to: 7.0.1
Requires PHP: 7.4
Stable tag: 1.3.1
License: GPLv2 or later

Canonical File 14 implementation for approved Worldwide Clinic value-proposition content, ethical conversion journeys, claim governance, destination contracts and privacy-minimized measurement.

== Description ==
File 14 owns approved patient/doctor value-proposition blocks, File 20 placement contracts, claim evidence/version history, ethical conversion diagnostics, `/global-clinic/`, `/clinic/how-it-works/`, and versioned destinations to Files 07/08/09. It never owns doctor profiles, verification evidence, clinic records, appointments, payments, clinical records, the global shell/navigation or visual-system truth.

== Business, trust and safety policy ==
* 0% platform commission on approved clinic flows.
* One currently approved free tier for approved core platform features.
* Optional support never purchases ranking, visibility, verification or basic service.
* Doctor application does not guarantee approval.
* Verification is not a cure, income or outcome guarantee.
* This platform is not an emergency service.
* No fake metrics, testimonials, scarcity pressure or automatic verification/booking inference.

== Reliability and security ==
* Explicit InnoDB owner schema verification before version promotion.
* MySQL named locks for install/upgrade, content-version allocation and audit-chain append.
* Optimistic concurrency plus transactional workflow transitions and claim withdrawal/history.
* Database-backed single-use measurement tokens and atomic rate-limit buckets.
* Durable idempotency command state with bounded retry/recovery.
* Outbox/inbox processing with stale-lock recovery, retry, exponential backoff and dead-letter state.
* Strict same-origin destination validation by scheme, host and effective port.
* Tamper-evident audit chain with full/partial verification status.
* Bounded, hashed owner snapshot and transactional owner-record rollback.
* Non-destructive uninstall by default; destructive purge requires explicit dual guard.

== Privacy and accessibility ==
Public content works without measurement. Measurement requires explicit consent, is disabled under Global Privacy Control, is excluded from sensitive routes, uses bounded random pseudonyms and can be exported/erased through WordPress privacy tools. Browser attribution is signed, restricted to File 14 acquisition routes and bounded to 30 days; guest pseudonyms expire after 24 hours. Save-Data and reduced-data clients suppress nonessential measurement.

The File 14 surface includes complete File 14-owned American English, Urdu and Arabic interface chrome, logical LTR/RTL direction, 44px targets, focus-visible treatment, reduced motion/data, forced-colors support, fluid reflow down to the 320px class and no inline executable markup. File 20 remains the sole owner of global navigation; File 14 only consumes its Back/Home contract with a bounded local fallback.

== Installation ==
1. Install only on staging first.
2. Verify database/files/configuration backup and a restore rehearsal.
3. Activate the plugin; activation fails closed if owner schema cannot be verified.
4. Review Settings → Global Clinic USP.
5. Verify Files 07, 08, 09, 20 and 25 contracts and degraded states.
6. Test `/global-clinic/`, `/clinic/how-it-works/`, English LTR, Urdu/Arabic RTL, keyboard, screen reader, 320–1920px and 400% zoom.
7. Test Save-Data, reduced motion, consent withdrawal, GPC, provider outage, queues, fresh install/upgrade, backup restore and rollback.
8. Obtain explicit Founder acceptance before production deployment.

== Changelog ==
= 1.3.1 =
* Completed deeper reliability/security correction against the fresh central plan and fresh File 14 plan.
* Raised schema to 10004 with explicit InnoDB verification and fail-closed upgrade status.
* Added database event tokens, atomic rate limiting, durable idempotency, five-minute inbox/outbox processing, stale-lock recovery and lifecycle cleanup.
* Added strict scheme/host/port same-origin checks, independent owner destination states and fail-closed File 20 slot readiness.
* Added transactional content activation/supersession, claim withdrawal/history and verified owner-data rollback.
* Added audit-chain locking/verification, expanded health evidence and safer repair/safe-mode gates.
* Added stable random user/guest pseudonyms, bounded guest retention and event export/erasure lifecycle.
* Removed single-use measurement tokens from cacheable HTML; tokens are issued just in time from a no-store endpoint.
* Added deterministic double-build packaging, archive path/CRC verification and file-level SHA-256 SBOM.

= 1.3.0 =
* Adopted exact Sabri Green `#087A4E`, File 20 navigation ownership, complete File14 en-US/ur-PK/ar-SA chrome, GPC, Save-Data and stronger accessibility/RTL controls.

= 1.0.0 =
* Rebuilt File 14 against SSH-F14-PLAN-2026-v1.0 and then-current Founder-approved directives.
