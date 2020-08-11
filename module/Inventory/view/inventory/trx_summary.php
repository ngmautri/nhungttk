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
            echo sprintf("Transaction #%s (Rev.#%s)", $headerDTO->getSysNumber(), $headerDTO->getRevisionNo()); endif;

        echo sprintf("<br><br>Transaction Code: %s", $headerDTO->getMovementType());

        ?></b>
      </li>
      <li>-----</li>
      <li>
         <b><?php

        if (! $transactionTypeArray == null) :
            echo $this->translate($transactionTypeArray['type_name']); endif;

        ?></b>
      </li>
      <li>-----</li>
      <li>
         <i><?php

        if (! $transactionTypeArray == null) :
            echo $this->translate($transactionTypeArray['type_description']); endif;

        ?></i>
      </li>
      <li>-----</li>
      <li>Status: <?php

    if (! $headerDTO == null) :
        echo $headerDTO->getDocStatus(); endif;

    ?> </li>
      <li>Total rows: <?php

    echo ($headerDTO->totalRows);
    ?></li>
      <li>-----</li>
      <li>Created by: <?php
    echo $headerDTO->getCreatedByName();
    ?></li>
   </ul>
</div>