# R71: Forty scoped File14 destination/filter behavioral reviews (v1.4.73)

Date: 2026-10-20. Source frozen from main SHA ef9651eb4b394896942c48ddde6d77c11ee28e92 (v1.4.72).

## Governing sources

Attached amended File14 v2.0 plan, F14-FR-003 Primary CTAs and F14-FR-009 Destination health for Files07/08/09. Degraded and invalid cases must fail safely. Attached Three-Central-Plans Governing Master Plan: unresolved destinations must not become dead links, and all owners retain source-of-truth.

Actual GitHub File14 class-gcu-contracts.php and class-gcu-companion-adapters.php are used as source evidence. Stale destination-state options cannot grant readiness. Signed owner events #19, real changed-placement CAS #22 and privacy-safe File08 booking aggregates #70 remain independent owner/staging blockers.

## Root cause and code corrections

R71-D01: Owner readiness was coerced through empty() even when available was a nonboolean truthy value. New native owner evidence requires available to be exactly boolean true and the URL an explicitly typed strict same-origin string.

R71-D02: Filter response nonarray, absent available, string false, missing/rewritten URLs could preserve an enabled CTA. New consumer requires a full array, exactly boolean true in available, and a canonical owner-identical URL; all uncertainty fails closed.

## Forty different method-level scenario tests

tests/r71-forty-destination-filter-fail-closed-tests.php calls the real GCU_Contracts::destination() PHP source method under forty different mocked WordPress/owner/filter payloads. Each case checks exact available state and cleared/unchanged URL. The test suite runs each of the forty complete review rounds after preexisting plan, owner and privacy checks. These are source-level PHP mocks, NOT installed WordPress, database, browser, or staging acceptance.

| Round | Mock variation | Expected outcome |

|---:|---|---|

| 01 | Canonical owner ready and unchanged consumer filter | CTA accessible, unchanged owner URL |
| 02 | Native owner explicitly false | CTA unavailable, URL cleared |
| 03 | Owner readiness string true | CTA unavailable, URL cleared |
| 04 | Owner readiness string false | CTA unavailable, URL cleared |
| 05 | Owner readiness numeric 1 | CTA unavailable, URL cleared |
| 06 | Owner readiness numeric 0 | CTA unavailable, URL cleared |
| 07 | Owner readiness null | CTA unavailable, URL cleared |
| 08 | Owner readiness array | CTA unavailable, URL cleared |
| 09 | Owner readiness absent | CTA unavailable, URL cleared |
| 10 | Owner URL missing | CTA unavailable, URL cleared |
| 11 | Owner URL external | CTA unavailable, URL cleared |
| 12 | Native false with otherwise valid URL | CTA unavailable, URL cleared |
| 13 | Owner URL numeric | CTA unavailable, URL cleared |
| 14 | Owner URL array | CTA unavailable, URL cleared |
| 15 | Owner URL null | CTA unavailable, URL cleared |
| 16 | Owner URL empty | CTA unavailable, URL cleared |
| 17 | Filter veto boolean false | CTA unavailable, URL cleared |
| 18 | Filter available integer zero | CTA unavailable, URL cleared |
| 19 | Filter available string false | CTA unavailable, URL cleared |
| 20 | Filter available string 0 | CTA unavailable, URL cleared |
| 21 | Filter available string true | CTA unavailable, URL cleared |
| 22 | Filter available integer 1 | CTA unavailable, URL cleared |
| 23 | Filter available null | CTA unavailable, URL cleared |
| 24 | Filter available empty string | CTA unavailable, URL cleared |
| 25 | Filter returns empty array | CTA unavailable, URL cleared |
| 26 | Filter returns scalar false | CTA unavailable, URL cleared |
| 27 | Filter returns scalar true | CTA unavailable, URL cleared |
| 28 | Filter returns numeric zero | CTA unavailable, URL cleared |
| 29 | Filter returns scalar null | CTA unavailable, URL cleared |
| 30 | Filter returns arbitrary string | CTA unavailable, URL cleared |
| 31 | Filter omits available | CTA unavailable, URL cleared |
| 32 | Filter omits URL | CTA unavailable, URL cleared |
| 33 | Filter URL null | CTA unavailable, URL cleared |
| 34 | Filter URL false | CTA unavailable, URL cleared |
| 35 | Filter URL integer | CTA unavailable, URL cleared |
| 36 | Filter URL array | CTA unavailable, URL cleared |
| 37 | Filter URL external | CTA unavailable, URL cleared |
| 38 | Filter URL rewritten same-origin | CTA unavailable, URL cleared |
| 39 | Filter URL empty | CTA unavailable, URL cleared |
| 40 | Filter harmless reason edit preserving exact identity | CTA accessible, unchanged owner URL |

## Completion and owner blockers

File14 cannot implement and falsely attest to File01 signed producer registry or File07/08/09 confirmed conversion events (#19), File20/25 actual changed-placement CAS restoration (#22), or a consent-safe independent File08 booking aggregate (#70) from this consumer safety change. No foreign owner DB/code was changed.

Full PHP7.4/8.3 Quality, both Fresh Reviews, Baseline Integrity, deterministic package and SBOM, exact merged-main CI are mandatory to confirm repository source correction. Unverified deployed version or DB/migration cannot be called resolved.

Repository HEAD at freeze: ef9651eb4b394896942c48ddde6d77c11ee28e92. Deployed Version: UNVERIFIED. DB Version: UNVERIFIED. Migration State: UNVERIFIED. Live Verification Status: NOT RUN.

Exact deployed code ابھی unverified ہے؛ repository-based diagnosis provisional ہے۔