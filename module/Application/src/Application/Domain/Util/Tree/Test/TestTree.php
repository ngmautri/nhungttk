<?php
namespace Application\Domain\Util\Tree\Test;

use Application\Domain\Util\Tree\AbstractTree;
use Application\Domain\Util\Tree\Node\GenericNode;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class TestTree extends AbstractTree

{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Tree\AbstractTree::initTree()
     */
    public function initTree()
    {
        $genericNode = new GenericNode();
        $genericNode->setId(1);
        $genericNode->setNodeName('_ROOT_');
        $this->data[1] = $genericNode;

        $genericNode = new GenericNode();
        $genericNode->setId(2);
        $genericNode->setParentId(1);
        $genericNode->setNodeName('SECTION I - LIVE ANIMALS; ANIMAL PRODUCTS');
        $this->data[2] = $genericNode;
        // $this->data[2] = 'SECTION I - LIVE ANIMALS; ANIMAL PRODUCTS';

        $genericNode = new GenericNode();
        $genericNode->setId(302);
        $genericNode->setParentId(1);
        $genericNode->setNodeName('SECTION II - VEGETABLE PRODUCTS');
        $this->data[302] = $genericNode;
        // $this->data[302] = 'SECTION II - VEGETABLE PRODUCTS';

        $genericNode = new GenericNode();
        $genericNode->setId(669);
        $genericNode->setParentId(1);
        $genericNode->setNodeName('SECTION III - ANIMAL OR VEGETABLE FATS AND OILS AND THEIR CLEAVAGE PRODUCTS; PREPARED EDIBLE FATS; ANIMAL OR VEGETABLE WAXES');
        $this->data[669] = $genericNode;
        // $this->data[669] = 'SECTION III - ANIMAL OR VEGETABLE FATS AND OILS AND THEIR CLEAVAGE PRODUCTS; PREPARED EDIBLE FATS; ANIMAL OR VEGETABLE WAXES';

        $genericNode = new GenericNode();
        $genericNode->setId(3);
        $genericNode->setParentId(2);
        $genericNode->setNodeName('CHAPTER 1 - LIVE ANIMALS');
        $this->data[3] = $genericNode;
        // $this->data[3] = 'CHAPTER 1 - LIVE ANIMALS';

        $genericNode = new GenericNode();
        $genericNode->setId(38);
        $genericNode->setParentId(2);
        $genericNode->setNodeName('CHAPTER 2 - MEAT AND EDIBLE MEAT OFFAL');
        $this->data[38] = $genericNode;
        // $this->data[38] = 'CHAPTER 2 - MEAT AND EDIBLE MEAT OFFAL';

        $genericNode = new GenericNode();
        $genericNode->setId(39);
        $genericNode->setParentId(38);
        $genericNode->setNodeName('Meat of bovine animals, fresh or chilled');
        $this->data[39] = $genericNode;
        // $this->data[39] = 'Meat of bovine animals, fresh or chilled';

        $genericNode = new GenericNode();
        $genericNode->setId(40);
        $genericNode->setParentId(39);
        $genericNode->setNodeName(' - Carcases and half-carcases');
        $this->data[40] = $genericNode;
        // $this->data[40] = ' - Carcases and half-carcases';

        $genericNode = new GenericNode();
        $genericNode->setId(41);
        $genericNode->setParentId(39);
        $genericNode->setNodeName(' - Other cuts with bone in');
        $this->data[41] = $genericNode;

        $genericNode = new GenericNode();
        $genericNode->setId(41);
        $genericNode->setParentId(39);
        $genericNode->setNodeName(' -Other cuts with bone in');
        $this->data[42] = $genericNode;
        // $this->data[41] = ' - Other cuts with bone in';

        $this->index[1][] = 2;
        $this->index[1][] = 302;
        $this->index[1][] = 669;
        $this->index[2][] = 3;
        $this->index[2][] = 38;
        $this->index[38][] = 39;
        $this->index[39][] = 40;
        $this->index[39][] = 41;
        $this->index[39][] = 42;
    }
}
