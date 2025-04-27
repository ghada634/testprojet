<?php

function OpenCon()
{
    $conn = new mysqli("172.31.88.204", "root", "", "edoc");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    return $conn;
}
