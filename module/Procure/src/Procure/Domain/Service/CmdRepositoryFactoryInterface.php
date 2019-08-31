<?php
namespace Procure\Domain\Service;


/**
 * Transaction Domain Service
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface CmdRepositoryFactoryInterface
{
    public function createPRCmdRepository();
    public function createQRCmdRepository();
    public function createPOCmdRepository();    
    public function createAPCmdRepository();
}
