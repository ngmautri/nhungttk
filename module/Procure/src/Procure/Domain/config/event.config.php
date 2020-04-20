<?php
use Procure\Application\Event\Handler\AP\ApPostedHandler;
use Procure\Application\Event\Handler\AP\ApReversedHandler;
use Procure\Application\Event\Handler\GR\GrFromApPosted;
use Procure\Application\Event\Handler\GR\GrFromApReversalPosted;
use Procure\Domain\Event\Ap\ApPosted;
use Procure\Domain\Event\Ap\ApReversed;

/**
 * Configuration file.
 *
 * @author Ngyuyen Mau Tri
 */
return [
    ApPosted::class => [
        ApPostedHandler::class,
        GrFromApPosted::class
    ],
    ApReversed::class => [
        ApReversedHandler::class,
        GrFromApReversalPosted::class
    ]
];
