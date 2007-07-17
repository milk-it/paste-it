<?php
/**
 * $Project: Pastebin $
 * $Id: layout.php,v 1.1 2006/04/27 16:22:39 paul Exp $
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
 
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?>\n";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<!--
pastebin.com Copyright 2006 Paul Dixon - email suggestions to lordelph at gmail.com
-->
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title><?php echo $page['title'] ?></title>
<meta name="ROBOTS" content="NOARCHIVE"/>
<link rel="stylesheet" type="text/css" media="screen" href="/pastebin.css?ver=4" />

<?php if (isset($page['post']['codecss']))
{
	echo '<style type="text/css">';
	echo $page['post']['codecss'];
	echo '</style>';
}
?>
<script type="text/javascript" src="/pastebin.js?ver=3"></script>
<script type="text/javascript" src="/prototype.js"></script>
<script type="text/javascript" src="/milx/milx.js" /></script>
<script type="text/javascript">
    Event.onLoad(function() { new Milx.FormHelper.AutoResizeTextarea("code", 40); });
</script>
</head>


<body onload="initPastebin()">
<div style="display:none;">
<h1 style="display: none;">pastebin - collaborative debugging</h1>
<p style="display: none;">pastebin is a collaborative debugging tool allowing you to share
and modify code snippets while chatting on IRC, IM or a message board.</p>
<p style="display: none;">This site is developed to XHTML and CSS2 W3C standards.  
If you see this paragraph, your browser does not support those standards and you 
need to upgrade.  Visit <a href="http://www.webstandards.org/upgrade/" target="_blank">WaSP</a>
for a variety of options.</p>
</div>

<div id="titlebar">
<a href="/"><img src="logo_milkit.gif" border="0"></a>
<span>
<?php 
	echo $page['title'];
?>
</span>
</div>



<div id="menu">

<h1>Posts Recentes</h1>
<ul>
<?php  
	foreach($page['recent'] as $idx=>$entry)
	{
		if ($entry['pid']==$pid)
			$cls=" class=\"highlight\"";
		else
			$cls="";
			
		echo "<li{$cls}><a href=\"{$entry['url']}\">";
		echo $entry['poster'];
		echo "</a><br/>{$entry['agefmt']}</li>\n";
	}
?>
<li><a href="<?php echo $CONF['this_script'] ?>">Criar novo post</a></li>
</ul>

<h1>Sobre</h1>
<p>Pastebin é uma ferramenta de debugging colaborativo, <a href="<?php echo $CONF['this_script'].'?help=1' ?>">veja o help</a>
para maiores informações.</p>

<script>
    function buildQuery()
    {
        frm = document.getElementById("search_form");
        url = frm["engine"].value;
        url = url + frm["searched_value"].value;

        window.open(url);

        return false;
    }
</script>
<h1>Documentações</h1>
<form id="search_form" onsubmit="javascript:return buildQuery()">
    <select name="engine">
        <option value="http://www.mysql.com/search/?base=http://dev.mysql.com&lang=en&doc=1&m=o&q=">MySQL Manual</option>
        <option value="http://www.php.net/search.php?show=quickref&pattern=">PHP Manual</option>
    </select>
    <br />
    <input type="text" size="9" name="searched_value" />
    <input type="submit" value="Go!" />
</form>

<h1>Créditos</h1>
<p>Desenvolvido por <a title="Blog, principalmente sobre desenvolvimento de software" href="http://blog.dixo.net">Paul Dixon</a></p>
<p>Personalizado por <a title="Milk-it Brasil Software House" href="http://www.milk-it.net">Milk-it</a></p>

<!--  
<p>
    <a href="http://validator.w3.org/check/referer"><img
        src="http://www.w3.org/Icons/valid-xhtml10"
        alt="Valid XHTML 1.0!" height="31" width="88" border="0"/></a>
</p>
-->

</div>


<div id="content">
	
	<?php
/*
 * Google AdWords block is below - if you re-use this script, be sure
 * to configure your own AdWords client id!
 */
if (strlen($CONF['google_ad_client'])) 
{
?>
<br/>
<?php
}

///////////////////////////////////////////////////////////////////////////////
// show processing errors
//
if (count($pastebin->errors))
{
	echo "<h1>Errors</h1><ul>";
	foreach($pastebin->errors as $err)
	{
		echo "<li>$err</li>";
	}
	echo "</ul>";
	echo "<hr />";
}


