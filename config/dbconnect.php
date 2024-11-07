<?php
require 'config.php';
function connect()
{
    // $user = "root";
    // $pass = "";
    // $db = "TaskManagementDb";
    // $mysqli = new mysqli("localhost", $user, $pass, $db );
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($mysqli->connect_errno )
    {
        die( "Couldn't connect to MySQL" );
    }
    return $mysqli;
}
