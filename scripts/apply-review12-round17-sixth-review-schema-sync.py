from pathlib import Path
p = Path('scripts/review80-sixth.py')
s = p.read_text(encoding='utf-8')
old = "add('01 sixth-review schema and minimum release identity', version_tuple >= (1,4,4) and \"GCU_SCHEMA_VERSION', 10005\" in loader)"
new = "add('01 sixth-review schema and minimum release identity', version_tuple >= (1,4,4) and \"GCU_SCHEMA_VERSION', 10006\" in loader)"
if s.count(old) != 1:
    raise SystemExit(f'sixth-review schema anchor count={s.count(old)}')
p.write_text(s.replace(old, new, 1), encoding='utf-8')
print('sixth-review schema expectation synchronized to 10006')
