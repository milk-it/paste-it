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
Prado::using("Application.models.Post");
/**
 * This class handles the Layout for the whole paste-it program.
 * If you wants to change the Paste-it layout, please, try to do it
 * creating a new Theme and setting it on the config.ini file.
 * Beleave, change Layout.(php|tpl) is not the better way.
 *
 * Paste-it Layout class
 * @author Carlos Henrique Júnior <carlos@milk-it.net>
 */
class Layout extends TTemplateControl
{
    public function onLoad($param)
    {
        global $config;

        if (isset($config["general"]["theme"]))
            $this->Page->Theme = $config["general"]["theme"];

        if (!$this->Page->IsPostBack)
        {
            $r = isset($config["general"]["recents"]) ? $config["general"]["recents"] : 10;

            $this->beforePosts->DataSource = Post::recents($r);
            $this->beforePosts->DataBind();
        }

        parent::onLoad($param);
    }
} // end of Layout class
?>
