<?php

use PHPUnit\Framework\TestCase;

class PageTest extends TestCase
{
    public static function pageProvider()
    {
        return [
            ['index.php', 'eDoc', 'Make Appointment'],
            ['login.php', 'Login', 'email'],
            ['signup.php', 'Register', 'password']
        ];
    }

    /**
     * @dataProvider pageProvider
     */
    public function testPagesContainCriticalElements($file, ...$keywords)
    {
        $path = __DIR__ . '/../' . $file;
        $this->assertFileExists($path);

        $content = file_get_contents($path);
        foreach ($keywords as $keyword) {
            $this->assertStringContainsString($keyword, $content);
        }
    }
}
