
<div class="list-group">
	<?php if ($current_step=="STEP1") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Payroll Period'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/payroll-period"><?php echo $this->translate('Payroll Period'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP2") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Payroll Input Consolidate'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/input-consolidate"><?php echo $this->translate('Payroll Input Consolidate'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP3") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Payroll Input Review'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/input-check"><?php echo $this->translate('Payroll Input Review'); ?></a>
	<?php endif;?>
	
	
	<?php if ($current_step=="STEP5") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Draft Payroll'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/simulate"><?php echo $this->translate('Draft Payroll'); ?></a>
	<?php endif;?>

	<?php if ($current_step=="STEP6") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Payroll Adjustment'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/adjust-payroll"><?php echo $this->translate('Payroll Review'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP7") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Payroll Review'); ?>&nbsp;&nbsp;&nbsp;<i
		class="fa fa-chevron-right" aria-hidden="true"></i></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/adjust-payroll"><?php echo $this->translate('Final Payroll'); ?></a>
	<?php endif;?>
	
	<?php if ($current_step=="STEP8") :?>
		<a class="list-group-item active" href="#"><?php echo $this->translate('Submision for Approval'); ?></a>
	<?php else:?>
		<a style="color: gray" class="list-group-item"
		href="/hr/salary-calculator/payroll-submit"><?php echo $this->translate('Submision for Approval'); ?></a>
	<?php endif;?>
	
</div>
