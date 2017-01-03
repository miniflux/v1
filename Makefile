.PHONY: archive
.PHONY: docker-image
.PHONY: docker-push
.PHONY: docker-destroy
.PHONY: docker-run
.PHONY: js
.PHONY: unit-test-sqlite
.PHONY: unit-test-postgres
.PHONY: sync-locales
.PHONY: find-locales

JS_FILE = assets/js/app.min.js
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

$(JS_FILE): assets/js/src/app.js \
	assets/js/src/feed.js \
	assets/js/src/item.js \
	assets/js/src/event.js \
	assets/js/src/nav.js
	@ yarn install || npm install
	@ ./node_modules/.bin/jshint assets/js/src/*.js
	@ cat $^ | node_modules/.bin/uglifyjs - > $@
	@ echo "Miniflux.App.Run();" >> $@

# Build a new archive: make archive version=1.2.3 dst=/tmp
archive:
	@ git archive --format=zip --prefix=miniflux/ v${version} -o ${dst}/miniflux-${version}.zip

functional-test-sqlite:
	@ rm -f data/db.sqlite
	@ ./vendor/bin/phpunit -c tests/phpunit.functional.sqlite.xml

unit-test-sqlite:
	@ ./vendor/bin/phpunit -c tests/phpunit.unit.sqlite.xml

unit-test-postgres:
	@ ./vendor/bin/phpunit -c tests/phpunit.unit.postgres.xml

sync-locales:
	@ php scripts/sync-locales.php

find-locales:
	@ php scripts/find-locales.php
