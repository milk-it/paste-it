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
 * This class will help us to generate that URLs
 * @author Carlos Júnior <carlos@milk-it.net>
 */
class UrlGen
{
    private static $BASE = "/";

    public static function setBase($base)
    {
        self::$BASE = $base;
    }

    public static function postPath($post)
    {
        if ($post->private_key)
            return self::$BASE . "private/{$post->private_key}";
        else
            return self::$BASE . $post->id;
    }
}

?>
