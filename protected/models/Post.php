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
Prado::using("System.Data.ActiveRecord.TActiveRecord");
Prado::using("System.Util.TDateTimeStamp");
/**
 * Post model class
 * @author Carlos Júnior <carlos@milk-it.net>
 */
class Post extends TActiveRecord
{
    const TABLE = "posts";

    public $id;
    public $parent_id;
    public $name;
    public $expire;
    private $_created_on;
    public $code;
    public $highlight;
    public $private_key;

    public function getCreated_On()
    {
        if ($this->_created_on === null)
            $this->_created_on = strftime("%Y-%m-%d %H:%M:%S", time());

        return $this->_created_on;
    }

    public function setCreated_On($val)
    {
        $this->_created_on = $val;
    }

    public static function finder($classname=__CLASS__)
    {
        return parent::finder($classname);
    }

    public static function recents($max=10)
    {
        $c = new TActiveRecordCriteria;
        $c->OrdersBy["id"] = "DESC";
        $c->Limit = $max;
        $c->Condition = "created_on > DATE_SUB(current_timestamp, INTERVAL $max DAY) AND private_key IS NULL";
        return self::finder()->findAll($c);
    }

    public static function findPrivate($key)
    {
        return self::finder()->find("private_key = '$key'");
    }

    public static function findPublic($id)
    {
        return self::finder()->find("id = $id AND private_key IS NULL");
    }

    public function getParent()
    {
        if ($this->parent_id)
            return self::finder()->find("id = $this->parent_id");
    }

    public function human_age()
    {
        $d = date_parse($this->created_on);
        $d = mktime($d["hour"], $d["minute"], $d["second"], $d["month"], $d["day"], $d["year"]);
        $age = mktime() - $d;
        $days=floor($age/(3600*24));
        $hours=floor($age/3600);
        $minutes=floor($age/60);
        $seconds=$age;
        
        if ($days>1)
                $age="{$days}d";
        elseif ($hours>0)
                $age="{$hours}hr".(($hours>1)?"s":"");
        elseif ($minutes>0)
                $age="{$minutes}min";
        else
                $age="{$seconds}seg";
        
        return $age;
    }

    public function descendents()
    {
        $c = new TActiveRecordCriteria;
        $c->OrdersBy["id"] = "ASC";
        $c->Condition = "parent_id = ? AND private_key IS NULL";

        return self::finder()->findAll("parent_id = ?", $this->id);
    }
}

?>
