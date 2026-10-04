@echo off
title PetGym Cloudflare Online Server
echo ========================================================
echo   MENJALANKAN SERVER PETGYM ^& CLOUDFLARE TUNNEL
echo   Domain Utama: https://petgym.website
echo   Subdomain Gym: https://*.petgym.website
echo ========================================================
echo.
start "Laravel Server (Port 8000)" cmd /k "cd /d c:\laragon\www\petgym && php artisan serve --port=8000"
timeout /t 2 /nobreak >nul
start "Cloudflare Tunnel" cmd /k "C:\laragon\bin\cloudflared\cloudflared.exe tunnel run petgym"
echo.
echo Server dan Cloudflare Tunnel sedang berjalan!
echo Website Anda sekarang ONLINE di: https://petgym.website
echo (Jangan tutup jendela terminal hitam yang muncul agar web tetap online).
echo.
pause
