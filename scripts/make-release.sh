#!/bin/sh

if [ $# -eq 0 ]; then
  echo 1>&2 "Usage: $0 <version> [destination]"
  exit 2
fi

VERSION=$1
DESTINATION=$2
APP="miniflux"

if [ -z "$2" ]
then
    DESTINATION=~/Devel/websites/$APP
fi

# tag the release
git tag -a v$VERSION -m "Version $VERSION" && git push origin v$VERSION || exit 2

# create the archive 
git archive --format=zip --prefix=$APP/ v$VERSION -o $DESTINATION/$APP-$VERSION.zip

cd $DESTINATION

if [ -L $APP-latest.zip ]
then
    unlink $APP-latest.zip
    ln -s $APP-$VERSION.zip $APP-latest.zip
fi
