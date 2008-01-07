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
Prado::using("Application.models.Diff");
Prado::using("Application.models.Post");
/**
 * ShowDiff backend.
 * Brings both posts and builds the diff component
 *
 * @author Carlos Henrique Júnior <carlos@milk-it.net>
 */
class ShowDiff extends TPage
{
    public function onLoad($param)
    {
        parent::onLoad($param);

        if (isset($_GET["private"]))
            $post1 = Post::findPrivate($_GET["private"]);
        else
            $post1 = Post::findPublic($_GET["id"]);

        $post2 = $post1->getParent();

        $diff = new Diff(explode("\n", $post2->code), explode("\n", $post1->code));
        $diff = "<table cellpadding=\"0\" cellspacing=\"0\" class=\"diff\">" .
	            "<tr><td></td><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td></tr>" .
		    $diff->output . "</table>";
        $this->diff->addParsedObject($diff);

        $this->originalLink->NavigateUrl = UrlGen::postPath($post2);
        $this->originalLink->Text = "#{$post2->id}";
        $this->modifiedLink->NavigateUrl = UrlGen::postPath($post1);
        $this->modifiedLink->Text = "#{$post1->id}";
    }
}

?>
