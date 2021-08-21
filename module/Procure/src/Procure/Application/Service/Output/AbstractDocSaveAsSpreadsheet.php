<?php
namespace Procure\Application\Service\Output;

use Application\Application\Service\Document\Spreadsheet\AbstractBuilder;
use Application\Entity\NmtInventoryItemPicture;
use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Output\Contract\DocSaveAsInterface;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
abstract class AbstractDocSaveAsSpreadsheet implements DocSaveAsInterface
{

    protected $builder;
    protected $doctrineEM;
    
    protected function getItemPic($id)
    {
        
        /** @var \Application\Entity\NmtInventoryItemPicture $pic ;*/
        $pic = $this->getDoctrineEM()
        ->getRepository('Application\Entity\NmtInventoryItemPicture')
        ->findOneBy(array(
            'item' => $id,
            'isActive' => 1
        ));
        
        $thumbnail_file = '/images/no-pic1.jpg';
        if ($pic instanceof NmtInventoryItemPicture) {
            
            $thumbnail_file = "/thumbnail/item/" . $pic->getFolderRelative() . "thumbnail_200_" . $pic->getFileName();
            $thumbnail_file = str_replace('\\', '/', $thumbnail_file); // Important for UBUNTU
            
            return $thumbnail_file;
        }
        
        return $thumbnail_file;
    }
    
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

    /**
     *
     * @param AbstractBuilder $builder
     */
    public function __construct(AbstractBuilder $builder)
    {
        $this->builder = $builder;
    }

    /**
     *
     * @return \Application\Application\Service\Document\Spreadsheet\AbstractBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
    }
}
