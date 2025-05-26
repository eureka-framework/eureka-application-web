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

class ManifestLoader
{
    /**
     * @param list<string> $filterExtension
     * @return array<string, string>
     */
    public function load(string $webAssetsPath, array $filterExtension = []): array
    {
        $manifestFile = "$webAssetsPath/manifest.json";

        if (!\is_readable($manifestFile)) {
            throw new TwigHelperException("manifest.json file is not readable.", 1104);
        }

        try {
            /** @var array<string, string> $manifest */
            $manifest = \json_decode(
                (string) \file_get_contents($manifestFile),
                true,
                flags: \JSON_THROW_ON_ERROR,
            );
        } catch (\JsonException $exception) {
            throw new TwigHelperException("Unable to decode manifest.json file!", 1105, $exception);
        }

        if ($filterExtension !== []) {
            $manifest = \array_filter(
                $manifest,
                fn(string $name) => \str_ends_with($name, '.css'),
                ARRAY_FILTER_USE_KEY,
            );
        }

        return $manifest;
    }
}
