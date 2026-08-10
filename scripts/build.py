#!/usr/bin/env python3
"""Deterministic, path-safe File 14 release builder."""
from __future__ import annotations
import hashlib, json, os, re, shutil, stat, zipfile
from pathlib import Path, PurePosixPath
ROOT = Path(__file__).resolve().parents[1]
PLUGIN = ROOT / "14-global-clinic-usp-integration"
DIST = ROOT / "dist"
MAIN = PLUGIN / "global-clinic-usp-integration.php"
FIXED_TIME = (2026, 1, 1, 0, 0, 0)
def sha256_bytes(data: bytes) -> str: return hashlib.sha256(data).hexdigest()
def parse_version() -> str:
    text = MAIN.read_text(encoding="utf-8")
    m = re.search(r"^\s*\*\s*Version:\s*([0-9]+(?:\.[0-9]+){2})\s*$", text, re.M)
    if not m: raise SystemExit("Plugin Version header not found")
    version = m.group(1)
    c = re.search(r"define\(\s*'GCU_VERSION'\s*,\s*'([^']+)'\s*\)", text)
    if not c or c.group(1) != version: raise SystemExit("Plugin header and GCU_VERSION drift")
    return version
def source_files() -> list[Path]:
    files=[]
    for path in sorted(PLUGIN.rglob("*")):
        if path.is_symlink(): raise SystemExit(f"Symlink forbidden in package: {path}")
        if not path.is_file(): continue
        rel=path.relative_to(ROOT).as_posix(); pure=PurePosixPath(rel)
        if pure.is_absolute() or ".." in pure.parts: raise SystemExit(f"Unsafe path: {rel}")
        if any(part in {"node_modules","vendor",".git","tests"} for part in pure.parts): continue
        files.append(path)
    if not files: raise SystemExit("No package files found")
    return files
def build_zip(output: Path, files: list[Path]):
    sbom=[]
    with zipfile.ZipFile(output,"w",compression=zipfile.ZIP_DEFLATED,compresslevel=9) as z:
        for path in files:
            rel=path.relative_to(ROOT).as_posix(); data=path.read_bytes()
            info=zipfile.ZipInfo(rel,FIXED_TIME); info.compress_type=zipfile.ZIP_DEFLATED
            mode=0o755 if os.access(path,os.X_OK) else 0o644
            info.external_attr=(stat.S_IFREG|mode)<<16; info.create_system=3; info.flag_bits|=0x800
            z.writestr(info,data,compress_type=zipfile.ZIP_DEFLATED,compresslevel=9)
            sbom.append({"path":rel.split("/",1)[1],"sha256":sha256_bytes(data),"bytes":len(data)})
    return sbom
def validate_zip(path: Path, expected_files: list[Path]):
    expected=[p.relative_to(ROOT).as_posix() for p in expected_files]
    with zipfile.ZipFile(path,"r") as z:
        names=z.namelist()
        if names != expected: raise SystemExit("ZIP file order/content drift")
        for name in names:
            pure=PurePosixPath(name)
            if pure.is_absolute() or ".." in pure.parts or not name.startswith("14-global-clinic-usp-integration/"): raise SystemExit(f"Unsafe archive path: {name}")
        bad=z.testzip()
        if bad: raise SystemExit(f"ZIP CRC failure: {bad}")
def main():
    version=parse_version(); files=source_files(); shutil.rmtree(DIST,ignore_errors=True); DIST.mkdir(parents=True,exist_ok=True)
    a=DIST/f"14-global-clinic-usp-integration-{version}.build-a.zip"; b=DIST/f"14-global-clinic-usp-integration-{version}.build-b.zip"
    sa=build_zip(a,files); sb=build_zip(b,files); validate_zip(a,files); validate_zip(b,files)
    if a.read_bytes()!=b.read_bytes() or sa!=sb: raise SystemExit("Deterministic double-build mismatch")
    final=DIST/f"14-global-clinic-usp-integration-{version}.zip"; shutil.copyfile(a,final); digest=sha256_bytes(final.read_bytes())
    (DIST/f"14-global-clinic-usp-integration-{version}.zip.sha256").write_text(f"{digest}  {final.name}\n",encoding="utf-8")
    sbom={"name":"14-global-clinic-usp-integration","version":version,"format":"file-sha256-sbom-v1","package_sha256":digest,"file_count":len(sa),"files":sa}
    (DIST/f"14-global-clinic-usp-integration-{version}.sbom.json").write_text(json.dumps(sbom,ensure_ascii=False,sort_keys=True,indent=2)+"\n",encoding="utf-8")
    a.unlink(); b.unlink()
    print(f"Built {final.name}"); print(f"SHA-256 {digest}"); print(f"Files {len(sa)}"); print("Deterministic double-build: PASS"); print("Archive path/CRC validation: PASS")
if __name__ == "__main__": main()
