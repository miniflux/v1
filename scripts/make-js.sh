#!/bin/sh

in=assets/js/all.js
out=assets/js/all.min.js

rm -f $in 2>/dev/null
rm -f $out 2>/dev/null

cat assets/js/app.js assets/js/feed.js assets/js/item.js assets/js/event.js assets/js/nav.js > $in
echo "Miniflux.App.Run();" >> $in

output=$(curl -s \
    -d compilation_level=ADVANCED_OPTIMIZATIONS \
    -d output_format=text \
    -d output_info=warnings \
    -d output_info=errors \
    -d warning_level=verbose \
    -d output_file_name=all.min.js \
    --data-urlencode "js_code@${in}" \
    https://closure-compiler.appspot.com/compile)

if [ $(echo "$output" | wc -l) -gt 1 ]; then
  echo -e "NOTHING DONE. There are issues:\n"
  echo "$output"
  exit 1
fi

curl -s "https://closure-compiler.appspot.com/$output" -o "$out";

rm -f $in
