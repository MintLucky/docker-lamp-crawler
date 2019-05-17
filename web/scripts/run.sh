#!/bin/bash
sleep 30
cd /app
/usr/local/bin/php -r '$_GET["m"]="GET"; require_once("index.php");'
#/usr/bin/python /scripts/uploader.py
apache2-foreground
