<?php

/*
 * Copyright (c) Deezer
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Script;

use Eureka\Component\Console\AbstractScript;
use Eureka\Component\Console\Option\Option;
use Eureka\Component\Console\Option\Options;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;

/**
 * Class Liveness
 *
 * @author Romain Cottard
 */
class Symfony extends AbstractScript
{
    private array $argv = [];
    /**
     * @param Command[] $commands
     */
    public function __construct(
        private readonly array $commands = [],
    ) {
        $this->setDescription('Symfony bridge command handler');
        $this->setExecutable();

        //~ Inject Symfony command as -c option
        $script  = \array_shift($_SERVER['argv']);
        $this->argv = $_SERVER['argv'];

        $command = \array_shift($_SERVER['argv']);
        $_SERVER['argv'] = [$script, 'symfony', '-c', $command];

        $this->initOptions(
            (new Options())
                ->add(new Option('c', 'command', ' Symfony command name', true, true)),
        );
    }

    public function run(): void
    {
        $command = $this->options()->value('command', 'c');

        if (!isset($this->commands[$command])) {
            throw new \UnexpectedValueException("Unkown symfony command '$command'");
        }

        $input  = new ArgvInput($this->argv);
        $output = new ConsoleOutput();
        $this->commands[$command]->run($input, $output);
    }
}
