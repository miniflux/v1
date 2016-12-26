.PHONY: archive
.PHONY: docker-image
.PHONY: docker-push
.PHONY: docker-destroy
.PHONY: docker-run
.PHONY: js
.PHONY: unit-test-sqlite
.PHONY: unit-test-postgres

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

js: $(JS_FILE)

$(JS_FILE): assets/js/app.js \
    assets/js/feed.js \
    assets/js/item.js \
    assets/js/event.js \
    assets/js/nav.js
	@ echo "/* AUTO GENERATED FILE, DO NOT MODIFY THIS FILE, USE 'make js' */" > $@
	@ cat $^ >> $@
	@ echo "Miniflux.App.Run();" >> $@

# Build a new archive: make archive version=1.2.3 dst=/tmp
archive:
	@ git archive --format=zip --prefix=miniflux/ v${version} -o ${dst}/miniflux-${version}.zip

unit-test-sqlite:
	@ ./vendor/bin/phpunit -c tests/phpunit.unit.sqlite.xml

unit-test-postgres:
	@ ./vendor/bin/phpunit -c tests/phpunit.unit.postgres.xml

