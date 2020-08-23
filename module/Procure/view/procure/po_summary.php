
<hr>
<span style="color: gray; margin: 2pt 2pt 2pt 8pt"><?php
echo $this->translate('Summary:');
?></span>
<div class="alert alert-info" role="alert" style="padding: 2px; margin: 2pt 2pt 2pt 8pt; font-size: 9.2pt">
   <ul style="font-size: 9pt; margin: 0; padding-left: 15pt">
      <li>
         <b><?php

        if (! $headerDTO == null) :
            echo $headerDTO->getVendorName(); endif;

        ?></b>
      </li>
      <li>-----</li>
      <li><?php

    if (! $headerDTO == null) :
        echo $headerDTO->getIncoterm() . " " . $headerDTO->getIncotermPlace(); endif;

    ?> </li>
      <li>Total rows:<?php

    if (! $headerDTO == null) :
        echo $headerDTO->getTotalRows(); endif;

    ?></li>
      <li>Status:<?php

    if (! $headerDTO == null) :
        echo $headerDTO->getTransactionStatus(); endif;

    ?> </li>
      <li>-----</li>
      <li>Net: <?php

    if (! $headerDTO == null) :
        echo number_format($headerDTO->getNetAmount(), 2) . " " . $headerDTO->getCurrencyIso3(); endif;

    ?></li>
      <li>Tax: <?php

    if (! $headerDTO == null) :
        echo number_format($headerDTO->getTaxAmount(), 2); endif;

    ?></li>
      <li>Gross: <?php

    if (! $headerDTO == null) :
        echo number_format($headerDTO->getGrossAmount(), 2) . " " . $headerDTO->getCurrencyIso3(); endif;

    ?></li>
      <li>Billed: <?php

    if (! $headerDTO == null) :
        echo number_format($headerDTO->getBilledAmount(), 2) . " " . $headerDTO->getCurrencyIso3(); endif;

    ?></li>
      <li>-----</li>
      <li>Created by: <?php

    if (! $headerDTO == null) :
        echo $headerDTO->getCreatedByName(); endif;

    ?></li>
   </ul>
</div>