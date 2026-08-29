from pathlib import Path

p = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
s = p.read_text(encoding='utf-8')
old = '<input type="hidden" name="route_key value="\' . esc_attr( $route ) . \'">'
new = '<input type="hidden" name="route_key" value="\' . esc_attr( $route ) . \'">'
count = s.count(old)
if count != 1:
    raise SystemExit(f'Expected exactly one malformed route_key field, found {count}')
s = s.replace(old, new, 1)
p.write_text(s, encoding='utf-8')
print('Round 03 route attribution correction applied.')
