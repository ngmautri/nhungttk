<?php
namespace Application\Domain\Util\Composite\Builder;

use Application\Domain\Util\Composite\GenericComponent;

/**
 *
 * @author Nguyen Mau Tri
 *        
 */
class TestBuilder extends AbstractBuilder

{

    /**
     *
     * {@inheritdoc}
     * @see \Application\Domain\Util\Composite\Builder\AbstractBuilder::initCategory()
     */
    public function initCategory()
    {
        $genericComponent = new GenericComponent();
        $genericComponent->setId(1);
        $genericComponent->setComponentName('_ROOT_');
        $this->data[1] = $genericComponent;

        $genericComponent = new GenericComponent();
        $genericComponent->setId(2);
        $genericComponent->setParenId(1);
        $genericComponent->setComponentName('SECTION I - LIVE ANIMALS; ANIMAL PRODUCTS');
        $this->data[2] = $genericComponent;
        // $this->data[2] = 'SECTION I - LIVE ANIMALS; ANIMAL PRODUCTS';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(302);
        $genericComponent->setParenId(1);
        $genericComponent->setComponentName('SECTION II - VEGETABLE PRODUCTS');
        $this->data[302] = $genericComponent;
        // $this->data[302] = 'SECTION II - VEGETABLE PRODUCTS';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(669);
        $genericComponent->setParenId(1);
        $genericComponent->setComponentName('SECTION III - ANIMAL OR VEGETABLE FATS AND OILS AND THEIR CLEAVAGE PRODUCTS; PREPARED EDIBLE FATS; ANIMAL OR VEGETABLE WAXES');
        $this->data[669] = $genericComponent;
        // $this->data[669] = 'SECTION III - ANIMAL OR VEGETABLE FATS AND OILS AND THEIR CLEAVAGE PRODUCTS; PREPARED EDIBLE FATS; ANIMAL OR VEGETABLE WAXES';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(3);
        $genericComponent->setParenId(2);
        $genericComponent->setComponentName('CHAPTER 1 - LIVE ANIMALS');
        $this->data[3] = $genericComponent;
        // $this->data[3] = 'CHAPTER 1 - LIVE ANIMALS';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(38);
        $genericComponent->setParenId(2);
        $genericComponent->setComponentName('CHAPTER 2 - MEAT AND EDIBLE MEAT OFFAL');
        $this->data[38] = $genericComponent;
        // $this->data[38] = 'CHAPTER 2 - MEAT AND EDIBLE MEAT OFFAL';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(39);
        $genericComponent->setParenId(38);
        $genericComponent->setComponentName('Meat of bovine animals, fresh or chilled');
        $this->data[39] = $genericComponent;
        // $this->data[39] = 'Meat of bovine animals, fresh or chilled';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(40);
        $genericComponent->setParenId(39);
        $genericComponent->setComponentName(' - Carcases and half-carcases');
        $this->data[40] = $genericComponent;
        // $this->data[40] = ' - Carcases and half-carcases';

        $genericComponent = new GenericComponent();
        $genericComponent->setId(41);
        $genericComponent->setParenId(39);
        $genericComponent->setComponentName(' - Other cuts with bone in');
        $this->data[41] = $genericComponent;

        $genericComponent = new GenericComponent();
        $genericComponent->setId(41);
        $genericComponent->setParenId(39);
        $genericComponent->setComponentName(' -Other cuts with bone in');
        $this->data[42] = $genericComponent;
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
