<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Entity\AppCoa;
use Application\Entity\AppCoaAccount;
use Application\Infrastructure\AggregateRepository\AbstractDoctrineRepository;
use Application\Infrastructure\Persistence\Domain\Doctrine\Mapper\ChartMapper;
use InvalidArgumentException;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
class ChartCmdRepositoryImpl extends AbstractDoctrineRepository implements ChartCmdRepositoryInterface
{

    const ROOT_ENTITY_NAME = "\Application\Entity\AppCoa";

    const DEPT_ENTITY_NAME = "\Application\Entity\AppCoaAccount";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::store()
     */
    public function store(BaseChart $rootEntity, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($rootEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_storeChart($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
        }

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();

        return $rootSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::storeAccount()
     */
    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new \InvalidArgumentException("Root entity not given.");
        }

        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new \RuntimeException("Doctrine root entity not found.");
        }

        $isFlush = true;
        $increaseVersion = true;
        $rowEntityDoctrine = $this->_storeAccount($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
        }

        $localSnapshot->id = $rowEntityDoctrine->getId();
        $localSnapshot->revisionNo = $rowEntityDoctrine->getRevisionNo();

        return $localSnapshot;
    }

    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given.");
        }

        /**
         *
         * @var AppCoa $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootEntity->getId());

        if ($rootEntityDoctrine == null) {
            throw new InvalidArgumentException("Doctrine root entity not found.");
        }

        /**
         *
         * @var AppCoaAccount $rowEntityDoctrine ;
         */
        $rowEntityDoctrine = $this->getDoctrineEM()->find(self::LOCAL_ENTITY_NAME, $localEntity->getId());

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localEntity->getId()));
        }

        //
        if ($rowEntityDoctrine->getCoa() == null) {
            throw new InvalidArgumentException("Doctrine row entity is not valid");
        }

        if ($rowEntityDoctrine->getCoa()->getId() != $rootEntity->getId()) {
            throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getInvoice()->getId(), $rootEntity->getId()));
        }

        $isFlush = true;

        // remove row.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    private function _storeAccount($rootEntityDoctrine, AccountSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion, $n = null)
    {

        /**
         *
         * @var \Application\Entity\AppCoaAccount $rowEntityDoctrine ;
         */
        if ($localSnapshot->getId() > 0) {

            $rowEntityDoctrine = $this->doctrineEM->find(self::LOCAL_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getCoa() == null) {
                throw new InvalidArgumentException("Doctrine row entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getCoa()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Doctrine row entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::LOCAL_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();

            // to update
            $rowEntityDoctrine->setInvoice($rootEntityDoctrine);
        }

        $rowEntityDoctrine = ChartMapper::mapAccountEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $rowEntityDoctrine;
    }

    /**
     *
     * @param ChartSnapshot $rootSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @throws InvalidArgumentException
     * @return \Application\Entity\AppCoa
     */
    private function _storeChart(ChartSnapshot $rootSnapshot, $isPosting, $isFlush, $increaseVersion)
    {

        /**
         *
         * @var \Application\Entity\AppCoa $entity ;
         *
         */
        if ($rootSnapshot->getId() > 0) {
            $entity = $this->getDoctrineEM()->find(self::ROOT_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }

            // just in case, it is not updated.
            if ($entity->getToken() == null) {
                $entity->setToken($entity->getUuid());
            }
        } else {
            $rootClassName = self::ROOT_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ChartMapper::mapChartEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return $entity;
    }

    private function _getRootSnapshot(BaseChart $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("Root entity not given!");
        }

        return $rootEntity->makeSnapshot();
    }

    /**
     *
     * @param BaseAccount $localEntity
     * @throws InvalidArgumentException
     * @return \Application\Domain\Company\AccountChart\BaseAccountSnapshot
     */
    private function _getLocalSnapshot(BaseAccount $localEntity)
    {
        if (! $localEntity instanceof BaseAccount) {
            throw new InvalidArgumentException("Local entity not given!");
        }
        return $localEntity->makeSnapshot();
    }

    public function remove(BaseChart $rootEntity, $isPosting = false)
    {}
}
