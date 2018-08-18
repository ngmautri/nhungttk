<div>
	<a style="font-size: 9pt; margin: 10pt 2pt 5pt 20pt" class ="btn btn-default btn-sm" href="/procure/po/list">
	<small><i class="fa fa-chevron-left" aria-hidden="true"></i></small>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("PO List");?></a>
	<div class="list-group"
		style="font-size: 9.5pt; margin: 20pt 2pt 5pt 5pt">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('PO Header'); ?>&nbsp;&nbsp;&nbsp;<i
			class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item" href="#">1. <?php echo $this->translate('PO Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('PO Line'); ?>&nbsp;&nbsp;&nbsp;<i
			class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
			href="<?php echo $this->baseUrl ?>/procure/po-row/add?token=<?php echo (!$entity == null)? $entity->getToken():"";?>&target_id=<?php echo (!$entity == null)? $entity->getId():"";?>">2. <?php echo $this->translate('PO Line'); ?></a>
	<?php endif;?>
		<?php if ($current_step=="STEP3") :?>
		<a style="color: white" class="list-group-item active" href="#">3. <?php echo $this->translate('Review & Post'); ?>&nbsp;&nbsp;&nbsp;<i
			class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
			href="<?php echo $this->baseUrl ?>/procure/po/review?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Review & Post'); ?></a>
	<?php endif;?>
	
</div>

</div>
