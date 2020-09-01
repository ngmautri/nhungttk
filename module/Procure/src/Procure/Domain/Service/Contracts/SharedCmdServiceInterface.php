<?php
namespace Procure\Domain\Service\Contracts;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
interface SharedCmdServiceInterface
{

    public function getAPCmdRepository();

    public function getPRCmdRepository();

    public function getPOCmdRepository();

    public function getGRCmdRepository();

    public function getQRCmdRepository();
}
