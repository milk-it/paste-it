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
 * Show page backend. Brings the post from the database and builds the
 * Highlight controls.
 *
 * @author Carlos Henrique Júnior <carlos@milk-it.net>
 */
class Show extends TPage
{
    public function onPreRender($param)
    {
        parent::onPreRender($param);

        if (isset($_GET["private"]))
            $post = Post::finder()->findPrivate($_GET["private"]);
        else
            $post = Post::finder()->findPublic($_GET["id"]);

        if (!$post)
            $this->Response->redirect($this->Request->constructUrl("page", "Home"));
        else
        {
            $this->processText($post);
    
            $this->postForm->setParentPost($post);

            if ($post->parent_id != 0)
            {
                $this->parentId->Text = $post->parent_id;
                $this->diffLink->NavigateUrl = $this->Request->constructUrl("page", "ShowDiff", array("id" => $post->id));
            }
            else
                $this->parentPost->Visible = false;
    
            $this->descendents->DataSource = $post->descendents();
            $this->descendents->dataBind();

            $this->postId->Text = $post->id;
            $this->posterName->Text = $post->name;
            $this->postDate->Text = $post->created_on;
        }
    }

    private function processText($post)
    {
        $buffer = array();
        $lines = explode("\n", $post->code);
        foreach ($lines as $line)
        {
            if (substr($line, 0, 3) == "===")
            {
                if (count($buffer) > 0)
                {
                    $this->codes->addParsedObject($this->createHighlight($buffer, $post->highlight));
                    $buffer = array();
                }
                $this->codes->addParsedObject("<h4>" . substr($line, 3) . "</h4>");
            } else
                $buffer[] = $line;
        }
        if (count($buffer) > 0)
        {
            $this->codes->addParsedObject($this->createHighlight($buffer, $post->highlight));
        }
    }

    private function createHighlight($buffer, $language)
    {
        $o = new TTextHighlighter;
        $o->Text = implode("\n", $buffer);
        $o->Language = $language;
        $o->ShowLineNumbers = true;
        $o->LineNumberStyle = "Li";
        $o->TabSize = 4;

        return $o;
    }
}

?>
