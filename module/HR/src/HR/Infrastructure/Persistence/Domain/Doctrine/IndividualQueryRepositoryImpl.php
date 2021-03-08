<?php
namespace HR\Infrastructure\Persistence\Domain\Doctrine;

use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use HR\Domain\Employee\Factory\IndividualFactory;
use HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface;
use HR\Infrastructure\Persistence\Domain\Doctrine\Mapper\IndividualMapper;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class IndividualQueryRepositoryImpl extends AbstractDoctrineRepository implements IndividualQueryRepositoryInterface
{

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface::getVersion()
     */
    public function getVersion($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\HrIndividual $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\HrIndividual')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return $doctrineEntity->getRevisionNo();
        }
        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface::getVersionArray()
     */
    public function getVersionArray($id, $token = null)
    {
        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\HrIndividual $doctrineEntity ;
         */

        $doctrineEntity = $this->doctrineEM->getRepository('\Application\Entity\HrIndividual')->findOneBy($criteria);
        if ($doctrineEntity !== null) {
            return [
                "revisionNo" => $doctrineEntity->getRevisionNo(),
                "version" => $doctrineEntity->getRevisionNo()
            ];
        }

        return null;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface::getRootEntityByTokenId()
     */
    public function getRootEntityByTokenId($id, $token)
    {
        if ($id == null || $token == null) {
            return null;
        }

        $criteria = array(
            'id' => $id,
            'token' => $token
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\HrIndividual')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = IndividualMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = IndividualFactory::contructFromDB($rootSnapshot);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface::getRootEntityById()
     */
    public function getRootEntityById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\HrIndividual')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = IndividualMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = IndividualFactory::contructFromDB($rootSnapshot);
        return $rootEntity;
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface::getIndividualTypeById()
     */
    public function getIndividualTypeById($id)
    {
        if ($id == null) {
            return null;
        }

        $criteria = array(
            'id' => $id
        );

        /**
         *
         * @var \Application\Entity\HrIndividual $doctrineEntity ;
         */
        $doctrineEntity = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\HrIndividual')
            ->findOneBy($criteria);

        if ($doctrineEntity == null) {
            return null;
        }

        return $doctrineEntity->getIndividualType();
    }

    /**
     *
     * {@inheritdoc}
     * @see \HR\Domain\Employee\Repository\IndividualQueryRepositoryInterface::getByEmployeeCode()
     */
    public function getByEmployeeCode($code)
    {
        if ($code == null) {
            return null;
        }

        $criteria = array(
            'employeeCode' => $code
        );

        $rootEntityDoctrine = $this->getDoctrineEM()
            ->getRepository('\Application\Entity\HrIndividual')
            ->findOneBy($criteria);

        if ($rootEntityDoctrine == null) {
            return null;
        }

        $rootSnapshot = IndividualMapper::createSnapshot($rootEntityDoctrine, null, true);
        if ($rootSnapshot == null) {
            return null;
        }

        $rootEntity = IndividualFactory::contructFromDB($rootSnapshot);
        return $rootEntity;
    }
}
