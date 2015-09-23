#!/bin/bash

rm -f .htaccess
for file in `find ./ -type l -name "*.phps"`; do
	rm $file
done
rm -f README

ln -s htaccess .htaccess
for file in `find ./ -type f -name "*.php"`; do
	newname="$file"s
	ln -s `basename $file` $newname
done
ln -s README.md README

chmod 644 README.md
chmod 644 LICENSE

