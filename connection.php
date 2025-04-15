<?php

function OpenCon()
{
    $conn = new mysqli("localhost", "root", "", "edoc");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
