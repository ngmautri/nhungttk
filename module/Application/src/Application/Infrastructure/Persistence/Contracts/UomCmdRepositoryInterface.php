<?php
namespace Application\Infrastructure\Persistence\Contracts;

use Application\Domain\Shared\Uom\UomSnapshot;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
Interface UomCmdRepositoryInterface
{

    public function store(UomSnapshot $snapshot);
}
