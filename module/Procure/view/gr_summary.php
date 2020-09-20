
<span style="color: gray; margin: 2pt 2pt 2pt 1pt">
       <?php
    use Application\Domain\Shared\Constants;

    echo $this->translate('Summary:');
    ?></span>
<div class="alert alert-<?php

echo $alert;
?>" role="alert" style="padding: 2px; margin: 2pt 2pt 2pt 8pt; font-size: 9.2pt">
   <ul style="font-size: 9pt; margin: 0; padding-left: 15pt">
      <li>
         <b><?php

        if (! $headerDTO == null) :
            echo sprintf("PR #%s (Version #%s)", $headerDTO->getSysNumber(), $headerDTO->getRevisionNo()); endif;

        ?></b>
         <span style="color: black;">
            <br><?php
            echo ($headerDTO !== null) ? $headerDTO->getDocNumber() : "";
            ?></span>
      </li>
      <li>-----</li>
      <li>Status: <?php

    if (! $headerDTO == null) :
        echo $headerDTO->getDocStatus(); endif;

    ?> </li>
      <li>Total rows: <?php

    echo ($headerDTO->totalRows);
    ?></li>
      <li>Status: <?php

    echo $transactionStatus;
    ?> </li>
      <li>-----</li>
      <li>Created by: <?php

    echo $headerDTO->getCreatedByName();
    echo $progress_div?></li>
   </ul>
</div>
<?php
if ($action == Constants::FORM_ACTION_REVIEW) :
    ?>
<div class="alert alert-warning" role="alert" style="padding: 5px 2px 2px 5px; margin: 5pt 2pt 2pt 8pt; font-size: 9.2pt; color: graytext;">
   <i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;
			<?php

    echo $this->translate('Please post when PO /Contract is signed. Document can\'t be changed anymore when posted! <br> ');
    ?>
		</div>
<?php endif;

?>
<hr>
