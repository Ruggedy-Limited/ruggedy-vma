copy docker-files\.env .\
docker-compose up -d
echo "Waiting for containers to initialise..."
docker-files\sleep.bat 10
docker exec -t ruggedy-vma /usr/share/nginx/html/ruggedy-vma/docker-files/post-docker-compose-up.sh
