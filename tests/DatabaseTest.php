<?php

use PHPUnit\Framework\TestCase;

class DatabaseTest extends TestCase
{
    private $db;

    protected function setUp(): void
    {
        $this->db = new PDO('sqlite::memory:');
        // Initialisez votre schéma de test ici
    }

    public function testUserCreation()
    {
        $stmt = $this->db->prepare("INSERT INTO users (email) VALUES (?)");
        $this->assertTrue($stmt->execute(['test@example.com']));
    }

    protected function tearDown(): void
    {
        $this->db = null;
    }
}
