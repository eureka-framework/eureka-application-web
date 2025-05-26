<?php

/*
 * Copyright (c) Romain Cottard
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Application\Middleware;

use Application\Service\Twig\TwigConfigurator;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Twig;

/**
 * Class TwigMiddleware
 *
 * @author Romain Cottard
 */
readonly class TwigMiddleware implements MiddlewareInterface
{
    public function __construct(
        private TwigConfigurator $twigConfigurator,
    ) {}

    /**
     * Process an incoming server request and return a response, optionally delegating
     * response creation to a handler.
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        $this->twigConfigurator->getPaths();

        return $handler->handle($request);
    }
}
