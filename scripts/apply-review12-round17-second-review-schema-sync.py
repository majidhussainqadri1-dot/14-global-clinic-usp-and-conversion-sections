from pathlib import Path
p = Path('scripts/review80-second.py')
s = p.read_text(encoding='utf-8')
old = '("03 current schema identities remain separated", "GCU_SCHEMA_VERSION\', 10005" in loader and "GCU_FUTURE_SCHEMA_VERSION\', 1" in loader),'
new = '("03 current schema identities remain separated", "GCU_SCHEMA_VERSION\', 10006" in loader and "GCU_FUTURE_SCHEMA_VERSION\', 1" in loader),'
if s.count(old) != 1:
    raise SystemExit(f'second-review schema anchor count={s.count(old)}')
p.write_text(s.replace(old, new, 1), encoding='utf-8')
print('second-review schema expectation synchronized to 10006')
