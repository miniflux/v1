#!/bin/sh

VERSION=$1
APP="miniflux"

cd /tmp
rm -rf /tmp/$APP /tmp/$APP-*.zip 2>/dev/null

git clone --depth 1 https://github.com/fguillot/$APP.git

rm -rf $APP/data/*.sqlite \
       $APP/.git \
       $APP/scripts \
       $APP/Dockerfile \
       $APP/vendor/composer/installed.json

find $APP -name docs -type d -exec rm -rf {} +;
find $APP -name tests -type d -exec rm -rf {} +;

find $APP -name composer.json -delete
find $APP -name phpunit.xml -delete
find $APP -name .travis.yml -delete
find $APP -name README.* -delete
find $APP -name .gitignore -delete
find $APP -name *.less -delete
find $APP -name *.scss -delete
find $APP -name *.rb -delete

sed -i.bak s/master/$VERSION/g $APP/common.php && rm -f $APP/*.bak

zip -r $APP-$VERSION.zip $APP
mv $APP-*.zip ~/Devel/websites/$APP

cd ~/Devel/websites/$APP/
unlink $APP-latest.zip
ln -s $APP-$VERSION.zip $APP-latest.zip

rm -rf /tmp/$APP 2>/dev/null
