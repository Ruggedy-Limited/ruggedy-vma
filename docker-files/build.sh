#!/usr/bin/env bash
docker-compose up -d
docker exec ruggedy-vma /usr/share/nginx/html/ruggedy-vma/post-docker-compose-up
