#/bin/sh
################################## 
#  .uninstall.sh
#
# ABSTRACT: PlugMedia QPKG removal
#
# HISTORY:
# 	2009/08/01	Written by Laurent (Ad'Novea)
# 	2008/01/16	find_base fnt by AndyChuo (QNAP)
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
################################## 
#
################################## 
# UTILS
################################## 
	CMD_CUT="/bin/cut"
	CMD_ECHO="/bin/echo"
	CMD_GETCFG="/sbin/getcfg"; 
	CMD_RM="/bin/rm"
	CMD_SED="/bin/sed"
#
################################## 
# SYSTEM
################################## 
#
	# QPKG management
	QPKG_INSTALL_MSG=""
	QPKG_BASE=""
	QPKG_DIR=""
	SYS_INIT_DIR="/etc/init.d"
	SYS_rcS_DIR="/etc/rcS.d/"
	SYS_rcK_DIR="/etc/rcK.d/"
	QPKG_RC_NUM="101" #for rcS and rcK
	QPKG_SERVICE_PROGRAM=""
#
################################## 
# QPKG
################################## 
	QPKG_NAME="plugmedia"
#
################################## 
# Determine BASE installation location  
################################## 
# 
	find_base() {
	# Determine BASE installation location according to smb.conf
		publicdir="`${CMD_GETCFG} Public path -f /etc/config/smb.conf`"
		if [ ! -z $publicdir ] && [ -d $publicdir ];then
			publicdirp1="`${CMD_ECHO} $publicdir | ${CMD_CUT} -d "/" -f 2`"
			publicdirp2="`${CMD_ECHO} $publicdir | ${CMD_CUT} -d "/" -f 3`"
			publicdirp3="`${CMD_ECHO} $publicdir | ${CMD_CUT} -d "/" -f 4`"
			if [ ! -z $publicdirp1 ] && [ ! -z $publicdirp2 ] && [ ! -z $publicdirp3 ]; then
				[ -d "/${publicdirp1}/${publicdirp2}/Public" ] && QPKG_BASE="/${publicdirp1}/${publicdirp2}"
			fi
		fi
		
	# Determine BASE installation location by checking where the Public folder is.
		if [ -z $QPKG_BASE ]; then
			for datadirtest in /share/HDA_DATA /share/HDB_DATA /share/HDC_DATA /share/HDD_DATA /share/MD0_DATA /share/MD1_DATA; do
				[ -d $datadirtest/Public ] && QPKG_BASE="/${publicdirp1}/${publicdirp2}"
			done
		fi
		if [ -z $QPKG_BASE ] ; then 
			QPKG_INSTALL_MSG="The Public share not found."
			$CMD_ECHO $QPKG_INSTALL_MSG;  _exit 1;  
		fi
		QPKG_INSTALL_PATH="${QPKG_BASE}/.qpkg" 
		QPKG_DIR="${QPKG_INSTALL_PATH}/${QPKG_NAME}" 
	} 
#
################################## 
# MAIN
################################## 
#
	find_base 
	# Remove symbolic link and CGI file
	$CMD_RM -Rf /share/Qweb/$QPKG_NAME
	$CMD_RM -Rf /home/httpd/cgi-bin/plugmedia
	
exit $RETVAL
