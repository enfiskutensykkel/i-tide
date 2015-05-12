#!/bin/bash

rm -f .htaccess
for file in `find ./ -type l -name "*.phps"`; do
	rm $file
done

ln -s htaccess .htaccess
for file in `find ./ -type f -name "*.php"`; do
	newname="$file"s
	#ln -s $file $newname
done

