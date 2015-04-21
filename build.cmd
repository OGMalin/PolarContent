REM 7z.exe a commandline compression app from 7-zip
del polarcontent.zip
..\7z a -Y polarcontent.zip *.php
..\7z a -Y polarcontent.zip *.xml
..\7z a -Y polarcontent.zip helpers
..\7z a -Y polarcontent.zip language
..\7z a -Y polarcontent.zip polarcontent
