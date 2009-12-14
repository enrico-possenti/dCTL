#!/bin/bash

#/**
# +----------------------------------------------------------------------+
# | A digital tale (C) 2009 Enrico Possenti :: dCTL                      |
# +----------------------------------------------------------------------+
# | Author:  NoveOPiu di Enrico Possenti <info@noveopiu.com>             |
# | License: Creative Commons License v3.0 (Attr-NonComm-ShareAlike      |
# |          http://creativecommons.org/licenses/by-nc-sa/3.0/           |
# +----------------------------------------------------------------------+
# | An eXist launch   script                                             |
# +----------------------------------------------------------------------+
#*/

SCRIPTPATH=`dirname $0`

MY_TOOLS_HOME="$SCRIPTPATH"
MY_EXIST_HOME="$MY_TOOLS_HOME/exist"
MY_EXIST_PORT="8080"

echo $MY_EXIST_HOME

#
case "$1" in
 start)
  cd $MY_EXIST_HOME
  java -Xmx512M -Djava.endorsed.dirs=lib/endorsed -jar start.jar jetty
	;;

	stop)
  cd $MY_EXIST_HOME
  java -jar start.jar shutdown -u ctl -p sirena -l xmldb:exist://localhost:$MY_EXIST_PORT/exist/xmlrpc/db
	;;

 *)
	 echo "Usage: $0 { start | stop }"
	;;
esac
exit 0
