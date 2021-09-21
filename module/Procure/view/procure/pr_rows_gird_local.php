
<hr>
<span style="color: gray; margin: 2pt 2pt 2pt 8pt"><?php
echo $this->translate('Summary:');
?></span>
<div class="alert alert-<?php

echo $alert;
?>" role="alert" style="padding: 2px; margin: 2pt 2pt 2pt 8pt; font-size: 9.2pt">
   <ul style="font-size: 9pt; margin: 0; padding-left: 15pt">
      <li>
         <b><?php

        if (! $headerDTO == null) :
            echo sprintf("GR-PO #%s (Version #%s)", $headerDTO->getSysNumber(), $headerDTO->getDocVersion()); endif;

        ?></b>
      </li>
      <li>-----</li>
      <li>
         <b><?php

        if (! $headerDTO == null) :
            echo $headerDTO->vendorName; endif;

        ?></b>
      </li>
      <li>
         <i><?php

        if (! $headerDTO == null) :
            echo sprintf("%s %s", $headerDTO->getVendorAddress(), $headerDTO->getVendorCountry()); endif;

        ?></i>
      </li>
      <li>-----</li>
      <li><?php

    if (! $headerDTO == null) :
        echo $headerDTO->incotermCode . " " . $headerDTO->incotermPlace; endif;

    ?> </li>
      <li>Status: <?php

    if (! $headerDTO == null) :
        echo $headerDTO->getDocStatus();
        echo '<br> Doc Type: ' . $headerDTO->getDocType();endif;

    ?> </li>
      <li>Total rows: <?php

    echo ($headerDTO->totalRows);
    ?></li>
      <li>Status: <?php

    echo $transactionStatus;
    ?> </li>
      <li>-----</li>
      <li>Net: <?php

    echo number_format($headerDTO->netAmount, 2) . " " . $headerDTO->currencyIso3;
    ?></li>
      <li>Tax: <?php

    echo number_format($headerDTO->taxAmount, 2);
    ?></li>
      <li>Gross: <?php

    echo number_format($headerDTO->grossAmount, 2) . " " . $headerDTO->currencyIso3;
    ?></li>
      <li>Billed: <?php

    echo number_format($headerDTO->getBilledAmount(), 2) . " " . $headerDTO->currencyIso3;
    ?></li>
      <li>-----</li>
      <li>Created by: <?php

    echo $headerDTO->getCreatedByName();
    ?></li>
   </ul>
</div>