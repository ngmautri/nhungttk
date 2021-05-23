<?php
namespace Inventory\Domain\Item\Variant;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Event\Company\AccountChart\AccountCreated;
use Application\Domain\Shared\Assembler\GenericObjectAssembler;
use Application\Domain\Shared\Command\CommandOptions;
use Doctrine\Common\Collections\ArrayCollection;
use Inventory\Domain\Item\Repository\ItemCmdRepositoryInterface;
use Inventory\Domain\Item\ValueObject\VariantCode;
use Inventory\Domain\Item\Variant\Validator\VariantValidatorFactory;
use Inventory\Domain\Service\Contracts\SharedServiceInterface;
use Inventory\Domain\Service\Contracts\VariantValidationServiceInterface;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Webmozart\Assert\Assert;
use Closure;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *
 */
abstract class BaseVariant extends AbstractVariant
{

    private $attributeCollection;

    private $attributeCollectionRef;

    protected $variantCodeVO;

    public function createAttributeFrom(VariantAttributeSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        Assert::notNull($snapshot, "VariantAttributeSnapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = VariantValidatorFactory::forCreatingVariantAttribute($sharedService);
        $snapshot->init($options);
        $snapshot->setVariant($this->getId()); // important;
        $attr = GenericVariantAttribute::createFromSnapshot($this, $snapshot);

        $this->validateAttribute($attr, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        $this->clearEvents();
        $this->getAttributeCollection()->add($attr);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var APRowSnapshot $localSnapshot ;
         * @var ItemCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAttribute($this, $attr);

        $params = [
            "rowId" => $localSnapshot->getId(),
            "rowToken" => $localSnapshot->getToken()
        ];

        $target = $this->makeSnapshot();
        $defaultParams = new DefaultParameter();
        $defaultParams->setTargetId($this->getId());
        $defaultParams->setTargetToken($this->getToken());
        // $defaultParams->setTargetDocVersion($this->getDocVersion());
        // $defaultParams->setTargetRrevisionNo($this->getRevisionNo());
        $defaultParams->setTriggeredBy($options->getTriggeredBy());
        $defaultParams->setUserId($options->getUserId());

        $event = new AccountCreated($target, $defaultParams, $params);
        $this->addEvent($event);

        return $localSnapshot;
    }

    public function createVariantCodeVO($itemId, $attributes)
    {
        $this->variantCodeVO = new VariantCode($itemId, $attributes);
    }

    /**
     *
     * @param VariantValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validate(VariantValidationServiceInterface $validatorService, $isPosting = false)
    {
        $this->validateVariant($validatorService);
        foreach ($this->getAttributeCollection() as $attr) {
            $this->validateAttribute($attr, $validatorService);
        }
    }

    /**
     *
     * @param VariantValidationServiceInterface $validatorService
     * @param boolean $isPosting
     */
    public function validateVariant(VariantValidationServiceInterface $validatorService, $isPosting = false)
    {
        $validatorService->getVariantValidators()->validate($this);

        if ($this->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @param BaseVariantAttribute $attr
     * @param VariantValidationServiceInterface $validationService
     * @param boolean $isPosting
     */
    public function validateAttribute(BaseVariantAttribute $attr, VariantValidationServiceInterface $validationService, $isPosting = false)
    {
        if ($validationService->getVariantAttributeValidators() == null) {
            return;
        }
        $validationService->getVariantAttributeValidators()->validate($this, $attr);
        if ($attr->hasErrors()) {
            $this->addErrorArray($this->getErrors());
        }
    }

    /**
     *
     * @param BaseVariant $other
     * @return boolean
     */
    public function equals(BaseVariant $other)
    {
        if ($other == null) {
            return false;
        }

        return $this->getVariantCodeVO()->equals($other->getVariantCodeVO());
    }

    /**
     *
     * @return \Inventory\Domain\Item\Variant\VariantSnapshot
     */
    public function makeSnapshot()
    {
        $snapshot = new VariantSnapshot();
        GenericObjectAssembler::updateAllFieldsFrom($snapshot, $this);
        return $snapshot;
    }

    /**
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getAttributeCollection()
    {
        return $this->attributeCollection;
    }

    /**
     *
     * @param ArrayCollection $attributeCollection
     */
    public function setAttributeCollection(ArrayCollection $attributeCollection)
    {
        $this->attributeCollection = $attributeCollection;
    }

    /**
     *
     * @return Closure
     */
    public function getAttributeCollectionRef()
    {
        return $this->attributeCollectionRef;
    }

    /**
     *
     * @param Closure $attributeCollectionRef
     */
    public function setAttributeCollectionRef(Closure $attributeCollectionRef)
    {
        $this->attributeCollectionRef = $attributeCollectionRef;
    }

    /**
     *
     * @return \Inventory\Domain\Item\ValueObject\VariantCode
     */
    public function getVariantCodeVO()
    {
        return $this->variantCodeVO;
    }
}
