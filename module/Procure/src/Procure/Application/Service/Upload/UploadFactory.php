<?php
namespace Procure\Application\Service\Upload;

use Doctrine\ORM\EntityManager;
use Procure\Domain\Contracts\ProcureDocType;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class UploadFactory
{

    /**
     *
     * {@inheritdoc}
     * @see \Zend\ServiceManager\FactoryInterface::createService()
     */
    static public function create($docType, EntityManager $doctrineEM)
    {
        $uploader = null;
        switch ($docType) {

            case ProcureDocType::PR:
                $uploader = new UploadPrRows($doctrineEM);
                break;
            case ProcureDocType::PO:
                $uploader = new UploadPoRows($doctrineEM);
                break;
            case ProcureDocType::INVOICE:
                $uploader = new UploadApRows($doctrineEM);
                break;
        }

        if ($uploader == null) {
            throw new \RuntimeException("Can not create Procure Uploader");
        }

        return $uploader;
    }
}