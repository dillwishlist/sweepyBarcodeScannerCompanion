<?php
$serverName = "lansweeper.local";
$connectionOptions = array(
    "Database" => "lansweeperdb",
    "Uid" => "sqluser",
    "PWD" => "sqlpassword"
);
//Establishes the connection
$conn = sqlsrv_connect($serverName, $connectionOptions);
if($conn)
    echo "Connected!"
?>