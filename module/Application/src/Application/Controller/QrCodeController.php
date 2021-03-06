<?php
namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Doctrine\ORM\EntityManager;
use Endroid\QrCode\QrCode;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class QrCodeController extends AbstractActionController
{

    const QR_CODE_PATH = "/data/procure/qr_code/test.png";

    protected $doctrineEM;

    /*
     * Defaul Action
     */
    public function indexAction()
    {}

    /**
     */
    public function testAction()
    {
        $qrCode = new QrCode('inventory/item/show?token=8_oN_6VBBS_1fwkV71Q8xiqwqY1q4chr&entity_id=3377&checksum=fa0d9289a1a311dae94ad9d6b7117655');
        $qrCode->setSize(80);
        $qrCode->writeFile(getcwd() . self::QR_CODE_PATH);
        // header('Content-Type: '.$qrCode->getContentType());

        // echo $qrCode->writeString();
    }

    /**
     *
     * @return the $doctrineEM
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }

    /**
     *
     * @param field_type $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }
}
