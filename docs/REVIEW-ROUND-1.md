# Fresh Review and Fix Round 1

Scope: governing plan, canonical ownership, legacy v0.1.1 implementation, routes, data, state machines, security/privacy, migration and release evidence.

Defects found and corrected:

1. Legacy plugin owned duplicate Doctor Portal/Mission pages and used old SGC namespace; replaced by the canonical GCU package and approved routes.
2. Most File 14 entities/workflows/APIs were absent; added claims, blocks, placements, experiments, funnel events, audit and versioned commands/queries.
3. Business copy was older/neutral rather than the latest Founder-approved free-tier/0%-commission policy; synchronized while keeping payment truth outside File 14.
4. Activation created pages; canonical package now uses rewrite routes and performs only read-only legacy inventory.
5. No reliable event transport; added outbox/inbox, retry and dead-letter state.
6. FAQ and localization requirements were static/incomplete; added versioned English, Urdu and Arabic block variants.

Regression result: PHP syntax, policy tests and contract tests pass after corrections.
