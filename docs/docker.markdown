Run Miniflux with Docker
========================

Miniflux can run easily with [Docker](https://www.docker.com).
There is a `Dockerfile` in the repository to build your own container.

Use the automated build
-----------------------

Every new commit on the repository trigger a new build on [Docker Hub](https://registry.hub.docker.com/u/miniflux/miniflux/).

```bash
docker run -d --name miniflux -p 80:80 -t miniflux/miniflux:latest
```

The tag **latest** is the **development version** of Miniflux, use at your own risk.

Use Docker Compose
------------------

The Git repository contains a `docker-compose.yml` file, so you can run easily Miniflux:

```bash
docker-compose up -d
```

- By default, the service listen on port 80
- A named volume is created to store your data on the host machine

Build your own image
--------------------

```bash
docker build -t <yourname/imagename> .
```

Run container from the image:

```bash
docker run -p 80:80 --name miniflux <yourname/imagename>
```
