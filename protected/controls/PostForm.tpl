<!---
    This control is the default post formulary that will be replicated
    in several pages.

    This file is the FrontEnd for the PostForm.

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
<com:TValidationSummary />
<com:THiddenField ID="parentId" Value="0" />
<div id="highlight">
    <com:TLabel ForControl="languageHighlight" Text="<%[Code Highlight:]%>" />
    <com:TDropDownList ID="languageHighlight">
        <com:TListItem Text="<%[None]%>" value="none" />
        <com:TListItem Text="Abap" Value="abap" />
        <com:TListItem Text="ActionScript" Value="actionscript" />
        <com:TListItem Text="ASP" Value="asp" />
        <com:TListItem Text="Bash" Value="bash" />
        <com:TListItem Text="C" Value="c" />
        <com:TListItem Text="C++" Value="cpp" />
        <com:TListItem Text="C#" Value="csharp" />
        <com:TListItem Text="ColdFusion" Value="cfm" />
        <com:TListItem Text="CSS" Value="css" />
        <com:TListItem Text="Diff" Value="diff" />
        <com:TListItem Text="HTML" Value="html" />
        <com:TListItem Text="Ini File" Value="ini" />
        <com:TListItem Text="JavaScript" Value="javascript" />
        <com:TListItem Text="Java" Value="java" />
        <com:TListItem Text="Lisp" Value="lisp" />
        <com:TListItem Text="Mysql" Value="mysql" />
        <com:TListItem Text="Pascal" Value="pascal" />
        <com:TListItem Text="Perl" Value="perl" />
        <com:TListItem Text="Php" Value="php" />
        <com:TListItem Text="Prado" Value="prado" />
        <com:TListItem Text="Python" Value="python" />
        <com:TListItem Text="Ruby" Value="ruby" />
        <com:TListItem Text="SQL" Value="sql" />
        <com:TListItem Text="TCL" Value="sql" />
        <com:TListItem Text="XML" Value="xml" />
    </com:TDropDownList><br />
    <p>
        <com:TTranslate ID="lineHighlightHelp" Text="to highlight a line prefix it with @@" /><br />
        <com:TTranslate ID="blocksHelp" Text="to create blocks prefix the header (the title) with ===" />
        <com:THyperLink NavigateUrl=<%=$this->Page->Request->constructUrl('page', 'Help')%>>(<%[More help]%>)</com:THyperLink>
    </p>
</div>
<div style="text-align:right">
    <com:TCheckBox ID="private" />
    <com:TLabel ForControl="private">
        <com:TImage ImageUrl="<%~ images/lock.gif %>" />
        <%[private post]%>
    </com:TLabel>
</div>
<com:TTextBox ID="postContent" TextMode="MultiLine" Columns="80" Rows="10" CssClass="postContent" />
<script type="text/javascript" src="<%~ js/milx/milx.js %>"></script>
<script type="text/javascript">
    new Milx.FormHelper.AutoResizeTextarea('<%= $this->postContent->ClientID %>', 30);
</script>
<div id="name_validity">
    <div id="validity">
        <com:TLabel ForControl="validity" Text="<%[How should we store your post?]%>" />
        <com:TRadioButtonList ID="validity" RepeatDirection="Horizontal" SelectedIndex="0">
            <com:TListItem Text="<%[Day]%>" Value="1" />
            <com:TListItem Text="<%[Month]%>" Value="30" />
            <com:TListItem Text="<%[Forever]%>" Value="0" />
        </com:TRadioButtonList>
    </div>
    <div id="name">
        <com:TLabel ForControl="posterName" Text="<%[Name:]%>" />
        <com:TTextBox ID="posterName" Maxlength="50" />
        <com:TRequiredFieldValidator
            ControlToValidate="posterName"
            ErrorMessage="<%[Don't you have a name?]%>"
            Display="None" />
        <span><com:TButton ID="saveButton" Text="<%[Post!]%>" /></span><br />
        <com:TCheckBox ID="rememberMe" />
        <com:TLabel ForControl="rememberMe" Text="<%[Remember me (and eliminate the captcha)]%>" />
    </div>
</div>
<div id="captchaBox">
    <com:TCaptcha ID="captcha" CaseSensitive="false" MinTokenLength="3" MaxTokenLength="3" />
    <com:TTextBox ID="captchaText" />
    <com:TCaptchaValidator ID="captchaValidator"
        CaptchaControl="captcha"
        ControlToValidate="captchaText"
        Display="None"
        ErrorMessage="<%[Please, type the right string of the image]%>" />
</div>
<br style="clear:both;" />
