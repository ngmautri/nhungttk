<?php
namespace Procure\Application\Service\Contracts;

use Application\Domain\Shared\Command\CommandOptions;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface PoServiceInterface extends ProcureServiceInterface
{

    public function createFromQuotation($id, $token, CommandOptions $options);

    public function getDocMap($id);
}
