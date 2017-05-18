#!/usr/bin/env bash
docker-compose up -d
docker exec ruggedy-vma /usr/share/nginx/html/ruggedy-vma/docker-files/post-docker-compose-up.sh
