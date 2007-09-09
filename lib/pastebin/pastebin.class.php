<?php
/**
 * $Project: Pastebin $
 * $Id: pastebin.class.php,v 1.2 2006/04/27 16:20:52 paul Exp $
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


/**
* Pastebin class models the pastebin data storage without getting involved
* in any UI generation
*/
class Pastebin
{
	var $conf=null;
	var $db=null;
	
	/**
	* Constructor expects a configuration array which should contain
	* the elements documented in config/default.conf.php
	*/
	function Pastebin(&$conf)
	{
		$this->conf=&$conf;
		$this->db=new DB;	
	}
	
	/**
	* Has a 5% probability of cleaning old posts from the database
	*/
	function doGarbageCollection()
	{
		if(rand()%100 < 5)
		{
			//is there a limit on the number of posts
			if ($this->conf['max_posts'])
			{
				$delete_count=$this->db->getPostCount($this->conf['subdomain'])-$this->conf['max_posts'];
				if ($delete_count>0)
				{
					$this->db->trimDomainPosts($this->conf['subdomain'], $delete_count);
				}
			}
			
			//delete expired posts
			$this->db->deleteExpiredPosts();
		}
	}
	
	/**
	* Private method for validating a user-submitted username
	*/
	function _cleanUsername($name)
	{
		return trim(preg_replace('/[^A-Za-z0-9_ \-]/', '',$name));	
	}
	
	/**
	* Private method for validating a user-submitted format code
	*/
	function _cleanFormat($format)
	{
		if (!array_key_exists($format, $this->conf['all_syntax']))
			$format='text';
			
		return $format;	
	}
	
	/**
	* Private method for validating a user-submitted expiry code
	*/
	function _cleanExpiry($expiry)
	{
		if (!preg_match('/^[dmf]$/', $expiry))
			$expiry='d';
			
		return $expiry;
	}
	
	
	/**
	* returns array of cookie info if present, false otherwise
	* all cookie data is cleaned before returning
	*/
	function extractCookie()
	{
		$data=false;
		if (isset($_COOKIE["persistName"]))
		{
			$data=array();
			
			//blow apart the cookie
			list($poster,$last_format,$last_expiry)=explode('#', $_COOKIE["persistName"]);
			
			//clean and validate the cookie inputs
			$data['poster']=$this->_cleanUsername($poster);
			$data['last_format']=$this->_cleanFormat($last_format);
			$data['last_expiry']=$this->_cleanFormat($last_expiry);
		}
		
		return $data;
	}
	
	//we expect the following
	//$post['remember'] =0|1 to remember poster/format in cookie
	//$post['poster'] = name of poster, empty for anonymous
	//$post['format'] = syntax highlight format
	//$post['expiry'] = d m or f for the expiry time
	//$post['code2']  = posted code
	//this method assumes that inputs do NOT have "magic" quotes!
	//returns post id if successful
	
	function doPost(&$post)
	{
		$id=0;
		
		$this->errors=array();
		
		//validate some inputs
		$post["poster"]=$this->_cleanUsername($post["poster"]);
		$post["format"]=$this->_cleanFormat($post["format"]);
		$post["expiry"]=$this->_cleanExpiry($post["expiry"]);
			
		//set/clear the persistName cookie
		if ($post["remember"])
		{
			$value=$post["poster"].'#'.$post["format"].'#'.$post['expiry'];
			
			//set cookie if not set
			if (!isset($_COOKIE["persistName"]) || 
				($value!=$_COOKIE["persistName"]))
				setcookie ("persistName", $value, time()+3600*24*365);  
		}
		else
		{
			//clear cookie if set
			if (isset($_COOKIE['persistName']))
				setcookie ('persistName', '', 0);
		}
		
		if (strlen($post['code2']))
		{
			$poster=preg_replace('/[^A-Za-z0-9_ \-]/', '',$post['poster']);
			$poster=$post['poster'];
			if (strlen($poster)==0)
				$poster='Anonymous';
			
			$format=$post['format'];
			if (!array_key_exists($format, $this->conf['all_syntax']))
				$format='';
			
			$code=$post["code2"];
			
			//now insert..
			$parent_pid=0;
			if (isset($post["parent_pid"]))
				$parent_pid=intval($post["parent_pid"]);
				
			$id=$this->db->addPost($poster,$this->conf['subdomain'],$format,$code,
				$parent_pid,$post["expiry"]);
			
			
		}
		else
		{
			$this->errors[]="No code specified";
		}
		
		return $id;
	}	
	
	function getPostURL($id)
	{
		global $CONF;
		return sprintf("http://{$_SERVER['HTTP_HOST']}".$this->conf['url_format'], $id);
	}

	function redirectToPost($id)
	{
		header("Location:".$this->getPostURL($id));	
	}
	
	function doDownload($pid)
	{
		$ok=false;
		$post=$this->db->getPost($pid, $this->conf['subdomain']);
		if ($post)
		{
			//figure out extension
			$ext="txt";
			switch($post['format'])
			{
				case 'bash':
					$ext='sh';
					break;
				case 'actionscript':
					$ext='html';
					break;
				case 'html4strict':
					$ext='html';
					break;
				case 'javascript':
					$ext='js';
					break;
				case 'perl':
					$ext='pl';
					break;
				case 'php':
				case 'c':
				case 'cpp':
				case 'css':
				case 'xml':
					$ext=$post['format'];
					break;
			}
			
			
			// dl code
			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="'.$pid.'.'.$ext.'"');
			echo $post['code'];
			$ok=true;
	
		}
		else
		{
			//not found
			header('HTTP/1.0 404 Not Found');
		}
	
		return $ok;
	}
	
