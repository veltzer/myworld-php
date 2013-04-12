#!/bin/bash

source scripts/defs.shinc

find ${BLOGDIR} -not -user root -or -not -group root
