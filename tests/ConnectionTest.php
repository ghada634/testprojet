<?php

use PHPUnit\Framework\TestCase;

require_once __DIR__ . '/../connection.php';

class ConnectionTest extends TestCase
{
    public function testDatabaseConnection()
    {
        $conn = OpenCon();
        $this->assertInstanceOf(mysqli::class, $conn);
    }

    public function testQuery()
    {
        $conn = OpenCon();
        $result = $conn->query("SHOW TABLES LIKE 'test_users'");
        $this->assertGreaterThan(0, $result->num_rows, "Table test_users not found.");
    }
}
