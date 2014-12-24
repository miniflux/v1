#!/bin/sh

in=assets/js/all.js
out=assets/js/all.min.js

rm -f $in 2>/dev/null
rm -f $out 2>/dev/null

cat assets/js/app.js assets/js/feed.js assets/js/item.js assets/js/event.js assets/js/nav.js > $in
echo "Miniflux.App.Run();" >> $in

curl -s \
-d compilation_level=SIMPLE_OPTIMIZATIONS \
-d output_format=text \
-d output_info=compiled_code \
--data-urlencode "js_code@${in}" \
http://closure-compiler.appspot.com/compile > $out

rm -f $in
