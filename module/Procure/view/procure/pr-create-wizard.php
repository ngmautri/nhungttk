	<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('PR Header'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">1. <?php echo $this->translate('PR Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('Add PR Line'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="<?php echo $this->baseUrl ?>/procure/pr-row/add?token=<?php echo (!$entity == null)? $entity->getToken():"";?>&target_id=<?php echo (!$entity == null)? $entity->getId():"";?>">2. <?php echo $this->translate('Add PR Line'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP3") :?>
		<a class="list-group-item active" class="list-group-item" 
		href="<?php echo $this->baseUrl ?>/procure/pr/review?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Review'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">3. <?php echo $this->translate('Review'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP4") :?>
		<a style="color: gray" class="list-group-item" 
		href="<?php echo $this->baseUrl ?>/procure/pr/submit?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Finish'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">3. <?php echo $this->translate('Finish'); ?></a>
	<?php endif;?>
</div>						