if (isset($_REQUEST["diff"]))
{
	
	$newpid=intval($_REQUEST['diff']);
	
	$newpost=$pastebin->getPost($newpid);
	if (count($newpost))
	{
		$oldpost=$pastebin->getPost($newpost['parent_pid']);	
		if (count($oldpost))
		{
			$page['pid']=$newpid;
			$page['current_format']=$newpost['format'];
			$page['editcode']=$newpost['code'];
			$page['posttitle']='';
	
			//echo "<div style=\"text-align:center;border:1px red solid;padding:5px;margin-bottom:5px;\">Diff feature is in BETA! If you have feedback, send it to lordelph at gmail.com</div>";
			echo "<h1>Diferença entre <br/>post modificado <a href=\"".$pastebin->getPostUrl($newpost['pid'])."\">{$newpost['pid']}</a> de {$newpost['poster']} em {$newpost['postdate']} e<br/>".
				"post original <a href=\"".$pastebin->getPostUrl($oldpost['pid'])."\">{$oldpost['pid']}</a> de {$oldpost['poster']} em {$oldpost['postdate']}<br/>";
			
			echo "Exibir ";
			echo "<a title=\"Nâo mostrar linhas modificadas ou inseridas\" style=\"padding:1px 4px 3px 4px;\" id=\"oldlink\" href=\"javascript:showold()\">antiga versão</a> | ";
			echo "<a title=\"Não mostrar linhas removidas da versão antiga\" style=\"padding:1px 4px 3px 4px;\" id=\"newlink\" href=\"javascript:shownew()\">nova versão</a> | ";
			echo "<a title=\"Mostrar ambas insersões e remoções\"  style=\"background:#880000;padding:1px 4px 3px 4px;\" id=\"bothlink\" href=\"javascript:showboth()\">ambas as versões</a> ";
			echo "</h1>";
			
			$newpost['code']=preg_replace('/^'.$CONF['highlight_prefix'].'/m', '', $newpost['code']);
			$oldpost['code']=preg_replace('/^'.$CONF['highlight_prefix'].'/m', '', $oldpost['code']);
			
			$a1=explode("\n", $newpost['code']);
			$a2=explode("\n", $oldpost['code']);
			
			$diff=new Diff($a2,$a1, 1);
			
			echo "<table cellpadding=\"0\" cellspacing=\"0\" class=\"diff\">";
			echo "<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td></tr>";
			echo $diff->output;
			echo "</table>";
		}
		
	}
	
	
}

///////////////////////////////////////////////////////////////////////////////
// show a post
//

if (isset($_GET['help']))
	$page['posttitle']="";
	
if (strlen($page['post']['posttitle']))
{
		echo "<h1>{$page['post']['posttitle']}";
		if ($page['post']['parent_pid']>0)
		{
			echo " (modificação ou post de <a href=\"{$page['post']['parent_url']}\" title=\"ver post original\">{$page['post']['parent_poster']}</a> ";
			echo "<a href=\"{$page['post']['parent_diffurl']}\" title=\"veja as diferenças\">veja diferenças</a>)";
		}
		
		echo "<br/>";
		
		$followups=count($page['post']['followups']);
		if ($followups)
		{
			echo "Veja respostas de ";
			$sep="";
			foreach($page['post']['followups'] as $idx=>$followup)
			{
				echo $sep."<a title=\"postado {$followup['postfmt']}\" href=\"{$followup['followup_url']}\">{$followup['poster']}</a>";
				$sep=($idx<($followups-2))?", ":" and ";	
			}
			
			echo " | ";
		}
		
		if ($page['post']['parent_pid']>0)
		{
			echo "<a href=\"{$page['post']['parent_diffurl']}\" title=\"veja as diferenças\">diff</a> | ";
		} 
		
		echo "<a href=\"{$page['post']['downloadurl']}\" title=\"download do arquivo\">download</a> | ";
		
		echo "<span id=\"copytoclipboard\"></span>";
		
		echo "<a href=\"/\" title=\"make new post\">new post</a>";
		
		echo "</h1>";
}
if (isset($page['post']['pid']))
{
	echo "<div class=\"syntax\">".$page['post']['codefmt']."</div>";
	echo "<br /><b>Enviar uma correção ou emenda abaixo. (<a href=\"{$CONF['this_script']}\">clique aqui para fazer um novo post</a>)</b><br/>";
	echo "Após enviar uma emenda, você poderá ver as diferenças entre o novo e o antigo post facilmente.";
}	



