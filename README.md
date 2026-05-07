# How to deploy

## Create a docker network

```
docker network create mynet
```

## Run the container

```
docker run --name {image_name} -p 8010:80 -d lucienozandry/{image_name}:latest
```

or 

```
docker run --network mynet --name maboo_api -p 8000:80 -e APP_URL=https://maboo.mg -v /etc/docker/api/master/storage:/var/www/html/storage -v /etc/docker/api/master/.env:/var/www/html/.env -d lucienozandry/maboo-api:latest
```

## Run typesense container

```
  services:
  typesense:
    image: typesense/typesense:30.1
    restart: on-failure
    ports:
      - "8108:8108"
    volumes:
      - ./typesense-data:/data
    command: '--data-dir /data --api-key=xyz --enable-cors'
  networks:
    mynet:
      external: true
```

## Run Redis

```
docker run -d --network mynet --name redis -p 6379:6379 redis
```