<?php

declare(strict_types=1);

/*
 * This file is part of sebastian/environment.
 *
 * (c) Sebastian Bergmann <sebastian@phpunit.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace SebastianBergmann\Environment;

use const PHP_BINARY;
use const PHP_BINDIR;
use const PHP_MAJOR_VERSION;
use const PHP_SAPI;
use const PHP_VERSION;

use function array_map;
use function array_merge;
use function defined;
use function escapeshellarg;
use function explode;
use function extension_loaded;
use function getenv;
use function ini_get;
use function is_readable;
use function parse_ini_file;
use function php_ini_loaded_file;
use function php_ini_scanned_files;
use function phpversion;
use function sprintf;
use function strpos;

/**
 * Utility class for HHVM/PHP environment handling.
 */
final class Runtime
{
    /**
     * @var string
     */
    private static $binary;

    /**
     * Returns true when Xdebug or PCOV is available or
     * the runtime used is PHPDBG.
     */
    public function canCollectCodeCoverage(): bool
    {
        return $this->hasXdebug() || $this->hasPCOV() || $this->hasPHPDBGCodeCoverage();
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled,
     * and is configured to discard comments.
     */
    public function discardsComments(): bool
    {
        if (!$this->isOpcacheActive()) {
            return false;
        }

        if (ini_get('opcache.save_comments') !== '0') {
            return false;
        }

        return true;
    }

    /**
     * Returns true when Zend OPcache is loaded, enabled,
     * and is configured to perform just-in-time compilation.
     */
    public function performsJustInTimeCompilation(): bool
    {
        if (PHP_MAJOR_VERSION < 8) {
            return false;
        }

        if (!$this->isOpcacheActive()) {
            return false;
        }

        if (strpos(ini_get('opcache.jit'), '0') === 0) {
            return false;
        }

        return true;
    }

    /**
     * Returns the path to the binary of the current runtime.
     * Appends ' --php' to the path when the runtime is HHVM.
     */
    public function getBinary(): string
    {
        // HHVM
        if (self::$binary === null && $this->isHHVM()) {
            // @codeCoverageIgnoreStart
            if ((self::$binary = getenv('PHP_BINARY')) === false) {
                self::$binary = PHP_BINARY;
            }

            self::$binary = escapeshellarg(self::$binary) . ' --php' .
                ' -d hhvm.php7.all=1';
            // @codeCoverageIgnoreEnd
        }

        if (self::$binary === null && PHP_BINARY !== '') {
            self::$binary = escapeshellarg(PHP_BINARY);
        }

        if (self::$binary === null) {
            // @codeCoverageIgnoreStart
            $possibleBinaryLocations = [
                PHP_BINDIR . '/php',
                PHP_BINDIR . '/php-cli.exe',
                PHP_BINDIR . '/php.exe',
            ];

            foreach ($possibleBinaryLocations as $binary) {
                if (is_readable($binary)) {
                    self::$binary = escapeshellarg($binary);

                    break;
                }
            }
            // @codeCoverageIgnoreEnd
        }

        if (self::$binary === null) {
            // @codeCoverageIgnoreStart
            self::$binary = 'php';
            // @codeCoverageIgnoreEnd
        }

        return self::$binary;
    }

    public function getNameWithVersion(): string
    {
        return $this->getName() . ' ' . $this->getVersion();
    }

    public function getNameWithVersionAndCodeCoverageDriver(): string
    {
        if (!$this->canCollectCodeCoverage() || $this->hasPHPDBGCodeCoverage()) {
            return $this->getNameWithVersion();
        }

        if ($this->hasPCOV()) {
            return sprintf(
                '%s with PCOV %s',
                $this->getNameWithVersion(),
                phpversion('pcov')
            );
        }

        if ($this->hasXdebug()) {
            return sprintf(
                '%s with Xdebug %s',
                $this->getNameWithVersion(),
                phpversion('xdebug')
            );
        }
    }

    public function getName(): string
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return 'HHVM';
            // @codeCoverageIgnoreEnd
        }

        if ($this->isPHPDBG()) {
            // @codeCoverageIgnoreStart
            return 'PHPDBG';
            // @codeCoverageIgnoreEnd
        }

        return 'PHP';
    }

    public function getVendorUrl(): string
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return 'http://hhvm.com/';
            // @codeCoverageIgnoreEnd
        }

        return 'https://secure.php.net/';
    }

    public function getVersion(): string
    {
        if ($this->isHHVM()) {
            // @codeCoverageIgnoreStart
            return HHVM_VERSION;
            // @codeCoverageIgnoreEnd
        }

        return PHP_VERSION;
    }

    /**
     * Returns true when the runtime used is PHP and Xdebug is loaded.
     */
    public function hasXdebug(): bool
    {
        return ($this->isPHP() || $this->isHHVM()) && extension_loaded('xdebug');
    }

    /**
     * Returns true when the runtime used is HHVM.
     */
    public function isHHVM(): bool
    {
        return defined('HHVM_VERSION');
    }

    /**
     * Returns true when the runtime used is PHP without the PHPDBG SAPI.
     */
    public function isPHP(): bool
    {
        return !$this->isHHVM() && !$this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI.
     */
    public function isPHPDBG(): bool
    {
        return PHP_SAPI === 'phpdbg' && !$this->isHHVM();
    }

    /**
     * Returns true when the runtime used is PHP with the PHPDBG SAPI
     * and the phpdbg_*_oplog() functions are available (PHP >= 7.0).
     */
    public function hasPHPDBGCodeCoverage(): bool
    {
        return $this->isPHPDBG();
    }

    /**
     * Returns true when the runtime used is PHP with PCOV loaded and enabled.
     */
    public function hasPCOV(): bool
    {
        return $this->isPHP() && extension_loaded('pcov') && ini_get('pcov.enabled');
    }

    /**
     * Parses the loaded php.ini file (if any) as well as all
     * additional php.ini files from the additional ini dir for
     * a list of all configuration settings loaded from files
     * at startup. Then checks for each php.ini setting passed
     * via the `$values` parameter whether this setting has
     * been changed at runtime. Returns an array of strings
     * where each string has the format `key=value` denoting
     * the name of a changed php.ini setting with its new value.
     *
     * @return string[]
     */
    public function getCurrentSettings(array $values): array
    {
        $diff  = [];
        $files = [];

        if ($file = php_ini_loaded_file()) {
            $files[] = $file;
        }

        if ($scanned = php_ini_scanned_files()) {
            $files = array_merge(
                $files,
                array_map(
                    'trim',
                    explode(",\n", $scanned)
                )
            );
        }

        foreach ($files as $ini) {
            $config = parse_ini_file($ini, true);

            foreach ($values as $value) {
                $set = ini_get($value);

                if (empty($set)) {
                    continue;
                }

                if ((!isset($config[$value]) || ($set !== $config[$value]))) {
                    $diff[$value] = sprintf('%s=%s', $value, $set);
                }
            }
        }

        return $diff;
    }

    private function isOpcacheActive(): bool
    {
        if (!extension_loaded('Zend OPcache')) {
            return false;
        }

        if ((PHP_SAPI === 'cli' || PHP_SAPI === 'phpdbg') && ini_get('opcache.enable_cli') === '1') {
            return true;
        }

        if (PHP_SAPI !== 'cli' && PHP_SAPI !== 'phpdbg' && ini_get('opcache.enable') === '1') {
            return true;
        }

        return false;
    }
}
