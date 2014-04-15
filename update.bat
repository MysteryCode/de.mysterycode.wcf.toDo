:: Setze Arbeitsverzeichniss
set _DEVROOT=%~dp0
:: Setze TMP Name
set _NAMETMP=%_DEVROOT:~0,-1%
:: Nutze Letzten Ordner Als Name
for %%f in (%_NAMETMP%) do set _NAME=%%~nxf

IF EXIST %_DEVROOT%files XCOPY /y /S %_DEVROOT%files C:\localhost\htdocs\test\wcf
IF EXIST %_DEVROOT%templates XCOPY /y /S %_DEVROOT%templates C:\localhost\htdocs\test\wcf\templates
IF EXIST %_DEVROOT%template XCOPY /y /S %_DEVROOT%template C:\localhost\htdocs\test\wcf\templates