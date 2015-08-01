Usage
====================

To rename module to my.module can use the following command:

$ find . -name "*.php" | xargs perl -w -i -p -e "s/simple([_\.]+)module/my\$1module/g"
$ for file in *.php ; do mv $file ${file//simple_module/my_module} ; done
