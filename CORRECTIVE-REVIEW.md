# File 14 Corrective Review — v0.1.1

## Governing decision

The original `0.1.0` source remains preserved in the baseline branch and original ZIP. Version `0.1.1` is a separate corrective candidate and is not production-approved until Hostinger staging acceptance is complete.

## Defects corrected

1. Removed unresolved public zero-commission promises and enforced neutral Business Policy wording.
2. Rebuilt activation, upgrade, and repair so existing pages are never overwritten or forcibly republished.
3. Added strict page/post-status/permalink validation and conditional CTA rendering.
4. Removed regex and closing-`nav` companion HTML mutation.
5. Registered Doctor Portal through File 20's `sabri_shell_navigation_destinations` filter.
6. Removed nested `main` landmarks and added instance-safe IDs plus duplicate-output guards.
7. Enforced 44px minimum interactive targets.
8. Standardized Founder metadata.
9. Converted public and admin strings to the plugin text domain and loaded translations.
10. Added idempotent upgrades, health reporting, safe repair, activation snapshots, and rollback.
11. Added conditional component assets and a separate footer stylesheet.
12. Added route-aware footer suppression.
13. Added PHP 7.4/8.3 lint, static corrective contracts, current checksums, and baseline provenance verification.

## Remaining acceptance gates

- Fresh WordPress staging installation.
- Upgrade from exact `0.1.0` with real existing pages and data.
- Conflicting slug/content preservation test.
- File 00, Files 07–09, and File 20 integration test.
- Responsive tests at approved viewports.
- Keyboard, landmarks, focus, contrast, and reduced-motion checks.
- Cache purge and cross-browser verification.
- Settings rollback and backup restore.
- Founder visual and functional acceptance.

No live or production-ready claim is made before these gates pass.
