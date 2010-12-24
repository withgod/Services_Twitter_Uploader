#!/bin/sh

ln -s ./src/Services .
php ./makepackage.php
pear package package.xml
rm ./Services

