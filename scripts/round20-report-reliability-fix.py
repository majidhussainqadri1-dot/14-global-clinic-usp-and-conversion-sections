from pathlib import Path

path = Path('14-global-clinic-usp-integration/includes/class-gcu-future-intelligence.php')
text = path.read_text()
print(len(text))