	/**
	* returns array of post summaries, each element has
	* url
	* poster
	* age
	*
	* parameter is a count or 0 for all
	*/
	function getRecentPosts($list=10)
	{
		//get raw db info
		$posts=$this->db->getRecentPostSummary($this->conf['subdomain'], $list);
		
		//augment with some formatting
		foreach($posts as $idx=>$post)
		{
			$age=$post['age'];
			$days=floor($age/(3600*24));
			$hours=floor($age/3600);
			$minutes=floor($age/60);
			$seconds=$age;
			
			if ($days>1)
				$age="$days dias atrás";
			elseif ($hours>0)
				$age="$hours hr".(($hours>1)?"s":"")." atrás";
			elseif ($minutes>0)
				$age="$minutes min".(($minutes>1)?"s":"")." atrás";
			else
				$age="$seconds seg".(($seconds>1)?"s":"")." atrás";
			
			$url=$this->getPostURL($post['pid']);
			
			$posts[$idx]['agefmt']=$age;
			$posts[$idx]['url']=$this->getPostURL($post['pid']);
			
		}
		
		return $posts;		
	}

	/**
	* Get formatted post, ready for inserting into a page
	* Returns an array of useful information
	*/
	function getPost($pid)
	{
		$post=$this->db->getPost($pid, $this->conf['subdomain']);
		if ($post)
		{
			//show a quick reference url, poster and parents
			$post['posttitle']="Postado por {$post['poster']} em {$post['postdate']}";
			
			if ($post['parent_pid']>0)
			{
				$parent_pid=$post['parent_pid'];
				
				$parent=$this->db->getPost($parent_pid, $this->conf['subdomain']);
				if ($parent)
				{
					
					$post['parent_poster']=$parent['poster'];
					$post['parent_url']=$this->getPostUrl($parent_pid);
					$post['parent_postdate']=$parent['postdate'];
					$post['parent_diffurl']=$this->conf['this_script']."?diff=$pid";
					
				}
			}
	
			//any amendments?
			$post['followups']=$this->db->getFollowupPosts($pid);
			foreach($post['followups'] as $idx=>$followup)
			{
				$post['followups'][$idx]['followup_url']=$this->getPostUrl($followup['pid']);	
			}
			
			$post['downloadurl']=$this->conf['this_script']."?dl=$pid";
			
			//store the code for later editing
			$post['editcode']=$post['code'];
	
	
			//preprocess
			$highlight=array();
			$prefix_size=strlen($this->conf['highlight_prefix']);
			if ($prefix_size)
			{
				$lines=explode("\n",$post['editcode']);
				$post['editcode']="";
				foreach ($lines as $idx=>$line)
				{
					if (substr($line,0,$prefix_size)==$this->conf['highlight_prefix'])
					{
						$highlight[]=$idx+1;
						$line=substr($line,$prefix_size);
					}
					$post['editcode'].=$line."\n";
				}
				$post['editcode']=rtrim($post['editcode']);
			}
				
			//get formatted version of code
			if (strlen($post['codefmt'])==0)
			{
                                $lines = explode("\n", $post['editcode']);
                                $posts = array();
                                $titles = array();
                                $current_post = 0;
                                foreach ($lines as $line) {
                                    if (substr($line, 0, 3) == "===") {
                                        $current_post++;
                                        $titles[$current_post] = substr($line, 3);
                                    } else {
                                        if (isset($posts[$current_post]))
                                            $posts[$current_post] = $posts[$current_post] . "\n" . $line;
                                        else
                                            $posts[$current_post] = $line;
                                    }
                                }

                                $formated = null;
                                for ($i = 0; $i <= $current_post; $i++) {
                                    if (isset($posts[$i])) {
                                        if (isset($titles[$i]))
                                            $formated .= "<h2>" . $titles[$i] . "</h2>";
                                        
                                        $geshi = new GeSHi($posts[$i], $post['format']);
				
                                        $geshi->enable_classes();
		    		        $geshi->set_header_type(GESHI_HEADER_DIV);
                                        $geshi->set_line_style('background: #ffffff;', 'background: #f4f4f4;');
    				        //$geshi->set_comments_style(1, 'color: #008800;',true);
    				        //$geshi->set_comments_style('multi', 'color: #008800;',true);
    				        //$geshi->set_strings_style('color:#008888',true);
    				        //$geshi->set_keyword_group_style(1, 'color:#000088',true);
    				        //$geshi->set_keyword_group_style(2, 'color:#000088;font-weight: normal;',true);
    				        //$geshi->set_keyword_group_style(3, 'color:black;font-weight: normal;',true);
    				        //$geshi->set_keyword_group_style(4, 'color:#000088',true);
    				        //$geshi->set_symbols_style('color:#ff0000');
    				
				        if (count($highlight))
				        {
					    $geshi->enable_line_numbers(GESHI_NORMAL_LINE_NUMBERS);
    					    $geshi->highlight_lines_extra($highlight);
				    	    $geshi->set_highlight_lines_extra_style('color:black;background:#FFFF88;');
				        }
				        else
				        {
					    $geshi->enable_line_numbers(GESHI_FANCY_LINE_NUMBERS,2);
				        }
				
        			        $formated .= $geshi->parse_code();
                                    }
                                }

				$post['codefmt']=$formated;
				$post['codecss']=$geshi->get_stylesheet();

				//save it!
				$this->db->saveFormatting($pid, $post['codefmt'], $post['codecss']);
			    }
			
			    $post['pid']=$pid;
		}
		else
		{
			$post['codefmt']="<b>Unknown post id, it may have been deleted</b><br />";
		}	
		
		return $post;
	}
	
}
