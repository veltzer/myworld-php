#!/bin/bash

# this script shuts down pulse audio so you can do recording sessions

echo "autospawn = no" > ~/.pulse/client.conf
pulseaudio --kill
