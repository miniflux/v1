#!/bin/sh

VERSION=$1
APP="miniflux"

cd /tmp
rm -rf /tmp/$APP /tmp/$APP-*.zip 2>/dev/null

git clone --depth 1 git@github.com:fguillot/$APP.git

rm -rf $APP/data/*.sqlite \
       $APP/.git \
       $APP/.gitignore \
       $APP/scripts \
       $APP/docs \
       $APP/README.*

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
