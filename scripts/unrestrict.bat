@echo off
REM Windows batch wrapper for unrestrict.php
REM Usage: unrestrict.bat [all|ip] [ip_address]

if "%1"=="" (
    php unrestrict.php help
) else if "%1"=="all" (
    php unrestrict.php all
) else if "%1"=="ip" (
    if "%2"=="" (
        echo Error: IP address required
        echo Usage: unrestrict.bat ip 192.168.1.100
    ) else (
        php unrestrict.php ip %2
    )
) else (
    php unrestrict.php %1 %2
)