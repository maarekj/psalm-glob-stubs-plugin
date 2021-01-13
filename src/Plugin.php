<?php

namespace JmvDevelop\PsalmGlobStubsPlugin;

use Psalm\Plugin\PluginEntryPointInterface;
use Psalm\Plugin\RegistrationInterface;
use SimpleXMLElement;
use Symfony\Component\Finder\Finder;

final class Plugin implements PluginEntryPointInterface
{
    public function __invoke(RegistrationInterface $registration, ?SimpleXMLElement $config = null): void
    {
        if ($config == null) {
            return;
        }

        $globs = (array)$config->glob;
        foreach ($globs as $glob) {
            if (false === is_array($glob)) {
                continue;
            }

            $dir = (string)($glob['dir'] ?? "");
            if ($this->startsWith($dir, "./") === true) {
                $dir = getcwd() . substr($dir, 1, strlen($dir) - 1);
            }

            $files = (Finder::create())
                ->in($dir)
                ->name((string)($glob['name'] ?? ""))
                ->files()
                ->getIterator();

            foreach ($files as $file) {
                $registration->addStubFile($file->getPathname());
            }
        }
    }

    private function startsWith(string $haystack, string $needle): bool
    {
        $length = \strlen($needle);

        return \substr($haystack, 0, $length) === $needle;
    }
}
