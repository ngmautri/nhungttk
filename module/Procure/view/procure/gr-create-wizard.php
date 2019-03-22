<div style="font-size: 9pt; margin: 5pt 2pt 5pt 5pt">
	<a style="font-size: 9pt; margin: 10pt 2pt 20pt 20pt" class ="btn btn-default btn-sm" href="/procure/gr/list">
	<small><i class="fa fa-chevron-left" aria-hidden="true"></i></small>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("GR List");?></a>


<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('Goods Receipt Header'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item" href="#">1. <?php echo $this->translate('Goods Receipt Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('Goods Receipt Line'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
			<a style="color: gray" class="list-group-item"
		href="<?php echo $this->baseUrl ?>/procure/gr-row/add?token=<?php echo (!$entity == null)? $entity->getToken():"";?>&target_id=<?php echo (!$entity == null)? $entity->getId():"";?>">2. <?php echo $this->translate('Goods Receipt Line'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP3") :?>
			<a class="list-group-item active" href="#">3. <?php echo $this->translate('Review & Post'); ?> &nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>	
	<?php else:?>
			<a style="color: gray" class="list-group-item"
		href="<?php echo $this->baseUrl ?>/procure/gr/review?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Finish'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php endif;?>
	
</div>

<div style="padding: 1px; font-size: 9.5pt; margin: 5pt 2pt 5pt 5pt; color:graytext;">
	<i class="fa fa-info-circle" aria-hidden="true"></i>&nbsp;<?php echo $this->translate("Goods receipt without Invoice");?>.<br>
<?php echo $this->translate("if it is with invoice, please click ");?> <a style="color: blue" class="" target="_blank"
		href="<?php echo $this->baseUrl ?>/finance/v-invoice/add"><?php echo $this->translate("here");?>...</a>
	
</div>
</div>