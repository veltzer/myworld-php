#!/bin/bash

# this script turns pulse audio back on

rm -f ~/.pulse/client.conf
pulseaudio --start
