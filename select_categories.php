
                <div  class="ev_fltlft" style="width:33%">
                    <div id="head" >Category</div>
                    <select name="disease_category" id="disease_category" class="selectBig" onChange="dynamic_Select('ajax/disease_subcategory.php', this.value, '0','subcategory_id' );">
                        <option value="">-- Select Category --</option>
                        <?php
$res = mysql_query("select * from `disease_category` ORDER BY `cat_name` ASC");
while($row = mysql_fetch_array($res)){
	if ( $row['id'] == $bc_disease_category )
		$sele = 'selected="selected"';
	else
		$sele = ''; ?>
                        	<option <?php echo $sele; ?> value="<?php echo $row['id']; ?>"><?php echo $row['cat_name']; ?></option>
                        <?php } ?>
                    </select>
                </div>


                   <br /><br />
		<script>
        function dynamic_Select(ajax_page,category_id,sub_category,resp_id)
         {
             $.ajax({
                type: "GET",
                url: ajax_page,
                data: "cat=" + category_id + "&subcat=" + sub_category + "&class=selectBig",
                dataType: "text/html",
                success: function(html){
                $("#"+resp_id).html(html);
                }
            });
          }
        </script>