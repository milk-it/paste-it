<?php
/**
 * $Project: Pastebin $
 * $Id: default.conf.php,v 1.3 2006/04/27 16:19:24 paul Exp $
 * 
 * Pastebin Collaboration Tool
 * http://pastebin.com/
 *
 * This file copyright (C) 2005 Paul Dixon (paul@elphin.com)
 * 
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
 
/**
* This is the main configuration file containing typical defaults. 
* 
* For ease of upgrading, DO NOT MODIFY THIS FILE!
* 
* Create an override file with a name matching your domain or element of
* of it. For example for the domain 'banjo.pastebin.com', the code will
* attempt to include these config files in order
* 
* default.conf.php
* com.conf.php
* pastebin.com.conf.php
* banjo.pastebin.com.conf.php 
*
* The purpose of this to allow you to specific global options lower down,
* say in com.conf.php, but domain-specific overrides in higher up files like
* banjo.pastebin.com.conf.php
*/



/**
* Site title
*/
$CONF['title']='Paste-it - ferramenta de debug colaborativo';

/**
* Email address feedback should be sent to
*/
$CONF['feedback_to']='lordelph@gmail.com';

/**
* Apparent sender address for feedback email
*/
$CONF['feedback_sender']='pastebin.com <no-reply@pastebin.com>';

/**
* database type - only mysql supported at present
*/
$CONF['dbsystem']='mysql';

/**
* db credentials
*/
$CONF['dbhost']='localhost';
$CONF['dbname']='paste';
$CONF['dbuser']='root';
$CONF['dbpass']='';

/**
 * format of urls to pastebin entries - %d is the placeholder for
 * the entry id. 
 * 
 * 1. for shortest possible url generation in conjuction with mod_rewrite:
 *    $CONF['url_format']='/%d';
 * 
 * 2. if using pastebin with mod_rewrite, but within a subdir, you'd use
 *    something like this:
 *    $CONF['url_format']="/mysubdir/%d";
 * 
 * 3. if not using mod_rewrite, you'll need something more like this:
 *    $CONF['url_format']="/pastebin.php?show=%d";
 */
$CONF['url_format']='/%d';

/**
* default expiry time d (day) m (month) or f (forever)
*/
$CONF['default_expiry']='m';

/**
* this is the path to the script - you may want to
* to use / for even shorter urls if the main script
* is renamed to index.php
*/
$CONF['this_script']='/pastebin.php';

/**
* what's the maximum number of posts we want to keep?
* Set this to 0 to have no limit on retained posts
*/
$CONF['max_posts']=0;

/**
* what's the highlight char?
*/
$CONF['highlight_prefix']='@@';

/**
* how many elements in the base domain name? This is used to determine
* what makes a "private" pastebin, i.e. for pastebin.com there are 2
* elements 'pastebin' and 'com' - for pastebin.mysite.com there 3. Got it?
* Good!
*/
$CONF['base_domain_elements']=2;

/**
* default syntax highlighter
*/
$CONF['default_highlighter']='text';

/**
* available formats
*/
$CONF['all_syntax']=array(
        'prado' => 'PRADO Template',
	'actionscript'=>'ActionScript',
	'ada'=>'Ada',
	'apache'=>'Apache Log File',
	'applescript'=>'AppleScript',
	'asm'=>'ASM (NASM based)',
	'asp'=>'ASP',
	'bash'=>'Bash',
	'c'=>'C',
	'c_mac'=>'C for Macs',
	'caddcl'=>'CAD DCL',
	'cadlisp'=>'CAD Lisp',
	'cpp'=>'C++',
	'csharp'=>'C#',
	'cfm'=>'ColdFusion',
	'css'=>'CSS',
	'd'=>'D',
	'delphi'=>'Delphi',
	'diff'=>'Diff',
	'dos'=>'DOS',
	'eiffel'=>'Eiffel',
	'fortran'=>'Fortran',
	'freebasic'=>'FreeBasic',
	'gml'=>'Game Maker',
	'html4strict'=>'HTML',
	'ini'=>'INI file',
	'java'=>'Java',
	'javascript'=>'Javascript',
	'lisp'=>'Lisp',
	'lua'=>'Lua',
	'matlab'=>'MatLab',
	'mpasm'=>'MPASM',
	'mysql'=>'MySQL',
	'nsis'=>'NullSoft Installer',
	'objc'=>'Objective C',
	'ocaml'=>'OCaml',
	'oobas'=>'Openoffice.org BASIC',
	'oracle8'=>'Oracle 8',
	'pascal'=>'Pascal',
	'perl'=>'Perl',
	'php'=>'PHP',
	'python'=>'Python',
	'qbasic'=>'QBasic/QuickBASIC',
	'robots'=>'Robots',
	'ruby'=>'Ruby',
	'scheme'=>'Scheme',
	'smarty'=>'Smarty',
	'sql'=>'SQL',
	'tcl'=>'TCL',
	'vb'=>'VisualBasic',
	'vbnet'=>'VB.NET',
	'visualfoxpro'=>'VisualFoxPro',
	'xml'=>'XML',

);

/**
* popular formats, listed first
*/
$CONF['popular_syntax']=array(
	'bash', 'c', 'cpp', 'html4strict',
	'java','javascript','php','perl', 'python', 'ruby', 'lua');

?>
