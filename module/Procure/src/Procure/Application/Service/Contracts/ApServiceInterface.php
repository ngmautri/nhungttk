<?php
namespace Procure\Application\Service\Contracts;

use Application\Domain\Shared\Command\CommandOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
interface ApServiceInterface extends ProcureServiceInterface
{

    public function createFromPO($id, $token, CommandOptions $options);
}
