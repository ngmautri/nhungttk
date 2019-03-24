<div style="font-size: 9pt; margin: 5pt 2pt 5pt 5pt">
	<a style="font-size: 9pt; margin: 10pt 2pt 20pt 20pt"
		class="btn btn-default btn-sm" href="/inventory/item-transaction/list">
		<small><i class="fa fa-chevron-left" aria-hidden="true"></i></small>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Transaction List");?></a>

	<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('Goods Transfer Header'); ?>&nbsp;&nbsp;&nbsp;<i
			class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item" href="#">1. <?php echo $this->translate('Goods Transfer Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('Goods Transfer Line'); ?>&nbsp;&nbsp;&nbsp;<i
			class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
			<a style="color: gray" class="list-group-item"
			href="<?php echo $this->baseUrl ?>/inventory/transfer-row/add?token=<?php echo (!$entity == null)? $entity->getToken():"";?>&target_id=<?php echo (!$entity == null)? $entity->getId():"";?>">2. <?php echo $this->translate('Goods Transfer Line'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP3") :?>
			<a class="list-group-item active" href="#">3. <?php echo $this->translate('Review & Post'); ?> &nbsp;&nbsp;&nbsp;<i
			class="fa fa-chevron-right" aria-hidden="true"></i></a>	
	<?php else:?>
			<a style="color: gray" class="list-group-item"
			href="<?php echo $this->baseUrl ?>/inventory/transfer/review?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Review & Post'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php endif;?>
	
	</div>
</div>

<?php if ($current_step=="STEP1") :?>
<div style="margin: 5pt 2pt 5pt 5pt;">&nbsp;<i class="fa fa-info-circle" aria-hidden="true"></i><span style="padding: 1px; font-size: 9.5pt; color: graytext;">
	<?php echo $this->translate("Goods tranfer between warehouse or between location in a warehouse");?>.
	</span>
</div>
<?php endif;?>

<?php if ($current_step=="STEP2") :?>
<div style="margin: 5pt 2pt 5pt 5pt;">&nbsp;<i class="fa fa-info-circle" aria-hidden="true"></i><span style="padding: 1px; font-size: 9.5pt; color: graytext;">
	<?php echo $this->translate(" Add item for transfer to other warehouse");?>.
	</span>
</div>
<?php endif;?>

<?php if ($current_step=="STEP3") :?>
<div style="margin: 5pt 2pt 5pt 5pt;">&nbsp;<i class="fa fa-info-circle" aria-hidden="true"></i><span style="padding: 1px; font-size: 9.5pt; color: graytext;">
	<?php echo $this->translate("Goods tranfer will be posted. No change can be made");?>.
	</span>
</div>
<?php endif;?>