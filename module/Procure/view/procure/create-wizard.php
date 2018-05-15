<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('PO Header'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">1. <?php echo $this->translate('PO Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('PO Line'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">2. <?php echo $this->translate('PO Line'); ?></a>
	<?php endif;?>
		<?php if ($current_step=="STEP2") :?>
		<a style="color: gray" class="list-group-item" 
		href="<?php echo $this->baseUrl ?>/procure/po/add1?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Finish'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">3. <?php echo $this->translate('Finish'); ?></a>
	<?php endif;?>
	
</div>								