<div style="font-size: 9pt; margin: 5pt 2pt 5pt 5pt">
	<a style="font-size: 9pt; margin: 10pt 2pt 20pt 20pt" class ="btn btn-default btn-sm" href="/finance/v-invoice/list">
	<small><i class="fa fa-chevron-left" aria-hidden="true"></i></small>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Outgoing Payment");?></a>
	
<div class="list-group">

	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('Payment Header'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">1. <?php echo $this->translate('Payment Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('A/P Invoice Line'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="<?php echo $this->baseUrl ?>/finance/v-invoice-row/add?token=<?php echo (!$entity == null)? $entity->getToken():"";?>&target_id=<?php echo (!$entity == null)? $entity->getId():"";?>">2. <?php echo $this->translate('A/P Invoice Line'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP3") :?>
		<a class="list-group-item active" class="list-group-item" href="#">3. <?php echo $this->translate('Review & Post'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
				href="<?php echo $this->baseUrl ?>/finance/v-invoice/review?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Review & Post'); ?></a>
	<?php endif;?>
	
	
</div>
</div>						