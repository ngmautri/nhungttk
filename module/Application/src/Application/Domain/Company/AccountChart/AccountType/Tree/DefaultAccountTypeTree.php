<?php
namespace Application\Domain\Company\AccountChart\AccountType\Tree;

use Application\Domain\Company\Contracts\Account\AccountType;
use Application\Domain\Company\Contracts\Account\AssetType;
use Application\Domain\Company\Contracts\Account\LiabilityType;
use Application\Domain\Util\Tree\AbstractTree;

/**
 *
 * @author Nguyen Mau Tri
 *
 */
class DefaultAccountTypeTree extends AbstractTree
{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $n = new AccountTypeNode();
        $n->setId(AccountType::ROOT);
        $n->setNodeName(AccountType::ROOT);
        $n->setNodeCode(AccountType::ROOT);
        $this->data[AccountType::ROOT] = $n;

        // BS
        $n = new AccountTypeNode();
        $n->setId(AccountType::BALANCE_SHEET);
        $n->setNodeName("Balance Sheet");
        $n->setNodeCode(AccountType::BALANCE_SHEET);
        $this->data[AccountType::BALANCE_SHEET] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::CASH_BANK);
        $n->setNodeName("Cash and Bank");
        $n->setNodeCode(AssetType::CASH_BANK);
        $this->data[AssetType::CASH_BANK] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::RECEIVEABLE);
        $n->setNodeName("Receivable");
        $n->setNodeCode(AssetType::RECEIVEABLE);
        $this->data[AssetType::RECEIVEABLE] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::PREPAYMENT);
        $n->setNodeName("Prepaymnet");
        $n->setNodeCode(AssetType::PREPAYMENT);
        $this->data[AssetType::PREPAYMENT] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::INVENTORY);
        $n->setNodeName("Inventory");
        $n->setNodeCode(AssetType::INVENTORY);
        $this->data[AssetType::INVENTORY] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::SUPPLIES);
        $n->setNodeName("Supplies");
        $n->setNodeCode(AssetType::SUPPLIES);
        $this->data[AssetType::SUPPLIES] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::OTHER_CURRENT_ASSET);
        $n->setNodeName("Other current asset");
        $n->setNodeCode(AssetType::OTHER_CURRENT_ASSET);
        $this->data[AssetType::OTHER_CURRENT_ASSET] = $n;

        $n = new AccountTypeNode();
        $n->setId(AssetType::FIXED_ASSET);
        $n->setNodeName("Fixed Asset");
        $n->setNodeCode(AssetType::FIXED_ASSET);
        $this->data[AssetType::FIXED_ASSET] = $n;

        // BS
        $n = new AccountTypeNode();
        $n->setId(LiabilityType::PAYABLE);
        $n->setNodeName("Payable");
        $n->setNodeCode(LiabilityType::PAYABLE);
        $this->data[LiabilityType::PAYABLE] = $n;

        $this->index[AccountType::ROOT][] = AccountType::BALANCE_SHEET;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::CASH_BANK;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::RECEIVEABLE;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::PREPAYMENT;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::INVENTORY;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::SUPPLIES;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::OTHER_CURRENT_ASSET;
        $this->index[AccountType::BALANCE_SHEET][] = AssetType::FIXED_ASSET;

        $this->index[AccountType::BALANCE_SHEET][] = LiabilityType::PAYABLE;
    }
}