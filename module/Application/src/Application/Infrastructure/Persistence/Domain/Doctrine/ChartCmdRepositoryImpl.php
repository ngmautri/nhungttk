<?php
namespace Application\Infrastructure\Persistence\Domain\Doctrine;

use Application\Domain\Company\BaseCompany;
use Application\Domain\Company\AccountChart\AccountSnapshot;
use Application\Domain\Company\AccountChart\BaseAccount;
use Application\Domain\Company\AccountChart\BaseChart;
use Application\Domain\Company\AccountChart\ChartSnapshot;
use Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface;
use Application\Entity\AppCoa;
use Application\Entity\AppCoaAccount;
use Application\Entity\NmtApplicationCompany;
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

    const COMPANNY_ENTITY_NAME = "\Application\Entity\NmtApplicationCompany";

    const CHART_ENTITY_NAME = "\Application\Entity\AppCoa";

    const ACCOUNT_ENTITY_NAME = "\Application\Entity\AppCoaAccount";

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::store()
     */
    public function store(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_storeChart($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        return $rootSnapshot;
    }

    public function storeAll(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {
        $rootSnapshot = $this->_getRootSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $entity = $this->_storeChart($rootSnapshot, $isPosting, $isFlush, $increaseVersion);

        $rootSnapshot->id = $entity->getId();
        $rootSnapshot->revisionNo = $entity->getRevisionNo();
        $rootSnapshot->version = $entity->getVersion();

        $accountCollection = $localEntity->getAccountCollection();
        if ($accountCollection->isEmpty()) {
            return $rootSnapshot;
        }

        $increaseVersion = false;
        $isFlush = false;
        $n = 0;

        foreach ($accountCollection as $accountEntity) {
            $n ++;

            // flush every 500 line, if big doc.
            if ($n % 500 == 0) {
                $this->doctrineEM->flush();
            }

            $localSnapshot = $this->_getLocalSnapshot($accountEntity);
            $this->_storeAccount($entity, $localSnapshot, $isPosting, $isFlush, $increaseVersion);
        }

        $this->doctrineEM->flush();

        return $rootSnapshot;
    }

    public function remove(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {}

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::storeAccount()
     */
    public function storeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnChart($rootEntity);
        $localSnapshot = $this->_getLocalSnapshot($localEntity);

        $isFlush = true;
        $increaseVersion = true;
        $rowEntityDoctrine = $this->_storeAccount($rootEntityDoctrine, $localSnapshot, $isPosting, $isFlush, $increaseVersion);

        $localSnapshot->id = $rowEntityDoctrine->getId();
        // $localSnapshot->revisionNo = $rowEntityDoctrine->getV();

        return $localSnapshot;
    }

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Company\AccountChart\Repository\ChartCmdRepositoryInterface::removeAccount()
     */
    public function removeAccount(BaseChart $rootEntity, BaseAccount $localEntity, $isPosting = false)
    {
        $rootEntityDoctrine = $this->assertAndReturnChart($rootEntity);

        $localSnapshot = $this->_getLocalSnapshot($localEntity);
        $rowEntityDoctrine = $this->assertAndReturnAccount($rootEntityDoctrine, $localSnapshot);

        $isFlush = true;

        // remove row.
        $this->getDoctrineEM()->remove($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        return true;
    }

    /**
     *
     * @param AppCoa $rootEntityDoctrine
     * @param AccountSnapshot $localSnapshot
     * @param boolean $isPosting
     * @param boolean $isFlush
     * @param boolean $increaseVersion
     * @return NULL|\Application\Entity\AppCoaAccount
     */
    private function _storeAccount(AppCoa $rootEntityDoctrine, AccountSnapshot $localSnapshot, $isPosting, $isFlush, $increaseVersion)
    {
        $rowEntityDoctrine = $this->assertAndReturnAccount($rootEntityDoctrine, $localSnapshot);

        $rowEntityDoctrine = ChartMapper::mapAccountEntity($this->getDoctrineEM(), $localSnapshot, $rowEntityDoctrine);

        $this->doctrineEM->persist($rowEntityDoctrine);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($rowEntityDoctrine == null) {
            throw new \RuntimeException("Something wrong. Row Doctrine Entity not created");
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
            $entity = $this->getDoctrineEM()->find(self::CHART_ENTITY_NAME, $rootSnapshot->getId());
            if ($entity == null) {
                throw new InvalidArgumentException(sprintf("Doctrine entity not found. %s", $rootSnapshot->getId()));
            }
        } else {
            $rootClassName = self::CHART_ENTITY_NAME;
            $entity = new $rootClassName();
        }

        // Populate with data
        $entity = ChartMapper::mapChartEntity($this->getDoctrineEM(), $rootSnapshot, $entity);

        $this->doctrineEM->persist($entity);

        if ($isFlush) {
            $this->doctrineEM->flush();
        }

        if ($entity == null) {
            throw new InvalidArgumentException("Something wrong. Doctrine root entity not created");
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

    /**
     *
     * @param BaseCompany $rootEntity
     * @throws InvalidArgumentException
     */
    private function assertAndReturnCompany(BaseCompany $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseCompany not given.");
        }

        /**
         *
         * @var NmtApplicationCompany $rowEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::COMPANNY_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof NmtApplicationCompany) {
            throw new InvalidArgumentException("Doctrine root entity not given!");
        }

        return $rootEntityDoctrine;
    }

    private function assertAndReturnChart(BaseChart $rootEntity)
    {
        if ($rootEntity == null) {
            throw new InvalidArgumentException("BaseChart not given.");
        }

        /**
         *
         * @var AppCoa $rootEntityDoctrine ;
         */
        $rootEntityDoctrine = $this->getDoctrineEM()->find(self::CHART_ENTITY_NAME, $rootEntity->getId());
        if (! $rootEntityDoctrine instanceof AppCoa) {
            throw new InvalidArgumentException("Account chart entity not found!");
        }

        return $rootEntityDoctrine;
    }

    private function assertAndReturnAccount(AppCoa $rootEntityDoctrine, AccountSnapshot $localSnapshot)
    {
        $rowEntityDoctrine = null;

        if ($localSnapshot == null) {
            throw new InvalidArgumentException(sprintf("Account snapshot not found! #%s", ""));
        }

        if ($localSnapshot->getId() > 0) {

            /**
             *
             * @var AppCoaAccount $rowEntityDoctrine ;
             */
            $rowEntityDoctrine = $this->doctrineEM->find(self::ACCOUNT_ENTITY_NAME, $localSnapshot->getId());

            if ($rowEntityDoctrine == null) {
                throw new InvalidArgumentException(sprintf("Account entity not found! #%s", $localSnapshot->getId()));
            }

            // to update
            if ($rowEntityDoctrine->getCoa() == null) {
                throw new InvalidArgumentException("Account entity is not valid");
            }

            // to update
            if (! $rowEntityDoctrine->getCoa()->getId() == $rootEntityDoctrine->getId()) {
                throw new InvalidArgumentException(sprintf("Account entity is corrupted! %s <> %s ", $rowEntityDoctrine->getGr()->getId(), $rootEntityDoctrine->getId()));
            }
        } else {
            $localClassName = self::ACCOUNT_ENTITY_NAME;
            $rowEntityDoctrine = new $localClassName();
            // to update
            $rowEntityDoctrine->setCoa($rootEntityDoctrine);
        }

        if ($rowEntityDoctrine == null) {
            throw new InvalidArgumentException("Can not create account  entity!");
        }

        return $rowEntityDoctrine;
    }

    public function removeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {}

    public function storeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {}

    public function storeWholeChart(BaseCompany $rootEntity, BaseChart $localEntity, $isPosting = false)
    {}
}
