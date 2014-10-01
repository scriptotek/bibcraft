# Oppsett for Windows

Python og Git:

- Last ned og installer [Python 2.x](https://www.python.org/ftp/python/2.7.8/python-2.7.8.msi). Husk å krysse av for at Python skal legges til i PATH
- Last ned https://bootstrap.pypa.io/ez_setup.py , åpne et vanlig shell (Cmd eller PowerShell) og kjør `python ez_setup.py`.
  Dette vil installere `easy_install` i C:\Python27\Scripts , så vi må legge til den mappa i PATH også (gjøres manuelt på vanlig måte)
- Last ned og installer [Git](http://git-scm.com/download/win) på vanlig måte, rett frem.
- Åpne et nytt shell og skriv `easy_install websocket-client pyyaml pyserial termcolor`

Bibcraft:

- Lag mappen "Bibcraft" på C: (eller hvor-som-helst)
- Åpne et shell, skriv
- `cd C:\Bibcraft`
- `git clone https://github.com/scriptotek/pyrfidgeek`
- `cd pyrfidgeek`
- `copy .\config-dist.yml .\config.yml`
- `notepad config.yml` og sett riktig port. Formen `port: 'COM4'` er for Windows,
  mens formen `port: '/dev/tty.SLAB_USBtoUART'` er for Linux/Mac, slett eller kommenter ut den som ikke brukes.
- `python wsclient.py`
