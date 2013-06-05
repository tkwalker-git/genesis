					 <table>
                    	<tr>
                        	<td width="82"><strong>Dosage</strong>:</td>
                            <td width="145"><input type="text" name="dosage" id="dosage" class="new_input" value="<?php echo $bc_dosage; ?>" /></td>
                        </tr>

                        <tr>
                        	<td><strong>Without food</strong>:</td>
                            <td><input type="checkbox" value="1" id="without_food" name="without_food" class="new_input" <?php if ($bc_without_food == 1){ echo 'checked="checked"';} ?>  /></td>
                        </tr>
                        <tr>
                        	<td><strong>Breakfast</strong>:</td>
                            <td><input type="text" name="breakfast" id="breakfast" class="new_input" value="<?php echo $bc_breakfast; ?>"></td>
                        </tr>
                         <tr>
                        	<td><strong>Snack1</strong>:</td>
                            <td><input type="text" name="snack1" id="snack1" class="new_input" value="<?php echo $bc_snack1; ?>"></td>
                        </tr>
                         <tr>
                        	<td><strong>Lunch</strong>:</td>
                            <td><input type="text" name="lunch" id="lunch" class="new_input" value="<?php echo $bc_lunch; ?>"></td>
                        </tr>
                         <tr>
                        	<td><strong>Snack2</strong>:</td>
                            <td><input type="text" name="snack2" id="snack2" class="new_input" value="<?php echo $bc_snack2; ?>"></td>
                        </tr>
                         <tr>
                        	<td><strong>Dinner</strong>:</td>
                            <td><input type="text" name="dinner" id="dinner" class="new_input" value="<?php echo $bc_dinner; ?>"></td>
                        </tr>
                        <tr>
                        	<td><strong>Before Bed</strong>:</td>
                            <td><input type="text" name="before_bed" id="before_bed" class="new_input" value="<?php echo $bc_before_bed; ?>"></td>
                        </tr>

                    </table>
                  </div>                                      

                    <div style="float:right; width:60%">
	                    <div id="head">Special Dosage Instructions</div>                 
                  	<table>
                       
                        <tr>
                        	<?php $bc_comment_s = strip_tags($bc_comment);?>
                            <td><textarea type="text" name="comment" id="comment" class="new_input" style="width:470px;height:255px;" value="<?php echo $bc_comment_s; ?>"></textarea></td>
                        </tr>

                    </table>