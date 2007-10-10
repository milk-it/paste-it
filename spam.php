<?php
    session_start();
    require_once "lib/pastebin/config.inc.php";
    require_once "lib/pastebin/pastebin.class.php";

    $pastebin=new Pastebin($CONF);

    if (($_GET["p"]==$pastebin->conf['spampass']) || $_SESSION["logged"]) {
        /**
         * Logout the admin of spam admin
         */
        if ($_GET["logout"]) {
            unset($_SESSION['logged']);
            header("Location: ".$pastebin->conf["this_script"]);
            exit();
        }

        if (count($_POST["pids"])) {
            switch ($_POST["action"]) {
                /**
                 * Delete the posts selected and banned the ip of spammer
                 */
                case "Remove":
                    $pastebin->db->bannedSpammerPosts($_POST["pids"]);
                    $pastebin->db->deletePosts($_POST["pids"]);
                    break;
                /**
                 * Define the posts as not was spam
                 */ 
                case "NoSpam":
                    $pastebin->db->setFlagSpam($_POST["pids"], 0);
                    break;
            }
        }

        $_SESSION["logged"]="1";
        $posts=$pastebin->db->getSpamPosts($subdomain);
?>
<form method="post" action="<?=$_SERVER["PHP_SELF"]?>">
    <table border="1">
        <tr>
            <th></th>
            <th>Pid</th>
            <th>Poster</th>
            <th>IP</th>
        </tr>
<?php
        foreach ($posts as $post) {
?>
        <tr>
            <td><input type="checkbox" name="pids[]" value="<?=$post["pid"]?>" checked="checked" /></td>
            <td><a href="<?=$pastebin->getPostURL($post["pid"])?>"><?=$post["pid"]?></a></td>
            <td><?=$post["poster"]?></td>
            <td><?=$post["ip"]?></td>
        </tr>
<?php   } ?>
    </table>
    <input type="submit" name="action" value="Remove" />
    <input type="submit" name="action" value="NoSpam" />
</form>
<a href="?logout=1">Logout</a> | <a href="<?=$_SERVER["PHP_SELF"]?>">Reload</a> | IPs Banned: <?=$pastebin->db->getNumIpBanneds($subdomain)?>
<?php        
    } else {
        header("HTTP/1.0 404 Not Found");
    }
?>
