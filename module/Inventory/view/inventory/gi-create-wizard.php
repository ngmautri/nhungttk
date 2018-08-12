<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('Goods Issue Header'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item" href="#">1. <?php echo $this->translate('Goods Issue Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('Goods Receipt Line'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
			<a style="color: gray" class="list-group-item"
		href="<?php echo $this->baseUrl ?>/inventory/gi-row/add?token=<?php echo (!$entity == null)? $entity->getToken():"";?>&target_id=<?php echo (!$entity == null)? $entity->getId():"";?>">2. <?php echo $this->translate('Goods Issue Line'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP3") :?>
			<a class="list-group-item active" href="#">3. <?php echo $this->translate('Review & Post'); ?> &nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>	
	<?php else:?>
			<a style="color: gray" class="list-group-item"
		href="<?php echo $this->baseUrl ?>/inventory/gi/review?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Review & Post'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php endif;?>
	
</div>
