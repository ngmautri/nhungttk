<?php
namespace Procure\Application\Service;

use Application\Service\AbstractService;
use Procure\Domain\Service\CmdRepositoryFactoryInterface;
use Procure\Infrastructure\Doctrine\DoctrineAPDocCmdRepository;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class CmdRepositoryFactory extends AbstractService implements CmdRepositoryFactoryInterface
{

    public function createPOCmdRepository()
    {}

    public function createPRCmdRepository()
    {}

    public function createQRCmdRepository()
    {}

    /*
     *
     */
    public function createAPCmdRepository()
    {
        return new DoctrineAPDocCmdRepository($this->getDoctrineEM());
    }
}
