<?php
namespace Application\Application\Service\Document\Spreadsheet;

use Application\Application\Service\Document\DocumentBuilderInterface;
use Application\Entity\NmtInventoryItemPicture;
use Doctrine\ORM\EntityManager;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractBuilder implements DocumentBuilderInterface
{

    protected $fileName;

    protected $phpSpreadSheet;
    

    
    /**
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getDoctrineEM()
    {
        return $this->doctrineEM;
    }
    
    /**
     *
     * @param EntityManager $doctrineEM
     */
    public function setDoctrineEM(EntityManager $doctrineEM)
    {
        $this->doctrineEM = $doctrineEM;
    }

    public function __construct()
    {
        $this->phpSpreadSheet = new Spreadsheet();
    }

    /**
     *
     * @return \PhpOffice\PhpSpreadsheet\Spreadsheet
     */
    public function getPhpSpreadSheet()
    {
        return $this->phpSpreadSheet;
    }

    /**
     *
     * @return mixed
     */
    public function getFileName()
    {
        return $this->fileName;
    }

    /**
     *
     * @param mixed $fileName
     */
    public function setFileName($fileName)
    {
        $this->fileName = $fileName;
    }
    
   
}
