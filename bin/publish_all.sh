#!/bin/bash

find static/[0-9]* -type d | cut -d "/" -f 2 | sort -r | while read file; do bash bin/publish.sh $file 1; done
