#!/bin/sh
# Einfaches Beispiel
echo convertiere auf maximale Kantenlänge von 300px
for i in *.jpg ; do convert "$i" -resize 300x300 "$i" ; done
