FROM php:8.2-cli-alpine

# curl extension je v základním image; jen ověříme a nastavíme workdir
WORKDIR /app

# Žádné externí závislosti – čisté PHP-OOP + curl (vestavěné)
COPY src/ /app/src/
COPY public/ /app/public/

EXPOSE 8080
