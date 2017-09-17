@echo off
TITLE DarkSystem
cd /d %~dp0
netstat -o -n -a | findstr 0.0.0.0:19132 > NUL
if %ERRORLEVEL% equ 0 (
    goto :loop
) else (
    taskkill /f /t /im mintty.exe


    goto :StartDS
)
:loop
ping 127.0.0.1 -n 5 > NUL
taskkill mintty.exe
netstat -o -n -a | findstr 0.0:19132 > NUL
if %ERRORLEVEL% equ 0 (
    goto :loop
) else (
    PING 127.0.0.1 -n 5 > NUL
    taskkill /f /t /im mintty.exe
    goto :StartDS
)
:StartDS
if exist bin\php\php.exe (
        taskkill /f /t /im mintty.exe
	set PHPRC=""
	set PHP_BINARY=bin\php\php.exe
) else (
	set PHP_BINARY=php
)

if exist PocketMine-MP.phar (
	set POCKETMINE_FILE=PocketMine-MP.phar
) else (
	if exist src\pocketmine\PocketMine.php (
		set POCKETMINE_FILE=src\pocketmine\PocketMine.php
	) else (
		echo "DarkSystem Bulunamadı!"
		pause
		exit 1
	)
)
if exist bin\php\php_wxwidgets.dll (
    %PHP_BINARY% %POCKETMINE_FILE% --enable-gui %*
) else (
    if exist bin\mintty.exe (
        start "" bin\mintty.exe -o Columns=88 -o Rows=32 -o AllowBlinking=0 -o FontQuality=3 -o Font="Lucida Console" -o FontHeight=13 -o CursorType=0 -o CursorBlinks=1 -h error -t "DarkSystem" -i bin/darksystem.ico -w max %PHP_BINARY% %POCKETMINE_FILE% --enable-ansi %*
    ) else (
        %PHP_BINARY% -c bin\php %POCKETMINE_FILE% %*
    )
)
goto :loop