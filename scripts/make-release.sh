#!/bin/sh

if [ $# -eq 0 ]; then
  echo 1>&2 "$0: version number missing"
  exit 2
fi

VERSION=$1
APP="miniflux"

# tag the release
git tag -a v$VERSION -m "Version $VERSION" && git push origin v$VERSION || exit 2

# create the archive 
git archive v$VERSION -o ~/Devel/websites/$APP/$APP-$VERSION.zip

cd ~/Devel/websites/$APP/
unlink $APP-latest.zip
ln -s $APP-$VERSION.zip $APP-latest.zip