<?php
namespace Application\Domain\Attachment;

use Application\Application\Event\DefaultParameter;
use Application\Domain\Company\AccountChart\GenericAccount;
use Application\Domain\Company\AccountChart\Tree\AccountChartNode;
use Application\Domain\Company\AccountChart\Validator\ChartValidatorFactory;
use Application\Domain\Company\Repository\CompanyCmdRepositoryInterface;
use Application\Domain\Event\Company\AccountChart\AccountCreated;
use Application\Domain\Service\Contracts\SharedServiceInterface;
use Application\Domain\Shared\Command\CommandOptions;
use Procure\Domain\AccountPayable\APRowSnapshot;
use Webmozart\Assert\Assert;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class GenericAttachment extends AbstractAttachment
{

    public function createFileFrom(\AttachmentFileSnapshot $snapshot, CommandOptions $options, SharedServiceInterface $sharedService, $storeNow = true)
    {
        // Assert::notEq($this->getS(), AccountChartStatus::ACTIVE, sprintf("Account chart is not active! %s", $this->getId()));
        Assert::notNull($snapshot, "Account Snapshot not founds");
        Assert::notNull($options, "Options not founds");

        $validationService = ChartValidatorFactory::forCreatingChart($sharedService);
        $snapshot->init($options);
        $snapshot->setCoa($this->getId()); // important;
        $account = GenericAccount::createFromSnapshot($this, $snapshot);

        $this->validateAccount($account, $validationService);

        if ($this->hasErrors()) {
            throw new \RuntimeException($this->getNotification()->errorMessage());
        }

        // Add to tree, in order to check.
        // create node
        $chartTree = $this->createChartTree();
        $root = $chartTree->getRoot();

        $parent = $root;
        if ($root->getNodeByCode($account->getParentAccountNumber()) != null) {
            $parent = $root->getNodeByCode($account->getParentAccountNumber());
        }

        $node = new AccountChartNode();

        $node->setParentCode($snapshot->getParentAccountNumber());
        $node->setParentId($parent->getId());

        $node->setContextObject($snapshot);
        $node->setId($snapshot->getAccountNumber());
        $node->setNodeCode($snapshot->getAccountNumber());
        $node->setNodeName($snapshot->getAccountName());

        // var_dump($root->isNodeDescendant($node));

        $node->setParentId($parent->getId());

        // $parent->add($node); // adding to tree. If ok, go further
        $chartTree->insertAccount($node, $parent, $options);

        $this->clearEvents();
        $this->getAccountCollection()->add($account);

        if (! $storeNow) {
            return $this;
        }

        /**
         *
         * @var APRowSnapshot $localSnapshot ;
         * @var CompanyCmdRepositoryInterface $rep ;
         */
        $rep = $sharedService->getPostingService()->getCmdRepository();
        $localSnapshot = $rep->storeAccount($this, $account);

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
}