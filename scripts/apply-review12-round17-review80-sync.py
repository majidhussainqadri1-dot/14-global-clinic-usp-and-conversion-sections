from pathlib import Path
p=Path('scripts/review80.py')
s=p.read_text(encoding='utf-8')
old='("18 base schema remains 10005 and separate from patch version", "GCU_SCHEMA_VERSION\', 10005" in loader),'
new='("18 base schema is 10006 after governed cleanup-index migration and remains separate from patch version", "GCU_SCHEMA_VERSION\', 10006" in loader),'
if s.count(old)!=1: raise SystemExit(f'review80 schema anchor count={s.count(old)}')
p.write_text(s.replace(old,new,1),encoding='utf-8')
print('review80 schema expectation synchronized to 10006')
