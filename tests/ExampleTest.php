<?php

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    public function testTrue()
    {
        $this->assertTrue(true);
    }

    public function testHomePageTitle()
    {
        $filePath = __DIR__ . '/../index.php';

        // Vérification robuste du fichier
        if (!file_exists($filePath)) {
            $this->fail("Le fichier index.php n'existe pas à l'emplacement attendu");
        }

        // Lecture avec vérification d'erreur
        $html = file_get_contents($filePath);

        if ($html === false) {
            $this->fail("Impossible de lire le contenu du fichier");
        }

        // Méthode moderne recommandée (PHPUnit 8+ et PHP 8+)
        $this->assertStringContainsString('eDoc', $html);

        // Alternative si nécessaire
        $this->assertTrue(str_contains($html, 'eDoc'));
    }
}
