<?php
use Application\Domain\Contracts\FormActions;
?>
<FORM id="Creating_Form" class="form-horizontal" action="<?php
echo $form_action;
?>" method="post" enctype="multipart/form-data">
   <input type="hidden" name="redirectUrl" value="<?php
echo $redirectUrl?>" />
   <input type="hidden" id="entity_id" name="entity_id" value="<?php
echo $entity_id;
?>" />
   <input type="hidden" id="entity_token" name="entity_token" value="<?php
echo $entity_token;
?>" />
   <input type="hidden" name="version" value="<?php
echo $version;
?>" />

                        <?php
                        if ($action == FormActions::AP_FROM_PO) :
                            ?>
                        <input type="hidden" id="source_id" name="source_id" value="<?php
                            echo $source_id;
                            ?>" />
   <input type="hidden" id="source_token" name="source_token" value="<?php
                            echo $source_token;
                            ?>" />
                        <?php endif;

                        ?>
                        <fieldset>
      <legend style="font-size: 9.5pt; color: gray;">
         <small>
            <span class="glyphicon glyphicon-triangle-right"></span>
         </small>
         &nbsp;
         <a href="#invoice_header" class="" data-toggle="collapse">HEADER (<?php
        echo "Version " . $version;
        ?>):</a>
      </legend>
      <div id="invoice_header" class="collapse in">
         <div class="form-group margin-bottom required">
            <label class="control-label col-sm-2">PR number:</label>
            <div class="col-sm-3">
               <input class="form-control input-sm" type="text" placeholder="" id="docNumber" name="prNumber" value="<?php
            echo (! $headerDTO == null) ? $headerDTO->getPrNumber() : "";
            ?>">
            </div>
         </div>
         <div class="form-group margin-bottom required">
            <label class="control-label col-sm-2 required">PR Date:</label>
            <div class="col-sm-3">
               <input class="form-control input-sm" type="text" id="submittedOn" name="submittedOn" value="<?php
            if (! $headerDTO == null) :
                echo $headerDTO->getSubmittedOn(); endif;

            ?>" placeholder=" please select" />
            </div>
         </div>
         <div class="form-group margin-bottom">
            <label class="control-label col-sm-2">Keywords.:</label>
            <div class="col-sm-3">
               <input class="form-control input-sm" type="text" placeholder="" id="" keywords"" name="keywords" value="<?php
            echo (! $headerDTO == null) ? $headerDTO->getKeywords() : "";
            ?>">
            </div>
         </div>
         <div class="form-group margin-bottom required">
            <label class="control-label col-sm-2"><?php
            echo $this->translate("Target Warehouse");
            ?>:</label>
            <div class="col-sm-3">
               <select tabindex="9" name="warehouse" id="target_wh_id" class="form-control input-sm">
                  <option value=""><?php
                echo $this->translate("...");
                ?></option>
<?php
// ================
echo $wh_option;
// ================
?>
                                        </select>
            </div>
         </div>
         <div class="form-group margin-bottom required">
            <label class="control-label col-sm-2"><?php
            echo $this->translate("Department:");
            ?>:</label>
            <div class="col-sm-3">
               <select name="department" class="form-control input-sm">
                  <option value=""><?php
                echo $this->translate("---");
                ?></option>
 <?php
// ================
echo $department_option;
// ================
?>>
                     </select>
            </div>
         </div>
         <div class="form-group margin-bottom">
            <label class="control-label col-sm-2">Description</label>
            <div class="col-sm-8">
               <input class="form-control input-sm" type="text" id="remarks" placeholder="" name="remarks" value="<?php
            echo (! $headerDTO == null) ? $headerDTO->getRemarks() : "";
            ?>">
            </div>
         </div>
      </div>
      <hr>
      <div class="form-group margin-bottom">
         <label class="control-label col-sm-2" for="inputTag"></label>
         <div class="col-sm-3">
            <button id="submit" onclick="submitForm('Creating_Form');" class="btn btn-default btn-sm">
               <i class="glyphicon glyphicon-floppy-disk"> </i>
               Save
            </button>
            <a class="btn btn-default btn-sm" href="<?php
            echo $review_url;
            ?>">
               <small>
                  <i class="glyphicon glyphicon-remove"></i>
               </small>
               &nbsp;Cancel
            </a>
         </div>
      </div>
   </fieldset>
</form>
