#!/bin/bash

rm -f veltzer.key veltzer.pem
openssl rsa -in privkey.pem -out veltzer.key
openssl x509 -in veltzer.pem.csr -out veltzer.pem -req -signkey veltzer.key -days 2000
sudo scp veltzer.pem /etc/ssl/certs
sudo scp veltzer.key /etc/ssl/private
