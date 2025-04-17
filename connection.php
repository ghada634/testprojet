<?php

function OpenCon()
{
    $conn = new mysqli("db", "root", "", "edoc");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
