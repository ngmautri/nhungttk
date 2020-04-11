<?php
namespace Procure\Infrastructure\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface;
use Procure\Infrastructure\Mapper\GrMapper;
use Procure\Domain\GoodsReceipt\GRDoc;
use Procure\Domain\PurchaseOrder\PODoc;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GRQueryRepositoryImpl extends AbstractDoctrineRepository implements GrQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcureGr $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getHeaderById()
     */
    public function getHeaderById($id, $token = null)
    {
        if ($token == null) {
            $criteria = array(
                'id' => $id
            );
        } else {
            $criteria = array(
                'id' => $id,
                'token' => $token
            );
        }

        /**
         *
         * @var \Application\Entity\NmtProcureGr $entity ;
         */
        $entity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        $snapshot = GrMapper::createDetailSnapshot($entity);

        if ($snapshot == null) {
            return null;
        }

        return GRDoc::makeFromSnapshot($snapshot);
    }

    /**
     *
     * {@inheritdoc}
     * @see \Procure\Domain\GoodsReceipt\Repository\GrQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\NmtProcureGr $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\NmtProcureGr')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "docVersion" => $doctrineEntity->getDocVersion()
            ];
        }

        return null;
    }

    public function getHeaderDTO($id, $token = null)
    {}

    public function getPODetailsById($id, $token = null)
    {}

    public function getById($id, $outputStragegy = null)
    {}

    public function getByUUID($uuid)
    {}

    public function findAll()
    {}
}
