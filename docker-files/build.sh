#!/usr/bin/env bash
cp docker-files/.env ./
docker-compose up -d
echo "Waiting for containers to initialise..."
sleep 10
docker exec -t ruggedy-vma /usr/share/nginx/html/ruggedy-vma/docker-files/post-docker-compose-up.sh
