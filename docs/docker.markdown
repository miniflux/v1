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

Build your own image
--------------------

```bash
docker build -t <yourname/imagename> .
```

Run container from the image:

```bash
docker run -p 80:80 --name miniflux <yourname/imagename>
```

You can also mount the volume `/var/www/html/data` to save the data on the host machine (this folder must have write access from the container web server user).
