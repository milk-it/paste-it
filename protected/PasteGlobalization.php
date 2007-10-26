<?php
/**
 *  Copyright (C) 2007  Carlos Henrique Júnior <carlos@milk-it.net>
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
/**
 *  PasteGlobalization handles the globalization (i18n) in paste-it app.
 *  This class is responsible by discover what is the user preferred
 *  language and save to use another if the user wants.
 *
 *  @author Carlos Henrique Júnior <carlos@milk-it.net>
 */
class PasteGlobalization extends TGlobalizationAutoDetect
{
    public function init($xml)
    {
	parent::init($xml);
	$this->Application->OnBeginRequest[] = array($this, 'beginRequest');
    }

    public function beginRequest($sender, $param)
    {
	if(null == ($culture = $this->Request['lang']))
	{
	    if(null !== ($cookie=$this->Request->Cookies['lang']))
	        $culture = $cookie->getValue();
	}

	if(is_string($culture))
	{
	    $this->setCulture($culture);
	    $this->Response->Cookies[] = new THttpCookie('lang', $culture);
	}
    }
}

?>
