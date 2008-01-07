<!---
    This is the Layout for the whole paste-it program.
    If you wants to change the Paste-it layout, please, try to do it
    creating a new Theme and setting it on the config.ini file.
    Beleave, change Layout.(php|tpl) is not the better way.

    Copyright (C) 2007  Carlos Henrique Júnior <carlos@milk-it.net>

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
--->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<com:THead>
    <prop:Title>Paste-it - Colaborative Debugging Tool</prop:Title>
    <meta name="ROBOTS" content="NOARCHIVE"/>
	<link href="<%~ highlight.css %>" type="text/css" rel="stylesheet" />
</com:THead>
<body>
    <div id="top">
        <h1><a href="<%= $this->Request->constructUrl("page", "Home") %>">Paste-it</a></h1>
        <h2><%[colaborative debugging tool]%></h2>
        <div id="lang">
            <%[Language:]%>
            <a href="index.php?lang=pt_BR">Português</a> | 
            <a href="index.php?lang=en_US">English</a>
        </div>
    </div>
    <div id="menu">
	<h2><a href="<%= $this->Request->constructUrl("page", "Home") %>"><%[new <span>post</span>]%></a></h2>
        <h2><%[last <span>posts</span>]%></h2>
        <com:TRepeater ID="beforePosts">
            <prop:HeaderTemplate><ul></prop:HeaderTemplate>
            <prop:FooterTemplate></ul></prop:FooterTemplate>
            <prop:ItemTemplate>
                <li><a href="<%= $this->Data->id %>"><%= $this->Data->name %></a><br />
                    <%= $this->Data->human_age() %> <%[old]%>
                </li>
            </prop:ItemTemplate>
        </com:TRepeater>
        
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

        <h2 class="menu_item"><%[docs]%></h2>
        <form id="search_form" onsubmit="javascript:return buildQuery()">
            <select name="engine">
                <option value="http://www.mysql.com/search/?base=http://dev.mysql.com&lang=en&doc=1&m=o&q=">MySQL Manual</option>
                <option value="http://www.php.net/search.php?show=quickref&pattern=">PHP Manual</option>
            </select>
            <br />
            <input type="text" size="9" name="searched_value" />
            <input type="submit" value="Go!" />
        </form>

	<h2 class="menu_item" style="margin-top:15px;"><com:THyperLink NavigateUrl=<%=$this->Page->Request->constructUrl('page', 'Help')%>><%[help <span>center</span>]%></com:THyperLink></h2>
	<a href="http://www.milk-it.net/" class="milk-it"><com:TImage ImageUrl=<%~ images/milk-it.jpg %> /></a>
        <div class="milk-it"><%= Prado::poweredByPrado(); %></div>
    </div>

    <div id="content">
        <com:TForm>
            <com:TContentPlaceHolder ID="content" />
        </com:TForm>
    </div>
</body>
</html>
