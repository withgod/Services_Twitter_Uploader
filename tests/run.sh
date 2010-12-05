#!/bin/sh

#export SOA_PROXY_TEST=1
#export SOA_PROXY_HOST=127.0.0.1
#export SOA_PROXY_PORT=3128

mkdir -p ./reports/coverage
phpunit
