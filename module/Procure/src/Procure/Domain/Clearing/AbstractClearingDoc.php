<?php
namespace Procure\Domain\Clearing;

use Application\Domain\Shared\AbstractEntity;
use Application\Domain\Shared\AggregateRootInterface;

/**
 * Abstract Clearing Document.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractClearingDoc extends AbstractEntity implements AggregateRootInterface
{
}