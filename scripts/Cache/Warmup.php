<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script\Cache;

use Application\Service\Twig\TwigCacheWarmer;
use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Option\Options;

/**
 * Class Liveness
 *
 * @author Romain Cottard
 */
class Warmup extends AbstractScript
{
    public function __construct(
        private readonly TwigCacheWarmer $twigCacheWarmer,
    ) {
        $this->setDescription('Cache Warmup');
        $this->setExecutable();

        $this->initOptions(new Options());
    }


    public function run(): void
    {
        echo "Warmup Cache for Twig...\n";
        $this->twigCacheWarmer->warmUp();
        echo "Done !\n";
    }
}
