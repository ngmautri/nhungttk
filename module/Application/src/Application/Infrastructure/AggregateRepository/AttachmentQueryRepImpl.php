<?php
namespace Application\Infrastructure\AggregateRepository;

use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query\ResultSetMappingBuilder;
use Application\Infrastructure\Mapper\AttachmentMapper;
use Application\Domain\Attachment\AttachmentSnapshot;
use Application\Domain\Attachment\AttachmentQueryRepInterface;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class AttachmentQueryRepImpl extends AbstractDoctrineRepository implements AttachmentQueryRepInterface
{

    /**
     *
     * @param int $id
     * @return NULL|NULL|\Application\Domain\Attachment\AttachmentSnapshot
     */
    public function getById($id)
    {
        $entity = $this->getDoctrineEM()
            ->getRepository('Application\Entity\NmtApplicationAttachment')
            ->find($id);
        $snaphshot = AttachmentMapper::createSnapshot($entity, new AttachmentSnapshot());

        if ($snaphshot == null) {
            return null;
        }

        return $snaphshot;
    }

    public function findAll()
    {}

    /**
     *
     * @param int $id
     * @return array|NULL
     */
    private function _getById($id)
    {
        $sql1 = "select * from nmt_application_attachment where id= %s";
        $sql = sprintf($sql1, $id);

        $sql = $sql . ";";

        // echo $sql;

        try {
            $rsm = new ResultSetMappingBuilder($this->getDoctrineEM());
            $rsm->addRootEntityFromClassMetadata('\Application\Entity\NmtApplicationAttachment', 'nmt_application_attachment');

            $query = $this->getDoctrineEM()->createNativeQuery($sql, $rsm);
            $result = $query->getSingleResult();
            return $result;
        } catch (NoResultException $e) {
            return null;
        }
    }
}
