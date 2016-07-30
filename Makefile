JS_FILE = assets/js/all.js
CONTAINER = miniflux
IMAGE = miniflux/miniflux
TAG = latest

docker-image:
	@ docker build -t $(IMAGE):$(TAG) .

docker-push:
	@ docker push $(IMAGE)

docker-destroy:
	@ docker rmi $(IMAGE)

docker-run:
	@ docker run --rm --name $(CONTAINER) -P $(IMAGE):$(TAG)

js:
	@ rm -f ${JS_FILE}
	@ echo "/* AUTO GENERATED FILE, DO NOT MODIFY THIS FILE, USE 'make js' */" > ${JS_FILE}
	@ cat assets/js/app.js assets/js/feed.js assets/js/item.js assets/js/event.js assets/js/nav.js >> ${JS_FILE}
	@ echo "Miniflux.App.Run();" >> ${JS_FILE}

# Build a new archive: make archive version=1.2.3 dst=/tmp
archive:
	@ git archive --format=zip --prefix=miniflux/ v${version} -o ${dst}/miniflux-${version}.zip
