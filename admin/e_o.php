<?php
/*
  $Id$

  osCommerce, Open Source E-Commerce Solutions
  http://www.oscommerce.com

  Copyright (c) 2010 osCommerce

  Released under the GNU General Public License
*/

  require('includes/application_top.php');

  $action = (isset($HTTP_GET_VARS['action']) ? $HTTP_GET_VARS['action'] : '');

  if (tep_not_null($action)) {
  }

  require(DIR_WS_INCLUDES . 'template_top.php');

?>
    <style type="text/css">
    .container {
        width: 500px;
        border-style: solid;
        border-width: 5px;
        float:left;
        box-sizing: border-box;
        padding: 15px;
        
    }
    .container input {
        width: 100%;
        clear: both;
    }

    </style>
<div class="container">
<table border="1" width="500">
<tr>
<td colspan="4">customer</td>
</tr>
<tr>
<td><?php echo ENTRY_CUSTOMER; ?></td><td>dd</td><td>dd</td><td>dd</td>
</tr>



</table>
</div>
<div>



<div class="container">
<label>aa
<input type="text" class="tel-number-field" name="tel_no_1" value="" maxlength="4" />
</label>
<input />
<div>











<div class="form-style-2-heading">Provide your information</div>
<form action="" method="post">
<label><span>Name <span class="required">*</span></span><input type="text" class="input-field" name="field1" value="" /></label>
<label for="field2"><span>Email <span class="required">*</span></span><input type="text" class="input-field" name="field2" value="" /></label>
<label><span>Telephone</span><input type="text" class="tel-number-field" name="tel_no_1" value="" maxlength="4" />-<input type="text" class="tel-number-field" name="tel_no_2" value="" maxlength="4"  />-<input type="text" class="tel-number-field" name="tel_no_3" value="" maxlength="10"  /></label>
<label for="field4"><span>Regarding</span><select name="field4" class="select-field">
<option value="General Question">General</option>
<option value="Advertise">Advertisement</option>
<option value="Partnership">Partnership</option>
</select></label>
<label for="field5"><span>Message <span class="required">*</span></span><textarea name="field5" class="textarea-field"></textarea></label>

<label><span>&nbsp;</span><input type="submit" value="Submit" /></label>
</form>
</div>


</div>

<?php
  require(DIR_WS_INCLUDES . 'template_bottom.php');
  require(DIR_WS_INCLUDES . 'application_bottom.php');
?>

