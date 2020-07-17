<?php
namespace Application\Domain\Util\Composite\Builder;

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
        /*
         * $this->data[1] = '1 _ROOT_';
         * $this->data[2] = 'SECTION I - LIVE ANIMALS; ANIMAL PRODUCTS';
         * $this->data[302] = 'SECTION II - VEGETABLE PRODUCTS';
         * $this->data[669] = 'SECTION III - ANIMAL OR VEGETABLE FATS AND OILS AND THEIR CLEAVAGE PRODUCTS; PREPARED EDIBLE FATS; ANIMAL OR VEGETABLE WAXES';
         * $this->data[3] = 'CHAPTER 1 - LIVE ANIMALS';
         * $this->data[38] = 'CHAPTER 2 - MEAT AND EDIBLE MEAT OFFAL';
         * $this->data[39] = 'Meat of bovine animals, fresh or chilled';
         * $this->data[40] = ' - Carcases and half-carcases';
         * $this->data[41] = ' - Other cuts with bone in';
         *
         * $this->index[1][] = 2;
         * $this->index[1][] = 302;
         * $this->index[1][] = 669;
         * $this->index[2][] = 3;
         * $this->index[2][] = 38;
         * $this->index[38][] = 39;
         * $this->index[39][] = 40;
         * $this->index[39][] = 41;
         */
    }
}
