<?php
/**
 * Init script for PRADO Framework
 * @author Carlos Júnior <carlos@milk-it.net>
 */
// load the configuration file
$config = parse_ini_file("config.ini", true);
// requires the Prado framework init script
$pradoPath = isset($config["general"]["pradoLocation"]) ? $config["general"]["pradoLocation"] : dirname(__FILE__) . "/framework/";
require_once($pradoPath . "/pradolite.php");

// starting paste2 app
$app = new TApplication;

// configuring database
Prado::using("System.Data.TDbConnection");
Prado::using("System.Data.ActiveRecord.TActiveRecordManager");

if (!isset($config["database"]["conn"]))
    throw new Exception("Please set the database/con variable on the config.ini file.");

$conn = new TDbConnection();
$conn->ConnectionString = $config["database"]["conn"];
$conn->Username = $config["database"]["user"];
$conn->Password = $config["database"]["pass"];
TActiveRecordManager::getInstance()->setDbConnection($conn);

// starting "the magic place where magic things happens"
$app->run();
?>
