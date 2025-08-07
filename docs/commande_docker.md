# methode 1
# Arrêter tous les containers
docker stop $(docker ps -aq)

# Supprimer tous les containers
docker rm -f $(docker ps -aq)

# Supprimer toutes les images
docker rmi -f $(docker images -q)

# Supprimer tous les volumes
docker volume rm $(docker volume ls -q)

# Supprimer tous les réseaux personnalisés
docker network rm $(docker network ls -q | grep -v "bridge\|host\|none")

# verifier que tt est propre 
docker system df



# methode 2
docker system prune -a --volumes --force

# Pour construire

docker compose up --build