"""Build a deterministic, allowlisted WordPress installation ZIP; does not compile SCSS."""
from pathlib import Path
import hashlib, re, sys, zipfile

root=Path(__file__).resolve().parents[1]
main=(root/'knk-floating-contact.php').read_text(encoding='utf-8')
version=re.search(r'\* Version: (\d+\.\d+\.\d+)',main).group(1)
assert "define('KNK_FLOATING_CONTACT_VERSION', '"+version+"')" in main
assert 'Stable tag: '+version in (root/'readme.txt').read_text(encoding='utf-8')
if len(sys.argv)>1: assert sys.argv[1]=='v'+version, 'Tag does not match plugin version'
files=['knk-floating-contact.php','uninstall.php','readme.txt','LICENSE']
files+=sorted(p.relative_to(root).as_posix() for d in ('assets','includes') for p in (root/d).rglob('*') if p.is_file())
out=root/'dist';out.mkdir(exist_ok=True)
target=out/f'knk-floating-contact-{version}.zip'
with zipfile.ZipFile(target,'w',zipfile.ZIP_DEFLATED) as z:
    for name in files:
        info=zipfile.ZipInfo('knk-floating-contact/'+name,date_time=(2026,1,1,0,0,0))
        info.compress_type=zipfile.ZIP_DEFLATED
        info.external_attr=0o100644<<16
        z.writestr(info,(root/name).read_bytes().replace(b'\r\n', b'\n'))
with zipfile.ZipFile(target) as z:
    assert z.testzip() is None
    assert z.namelist()==['knk-floating-contact/'+n for n in files]
digest=hashlib.sha256(target.read_bytes()).hexdigest()
(out/'SHA256SUMS').write_text(digest+'  '+target.name+'\n',encoding='ascii')
print(target.name,digest,len(files),'files')
