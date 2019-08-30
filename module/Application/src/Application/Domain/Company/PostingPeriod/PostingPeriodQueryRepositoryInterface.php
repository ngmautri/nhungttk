<?php
namespace Application\Domain\Company\PostingPeriod;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
Interface PostingPeriodQueryRepositoryInterface
{
    public function getPostingPeriod($postingDate);
    public function getPostingPeriodStatus($postingDate);
    public function getLatestFX($sourceCurrencyId, $targetCurrencyId);
}
