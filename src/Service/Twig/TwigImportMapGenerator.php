<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Service\Twig;

use Application\Exception\TwigHelperException;

class TwigImportMapGenerator
{
    /** @var array{js: array<string, string>, css: array<string, string>} $imports */
    private array $imports;

    public function __construct(
        private readonly ManifestLoader $manifestLoader,
        string $webAssetsPath,
        private readonly string $name,
    ) {
        $entrypoints          = $this->loadEntrypoints($webAssetsPath, $name);
        $this->imports['js']  = $this->loadImportMap($webAssetsPath, $entrypoints);
        $this->imports['css'] = $this->manifestLoader->load($webAssetsPath, ['.css']);
    }

    public function importmap(): string
    {
        return '        <script type="importmap" data-turbo-track="reload">' . "\n"
            . \json_encode(['imports' => $this->imports['js']], \JSON_PRETTY_PRINT | \JSON_UNESCAPED_SLASHES)
            . '</script>' . "\n"
        ;
    }

    public function css(): string
    {
        $css = [];
        foreach ($this->imports['css'] as $import) {
            $css[] = '        <link rel="stylesheet" href="' . $import . '">';
        }

        return \implode("\n", $css);
    }

    public function js(): string
    {
        $js = [];
        foreach ($this->imports['js'] as $import) {
            $js[] = '        <link rel="modulepreload" href="' . $import . '">';
        }

        $js[] = '        <script type="module" data-turbo-track="reload">import \'' . $this->name . '\' ;</script>';

        return \implode("\n", $js);
    }

    /**
     * @return list<string>
     */
    private function loadEntrypoints(string $webAssetsPath, string $name): array
    {
        $entrypointFile = "$webAssetsPath/entrypoint.$name.json";

        if (!\is_readable($entrypointFile)) {
            throw new TwigHelperException("entrypoint.$name.json file is not readable.", 1100);
        }

        try {
            /** @var list<string> $entrypoints */
            $entrypoints = \json_decode(
                (string) \file_get_contents($entrypointFile),
                true,
                flags: \JSON_THROW_ON_ERROR,
            );
            \array_unshift($entrypoints, $name); // prepend $name to the entrypoints list

            return $entrypoints;
        } catch (\JsonException $exception) {
            throw new TwigHelperException("Unable to decode entrypoint.$name.json file!", 1101, $exception);
        }
    }

    /**
     * @param list<string> $entrypoints
     * @return array<string, string>
     */
    private function loadImportMap(string $webAssetsPath, array $entrypoints): array
    {
        $importmapFile  = "$webAssetsPath/importmap.json";

        if (!\is_readable($importmapFile)) {
            throw new TwigHelperException("importmap.json file is not readable.", 1102);
        }

        try {
            /** @var array<string, array{path: string, type: string}> $importmap */
            $importmap = \json_decode(
                (string) \file_get_contents($importmapFile),
                true,
                flags: \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw new TwigHelperException('Unable to decode importmap.json file!', 1103, $exception);
        }

        $imports = [];
        foreach ($entrypoints as $entrypoint) {
            if (!isset($importmap[$entrypoint]) || $importmap[$entrypoint]['type'] !== 'js') {
                continue;
            }

            $imports[$entrypoint] = $importmap[$entrypoint]['path'];
        }

        return $imports;
    }
}