if (isset($_GET['help']))
{
	?>
	<h1>O que é o pastebin?</h1>
	<p>pastebin está aqui para ajudar você colaborando com o debug do código. Se você não é familiarizado com a idéia, a
        maioria das pessoas gostam de usar ele assim:</p>
	<ul>
	<li>submetem o fragmento do código para o pastebin, pegando a url</li>
	<li>colam a url em IRC ou conversas de IM</li>
	<li>alguém responde por ler e talvez submete a modificação no seu código</li>
	<li>você então ve a modifcação, talvez usando a ferramenta diff para localizar as alterações</li>
	</ul>
	
	<h1>Como eu posso ver a diferença entre dois posts?</h1>
	<p>Quando você ve um post, você tem a oportunidade de editar o texto - 
		<strong>isso criará um novo post</strong>, mas quando você visualizar você terá um link "diff" para 
                comparar as mudanças entre a velha e a nova versão.</p>
        <p>Isto é um poderoso recurso, ótimo para ver exatamente quais linhas alguém mudou.</p>	

	<?php
}
else
{
?>
<form name="editor" method="post" action="<?php echo $CONF['this_script']?>">
<input type="hidden" name="parent_pid" value="<?php echo $page['post']['pid'] ?>"/>

<br/>Usar <select name="format">
<option value="text">Não</option>
<optgroup label="Principais">
<?php

//show the popular ones
foreach ($CONF['all_syntax'] as $code=>$name)
{
	if (in_array($code, $CONF['popular_syntax']))
	{
		$sel=($code==$page['current_format'])?"selected=\"selected\"":"";
		echo "<option $sel value=\"$code\">$name</option>";
	}
}
?>
</optgroup>
<optgroup label="Outras Linguagens">
<?php
//show all formats
foreach ($CONF['all_syntax'] as $code=>$name)
{
	$sel=($code==$page['current_format'])?"selected=\"selected\"":"";
	if (in_array($code, $CONF['popular_syntax']))
		$sel="";
	echo "<option $sel value=\"$code\">$name</option>";
	
}
?>
</optgroup>
</select> sintaxe destacando<br/>
<br/>

Para destacar linhas particulares, prefixar cada linha com <?php echo $CONF['highlight_prefix'] ?><br/>
Para dividir o paste em blocos, prefixar cada cabeçalho de bloco com ===Nome do Bloco<br/>
<textarea id="code" class="codeedit" name="code2" cols="80" rows="10" onkeydown="return catchTab(this,event)"><?php 
echo htmlentities($page['post']['editcode']) ?></textarea>

<div id="namebox">
	
<label for="poster">Seu Nome</label><br/>
<input type="text" maxlength="24" size="24" id="poster" name="poster" value="<?php echo ($page['poster']) ? $page['poster'] : $_GET['poster'] ?>" />
<input type="submit" name="paste" value="Enviar"/>
<br />
<input type="checkbox" name="remember" value="1" <?php echo $page['remember'] ?>/>Lembrar minhas configurações

</div>


<div id="expirybox">


<div id="expiryradios">
<label>Como seu post deve ser guardado?</label><br/>

<input type="radio" id="expiry_day" name="expiry" value="d" <?php if ($page['expiry']=='d') echo 'checked="checked"'; ?> />
<label id="expiry_day_label" for="expiry_day">Dia</label>

<input type="radio" id="expiry_month" name="expiry" value="m" <?php if ($page['expiry']=='m') echo 'checked="checked"'; ?> />
<label id="expiry_month_label" for="expiry_month">Mês</label>

<input type="radio" id="expiry_forever" name="expiry" value="f" <?php if ($page['expiry']=='f') echo 'checked="checked"'; ?> />
<label id="expiry_forever_label" for="expiry_forever">Sempre</label>
</div>

<div id="expiryinfo"></div>
	
</div>

<div id="end"></div>

</form>
<?php 
} 
?>

</div>
</body>
</html>
