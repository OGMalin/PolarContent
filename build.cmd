REM 7z.exe a commandline compression app from 7-zip
del polarcontent.zip
..\7z a -Y polartemplate.zip *.php
..\7z a -Y polartemplate.zip *.xml
..\7z a -Y polartemplate.zip media

