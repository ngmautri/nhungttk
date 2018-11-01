<div style="font-size: 9pt; margin: 5pt 2pt 5pt 5pt">
	<a style="font-size: 9pt; margin: 10pt 2pt 20pt 20pt" class ="btn btn-default btn-sm" href="/procure/quote/list">
	<small><i class="fa fa-chevron-left" aria-hidden="true"></i></small>&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $this->translate("Quotation List");?></a>


<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#">1. <?php echo $this->translate('Quotation Header'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">1. <?php echo $this->translate('Quotation Header'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#">2. <?php echo $this->translate('Quotation Line'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">2. <?php echo $this->translate('Quotation Line'); ?></a>
	<?php endif;?>
		<?php if ($current_step=="STEP2") :?>
		<a style="color: gray" class="list-group-item" 
		href="<?php echo $this->baseUrl ?>/procure/quote/show?token=<?php echo (!$target == null)? $target->getToken():"";?>&entity_id=<?php echo (!$target == null)? $target->getId():"";?>">3. <?php echo $this->translate('Finish'); ?>&nbsp;&nbsp;&nbsp;</a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="#">3. <?php echo $this->translate('Finish'); ?></a>
	<?php endif;?>
	
</div>	
</div>					