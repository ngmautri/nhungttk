<?php
/**@var \Procure\Application\DTO\Po\PoDTO $headerDTO ;*/
if ($headerDTO != NULL) {
    $review_url = sprintf($this->baseUrl . "/procure/po/review1?entity_id=%s&entity_token=%s", $headerDTO->getId(), $headerDTO->getToken());
    $add_row_url = sprintf($this->baseUrl . "/procure/po/add-row?target_id=%s&target_token=%s", $headerDTO->getId(), $headerDTO->getToken());
}

?>
<div class="list-group" style="font-size: 9.5pt; margin: 20pt 2pt 5pt 5pt">
	<?php

if ($current_step == "STEP1") :
    ?>
		<a class="list-group-item active" href="#">1. <?php

    echo $this->translate('PO Header');
    ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
   </a>
	<?php

else :
    ?>
		<a style="color: gray" class="list-group-item" href="#">1. <?php

    echo $this->translate('PO Header');
    ?></a>
	<?php

endif;
?>
	
	<?php

if ($current_step == "STEP2") :
    ?>
		<a class="list-group-item active" href="#">2. <?php

    echo $this->translate('PO Line');
    ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
   </a>
	<?php
else :
    ?>
		<a style="color: gray" class="list-group-item" href="<?php

    echo $add_row_url;
    ?>">2. <?php
    echo $this->translate('PO Line');
    ?></a>
	<?php

endif;
?>
		<?php

if ($current_step == "STEP3") :
    ?>
		<a style="color: white" class="list-group-item active" href="#">3. <?php

    echo $this->translate('Review & Post');
    ?>&nbsp;&nbsp;&nbsp;<i class="fa fa-chevron-right" aria-hidden="true"></i>
   </a>
	<?php

else :
    ?>
		<a style="color: gray" class="list-group-item" href="<?php

    echo $review_url;
    ?>">3. <?php

    echo $this->translate('Review & Post');
    ?></a>
	<?php

endif;
?>
	
</div>
