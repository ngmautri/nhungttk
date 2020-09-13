<div style="font-size: 9pt; margin: 5pt 2pt 5pt 5pt">
   <a style="font-size: 9pt; margin: 10pt 2pt 20pt 20pt" class="btn btn-default btn-sm" href="/inventory/item-transaction/list">
      <small>
         <i class="fa fa-chevron-left" aria-hidden="true"></i>
      </small>&nbsp;&nbsp;&nbsp;&nbsp;<?php
    echo $this->translate("Transaction List");
    ?></a>
   <div class="list-group">
	<?php

if ($current_step == "STEP1") :
    ?>
		<a class="list-group-item active" href="#">1. <?php

    echo $this->translate('Goods Receipt Header');
    ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
      </a>
	<?php

else :
    ?>
		<a style="color: gray" class="list-group-item" href="#">1. <?php

    echo $this->translate('Goods Receipt Header');
    ?></a>
	<?php

endif;
?>
	
	<?php

if ($current_step == "STEP2") :
    ?>
		<a class="list-group-item active" href="#">2. <?php

    echo $this->translate('Goods Receipt Line');
    ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
      </a>
	<?php

else :
    ?>
			<a style="color: gray" class="list-group-item" href="<?php

    echo $this->baseUrl?>/inventory/gr/add-row?target_token=<?php

    echo (! $headerDTO == null) ? $headerDTO->getToken() : "";
    ?>&target_id=<?php

    echo (! $headerDTO == null) ? $headerDTO->getId() : "";
    ?>">2. <?php

    echo $this->translate('Goods Receipt Line');
    ?>&nbsp;&nbsp;&nbsp;</a>
	<?php

endif;
?>
	
	<?php

if ($current_step == "STEP3") :
    ?>
			<a class="list-group-item active" href="#">3. <?php

    echo $this->translate('Review & Post');
    ?> &nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
      </a>	
	<?php

else :
    ?>
			<a style="color: gray" class="list-group-item" href="<?php

    echo $this->baseUrl?>/inventory/gi/review?entity_token=<?php

    echo (! $headerDTO == null) ? $headerDTO->getToken() : "";
    ?>&entity_id=<?php

    echo (! $headerDTO == null) ? $headerDTO->getId() : "";
    ?>">3. <?php

    echo $this->translate('Review & Post');
    ?>&nbsp;&nbsp;&nbsp;</a>
	<?php

endif;
?>
	
</div>
</div>