:: Setze Arbeitsverzeichniss
set _DEVROOT=%~dp0
:: Setze TMP Name
set _NAMETMP=%_DEVROOT:~0,-1%
:: Nutze Letzten Ordner Als Name
for %%f in (%_NAMETMP%) do set _NAME=%%~nxf

:: Lösche Alte Datei
del "%_DEVROOT%%_NAME%.tar"

:: 7Zip Workspace
cd "C:\Program Files\7-Zip"

:: Odner Inhalt packen
IF EXIST %_DEVROOT%acpTemplates\ 7z a -ttar -mx9 -r0 "%_DEVROOT%acpTemplates.tar"  "%_DEVROOT%acpTemplates\*"
IF EXIST %_DEVROOT%acptemplates\ 7z a -ttar -mx9 -r0 "%_DEVROOT%acptemplates.tar"  "%_DEVROOT%acptemplates\*"
IF EXIST %_DEVROOT%acpTemplate\ 7z a -ttar -mx9 -r0 "%_DEVROOT%acpTemplate.tar"  "%_DEVROOT%acpTemplate\*"
IF EXIST %_DEVROOT%files\ 7z a -ttar -mx9 -r0 "%_DEVROOT%files.tar"  "%_DEVROOT%files\*"
IF EXIST %_DEVROOT%templates\ 7z a -ttar -mx9 -r0 "%_DEVROOT%templates.tar"  "%_DEVROOT%templates\*"
IF EXIST %_DEVROOT%template\ 7z a -ttar -mx9 -r0 "%_DEVROOT%template.tar"  "%_DEVROOT%template\*"

:: Combiniere Archive
7z u -ttar -mx9 -r0 -xr!create.bat -xr!update.bat -xr!.git -xr!.project -xr!.gitignore -xr!LICENSE -xr!README.md -xr!acptemplates -xr!acpTemplates -xr!acpTemplate -xr!files -xr!templates -xr!template "%_DEVROOT%%_NAME%.tar"  "%_DEVROOT%*"

:: Lösche Tmp Archive
IF EXIST %_DEVROOT%acpTemplates.tar del "%_DEVROOT%acpTemplates.tar"
IF EXIST %_DEVROOT%acptemplates.tar del "%_DEVROOT%acptemplates.tar"
IF EXIST %_DEVROOT%acpTemplate.tar del "%_DEVROOT%acpTemplate.tar"
IF EXIST %_DEVROOT%files.tar del "%_DEVROOT%files.tar"
IF EXIST %_DEVROOT%templates.tar del "%_DEVROOT%templates.tar"
IF EXIST %_DEVROOT%template.tar del "%_DEVROOT%template.tar"