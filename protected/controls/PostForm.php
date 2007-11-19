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
Prado::using("System.I18N.TTranslate");
Prado::using("Application.models.Post");
/**
 * This control is the default post formulary that
 * will be replicated in several pages
 * PostForm class (the backend for PostForm control)
 * @author Carlos Henrique Júnior <carlos@milk-it.net>
 */
class PostForm extends TTemplateControl
{

    public function onLoad($param)
    {
        global $config;
        parent::onLoad($param);
        if (!$this->Page->IsPostBack)
        {
            $this->loadConfigurations();
            $this->lineHighlightHelp->Visible = $config["general"]["line_highlight"] == 1;
            $this->blocksHelp->Visible = $config["general"]["blocks"] == 1;
        }
        $this->saveButton->OnClick->add(array($this, "onSave"));
    }

    public function setParentPost($post)
    {
        $this->languageHighlight->SelectedValue = $post->highlight;
        $this->postContent->Text = $post->code;
        $this->parentId->Value = $post->id;
        if (strlen($post->private_key) > 0)
        {
            $this->private->Checked = true;
            $this->private->Enabled = false;
        }
    }

    public function onSave($param)
    {
        if ($this->Page->IsValid)
        {
            $post = new Post();
            $post->parent_id = $this->getParentId();
            $post->code = $this->getCode();
            $post->highlight = $this->getHighlight();
            $post->expire = $this->getValidity();
            $post->private_key = $this->isPrivatePost() ? uniqid() : null;
            $post->name = $this->getName();

            $post->save();

            if ($this->rememberMe->Checked)
            {
                $expire = time() + 60 * 60 *24 * 30;

                $cookie = new THttpCookie("__name", $this->posterName->getText());
                $cookie->setExpire($expire);
                $this->Page->Response->Cookies->add($cookie);

                $cookie = new THttpCookie("__validity", $this->validity->getSelectedValue());
                $cookie->setExpire($expire);
                $this->Page->Response->Cookies->add($cookie);

                $cookie = new THttpCookie("__highlight", $this->languageHighlight->getSelectedValue());
                $cookie->setExpire($expire);
                $this->Page->Response->Cookies->add($cookie);

                $this->disableCaptcha();
            }

            $this->Response->redirect("?page=Show&private=$post->private_key");
        }
        $this->raiseEvent("OnSave", $this, $param);
    }

    public function getCode()
    {
        return $this->postContent->Text;
    }

    public function getName()
    {
        return $this->posterName->Text;
    }

    public function getHighlight()
    {
        return $this->languageHighlight->getSelectedValue();
    }

    public function getValidity()
    {
        return $this->validity->getSelectedValue();
    }

    public function getParentId()
    {
        return $this->parentId->getValue();
    }

    public function isPrivatePost()
    {
        return $this->private->Checked;
    }

    private function disableCaptcha()
    {
        $this->captcha->Visible = false;
        $this->captchaText->Visible = false;
        $this->captchaValidator->Visible = false;
    }

    private function loadConfigurations()
    {
        global $config;

        // If the poster is passed in URL, set the posterName as poster of URL
        if (isset($this->Page->Request["poster"]))
            $this->posterName->Text = $this->Page->Request["poster"];

        if ($name = $this->Page->Request->Cookies["__name"])
        {
            // disabling captcha
            $this->disableCaptcha();

            // loading data
            $this->posterName->Text = $name->getValue();
            $this->validity->setSelectedValue($this->Page->Request->Cookies["__validity"]->getValue());
            $this->languageHighlight->setSelectedValue($this->Page->Request->Cookies["__highlight"]->getValue());
    
            $this->rememberMe->Checked = true;
        } else if ($config["general"]["captcha"] != 1)
            $this->disableCaptcha();
    }
} // end PostForm class
