phony:
	echo "No target specified"

buildAll: src Model Services aMess

src: buildDir srcBuild
	zip -r build/src/Source.zip src

srcBuild:
	mkdir -p build/src

Model: buildDir modelBuild
	zip -r build/model/Model.zip src/SlartyBartfast/Model

modelBuild:
	mkdir -p build/model

Services: buildDir servicesBuild
	zip -r build/services/Services.zip src/SlartyBartfast/Services

servicesBuild:
	mkdir -p build/services

buildDir:
	mkdir -p build

aMess: buildDir messBuild
	echo '<?php $$i = 0; while($$i < 100000) { echo time(); $$i++; }' > build/time.php
	php build/time.php > build/timeoutput.txt
	zip -r build/mess/StupidTime.zip build/timeoutput.txt build/time.php
	rm build/timeoutput.txt build/time.php

messBuild:
	mkdir -p build/mess

clean:
	rm -rf build

