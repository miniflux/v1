Run Miniflux with Docker
========================

Build your own image
--------------------

```bash
docker build -t <yourname/imagename> .
```

Run container from the image
----------------------------

```bash
docker run -p 80:80 --name miniflux <yourname/imagename>
```

Enjoy!
