<form id="upload_form" class="form-horizontal" action="<?php
echo $rows_upload_url;
?>" method="post" enctype="multipart/form-data">
   <input type="hidden" name="entity_id" value="<?php

echo (! $headerDTO == null) ? $headerDTO->getId() : "";
?>" />
   <input type="hidden" name="entity_token" value="<?php

echo (! $headerDTO == null) ? $headerDTO->getToken() : "";
?>" />
   <div class="form-group margin-bottom">
      <label class="control-label col-sm-2" for="inputTag"></label>
      <div class="col-sm-3">
         <input style="" type="file" id="uploaded_file" name="uploaded_file" />
      </div>
   </div>
   <div class="form-group margin-bottom">
      <label class="control-label col-sm-2" for="inputTag"></label>
      <div class="col-sm-3">
         <button type="submit" onclick="submitForm('upload_form');" class="btn btn-default btn-sm">
            <i class="glyphicon glyphicon-upload"> </i>
            Upload
         </button>
      </div>
   </div>
</form>
<hr>