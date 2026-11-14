<?php

declare(strict_types=1);

namespace StanislasPoisson\DevTools\Tests;

/**
 * A directory that is removed with its content when the object goes away.
 */
final readonly class TemporaryDirectory
{
    public string $path;

    public function __construct()
    {
        $path = tempnam(sys_get_temp_dir(), 'devtools');
        unlink((string) $path);
        mkdir((string) $path);

        $this->path = (string) $path;
    }

    public function __destruct()
    {
        $this->remove($this->path);
    }

    public function write(string $name, string $content): void
    {
        file_put_contents($this->path . '/' . $name, $content);
    }

    private function remove(string $path): void
    {
        // scandir() instead of glob(GLOB_BRACE): musl (Alpine) does not define GLOB_BRACE
        $items = scandir($path);

        foreach (false === $items ? [] : $items as $name) {
            if (in_array($name, ['.', '..'], true)) {
                continue;
            }

            $item = $path . '/' . $name;

            is_dir($item) ? $this->remove($item) : unlink($item);
        }

        rmdir($path);
    }
}
