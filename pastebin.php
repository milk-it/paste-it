<?php
/**
 * $Project: Pastebin $
 * $Id: pastebin.php,v 1.3 2006/04/27 16:21:10 paul Exp $
 * 
 * Pastebin Collaboration Tool
 * http://pastebin.com/
 *
 * This file copyright (C) 2006 Paul Dixon (paul@elphin.com)
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
 
 
///////////////////////////////////////////////////////////////////////////////
// includes
//
require_once('lib/pastebin/config.inc.php');
require_once('lib/geshi/geshi.php');
require_once('lib/pastebin/diff.class.php');
require_once('lib/pastebin/pastebin.class.php');

///////////////////////////////////////////////////////////////////////////////
// magic quotes are anything but magic - lose them!
//
if (get_magic_quotes_gpc())
{
	function callback_stripslashes(&$val, $name) 
	{
		if (get_magic_quotes_gpc()) 
			$val=stripslashes($val);
	}


	if (count($_GET))
		array_walk ($_GET, 'callback_stripslashes');
	if (count($_POST))
		array_walk ($_POST, 'callback_stripslashes');
	if (count($_COOKIE))
		array_walk ($_COOKIE, 'callback_stripslashes');
}

///////////////////////////////////////////////////////////////////////////////
// user submitted the "private pastebin" form? redirect them...
//
if ($_GET['goprivate'])
{
	$sub=trim(strtolower($_GET['goprivate']));
	if (preg_match('/^[a-z0-9][a-z0-9\.\-]*[a-z0-9]$/i', $sub))
	{
		header("Location: http://{$sub}.pastebin.com");
		exit;
	}
}

///////////////////////////////////////////////////////////////////////////////
// create our pastebin object
//
$pastebin=new Pastebin($CONF);


// clean up older posts 
$pastebin->doGarbageCollection();


///////////////////////////////////////////////////////////////////////////////
// process new posting
//
$errors=array();
if (isset($_POST['paste']))
{
	//process posting and redirect
	$id=$pastebin->doPost($_POST);
	if ($id)
	{
		$pastebin->redirectToPost($id);
		exit;
	}

}


///////////////////////////////////////////////////////////////////////////////
// process download
//
if (isset($_GET['dl'])) 
{
	$pid=intval($_GET['dl']);
	
	if (!$pastebin->doDownload($pid))
	{
		//not fount
		echo "Pastebin entry $pid is not available";
	}
	exit;
}

	

///////////////////////////////////////////////////////////////////////////////
// if we get this far, we're going to be displaying some HTML, so let's kick
// off here...
$page=array();

//figure out some nice defaults
$page['current_format']=$CONF['default_highlighter'];
$page['expiry']=$CONF['default_expiry'];
$page['remember']='';	

//see if we can come up with a better default using the subdomain
if (strlen($CONF['subdomain']) && isset($CONF['all_syntax'][$CONF['subdomain']]))
{
	//cool, domain is something like ruby.pastebin.com, so lets go with that
	//as a default
	$page['current_format']=$CONF['subdomain'];
}

//are we remembering the user?
$cookie=$pastebin->extractCookie();
if ($cookie)
{
	//initialise bits of page with cookie data
	$page['remember']='checked="checked"';
	$page['current_format']=$cookie['last_format'];
	$page['poster']=$cookie['poster'];
	$page['expiry']=$cookie['last_expiry'];
}


//add list of recent posts
$list=isset($_REQUEST["list"]) ? intval($_REQUEST["list"]) : 10;
$page['recent']=$pastebin->getRecentPosts($list);

//send feedback mail?
if (isset($_POST['feedback']) && strlen($_POST['msg']))
{
	$matches=array();
	$spam=false;
	
	//more than two links?
	preg_match_all('{http://}', $_POST['msg'], $matches);
	$spam=$spam || count($matches[0])>2;
	
	//[url=][/url] ?
	$spam=$spam || preg_match('{\[url=}i', $_POST['msg']);
	$spam=$spam || preg_match('{<a href=}i', $_POST['msg']);
	
	
	if (!$spam)
	{
		@mail($CONF['feedback_to'], "[pastebin] Feedback", $_POST['msg'], "From: {$CONF['feedback_sender']}");
		$page['thankyou']="Thanks for your feedback, if you included an email address in your message, we'll get back to you asap.";
	}
	else
	{
		$page['thankyou']="Sorry, that looked a bit too much like spam - go easy on the links there.";
	}
}



///////////////////////////////////////////////////////////////////////////////
// show a post
//
if (isset($_REQUEST["show"]))
{
	$pid=intval($_REQUEST['show']);
	
	//get the post
	$page['post']=$pastebin->getPost($pid);
	
	//ensure corrent format is selected
	$page['current_format']=$page['post']['format'];
}
else
{
	$page['posttitle']='New Posting';
}

//use configured title
$page['title']=	$CONF['title'];

//on a subdomain, label it as private...
/*if (strlen($CONF['subdomain']))
{
	$page['title']=$CONF['subdomain']. ' private pastebin - collaborative debugging tool';
}
elseif ($page['current_format']!='text')
{
	//give the page a title which features the syntax used..
	$page['title']=$CONF['all_syntax'][$page['current_format']] . " ".$page['title'];
}*/



///////////////////////////////////////////////////////////////////////////////
// HTML page output
//
include("layout.php");




  


