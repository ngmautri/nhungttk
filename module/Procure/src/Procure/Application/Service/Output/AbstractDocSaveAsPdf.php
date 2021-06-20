<?php
namespace Procure\Application\Service\Output;

use Application\Application\Service\Document\Pdf\AbstractBuilder;
use Doctrine\ORM\EntityManager;
use Procure\Application\Service\Output\Contract\DocSaveAsInterface;

/**
 * Director in Builder Pattern.
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class AbstractDocSaveAsPdf implements DocSaveAsInterface
{

    protected $builder;

    protected $doctrineEM;

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
     * @return \Application\Application\Service\Document\Pdf\AbstractBuilder
     */
    public function getBuilder()
    {
        return $this->builder;
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
}
