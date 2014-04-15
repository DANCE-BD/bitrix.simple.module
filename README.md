bitrix.simple.module
====================

Bitrix framework module example

To rename module to my.module can use the following command:

$ find . -name "*.php" | xargs perl -w -i -p -e "s/simple([_\.]+)module/my\$1module/g"
