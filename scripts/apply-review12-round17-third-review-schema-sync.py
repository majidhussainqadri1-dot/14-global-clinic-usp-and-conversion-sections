from pathlib import Path
p = Path('scripts/review80-third.py')
s = p.read_text(encoding='utf-8')
old = "('02 base/Future schema identities remain separated',\"GCU_SCHEMA_VERSION', 10005\" in loader and \"GCU_FUTURE_SCHEMA_VERSION', 1\" in loader),"
new = "('02 base/Future schema identities remain separated',\"GCU_SCHEMA_VERSION', 10006\" in loader and \"GCU_FUTURE_SCHEMA_VERSION', 1\" in loader),"
if s.count(old) != 1:
    raise SystemExit(f'third-review schema anchor count={s.count(old)}')
p.write_text(s.replace(old, new, 1), encoding='utf-8')
print('third-review schema expectation synchronized to 10006')
