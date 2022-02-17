<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>	
<section>
	<div class="container">
			<?php echo form_open_multipart(base_url().'admin/lotteries/import/'.$lottery->id, 'id = "import_form"'); ?>
			<h2><?php echo 'Import Lottery: '.$lottery->lottery_name; ?></h2>
			<h3 style = "text-align:center;" id="import_message"></h3>
			<div class="row">
				<div class="col-9" style ="width:100%;">
					<div class = "card">
						<div class = "card-body">
							<div class="form-group">
							<!-- Current Lottery Image -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Current Lottery Image Logo:', 'current_lottery_image_logo_lb', $extra); ?>
								<div class="col-8">
										<?php $image_info = getimagesize(base_url().'images/uploads/'.$lottery->lottery_image); 
											$extra = array('width' => $image_info[0], 'height' => $image_info[1]);
											echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); ?>
								</div>
							</div>
							<!-- Lottery Description  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;');
									echo form_label('Lottery Description:', 'lottery_descriptiuon_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label(wordwrap($lottery->lottery_description, 60, '<br /> ', FALSE), 'lottery_description_lb', $extra); ?>
								</div>
							</div>
							<!-- Current State or Province Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Current State / Province:', 'state_province', $extra); ?>
								<div class="col-8" style="margin-top:10px;">
									<span class="bfh-states col-form-label-lg" data-country="<?=$lottery->lottery_country_id; ?>" data-state="<?=$lottery->lottery_state_prov; ?>"></span></label>
								</div>
							</div>
							<!-- Country Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Country', 'country_lb', $extra); ?>
								<div class="col-8">
									<span id="countries_states2" class="bfh-selectbox bfh-countries col-form-label-lg" data-flags="true" data-country=<?=$lottery->lottery_country_id; ?> data-name="lottery_country_id">
								</div>
							</div>
							<!-- Lottery Pick Game -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Pick:', 'lottery_pick_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label($lottery->balls_drawn, 'lottery_balls_drawn_lb', $extra); ?>
								</div>
							</div>
							<!-- Lottery Range  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Lottery Range:', 'lottery_Range_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label('From '.$lottery->minimum_ball.' To '.$lottery->maximum_ball, 'lottery_range_lb', $extra); ?>
								</div>
							</div>
							<!-- Extra / Bonus Ball  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Extra / Bonus Ball?', 'extra_ball_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label((!empty($lottery->extra_ball) ? 'YES' : 'NO'), 'extra_ball_lb', $extra); ?>
								</div>
							</div>
							<!-- Draw Days  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Days of Draw:', 'draw_days_lb', $extra); ?>
								<div class="col-8">
									<?php $s = '';
										if ($lottery->monday) $s .= 'Mondays<br />';
										if ($lottery->tuesday) $s .= 'Tuesdays<br />';
										if ($lottery->wednesday) $s .= 'Wednesdays<br />';
										if ($lottery->thursday) $s .= 'Thursdays<br />';
										if ($lottery->friday) $s .= 'Fridays<br />';
										if ($lottery->saturday) $s .= 'Saturdays<br />';
										if ($lottery->sunday) $s .= 'Sundays';
										echo form_label($s, 'draw_days_lb', $extra); ?>
								</div>
							</div>
							<!-- Allow Duplicates? / Extra / Bonus Balls  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Allow Duplicate Extra / Bonus Ball?', 'duplicate_extra_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label((!empty($lottery->duplicate_extra_ball) ? 'YES' : 'NO'), 'duplicate_extra_ball_lb', $extra); ?>
								</div>
							</div>
							<!-- Extra Ball Range  -->
							<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Lottery Range:', 'Extra_Ball_Range_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_label('From '.$lottery->minimum_extra_ball.' To '.$lottery->maximum_extra_ball, 'lottery_extra_ball_range_lb', $extra); ?>
								</div>
							</div>
							<!-- Allow importing Bonus / Extra ball as 0, if Bonus Draws? -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Allow Import of 0 Bonus / Extra Ball?', 'allow_zero_extra_lb', $extra); ?>
								<div class="col-8">
									<?php echo form_checkbox('allow_zero_extra', set_value('allow_zero_extra', '1'), set_checkbox('allow_zero_extra', '1', (!empty($import_data[0]->zero_extra))), 'style = "margin:15px 0px 0px 15px;"'); ?>
								</div>
							</div>
							<!-- Eliminate Field in CSV File -->
							<div class="form-group form-group-lg row">
							<?php if(isset($import_data[0]->columns)) 
									{
										$extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;');
										echo form_label('Last Columns Eliminated from Import:', 'last_lottery_columns_lb', $extra); ?>
										<div class="col-8">
											<?php $removal = rtrim($import_data[0]->columns,",");
											echo form_label($removal.' ', 'last_lottery_columns_imported_lb', $extra); ?>
										</div> 
								<?php }
								$extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('CSV Column # (0-Based, No Spaces)', 'cvs_field_lb', $extra); ?>
								<div class="col-8">
									<div class="table-responsive" style = "width:80%">  
                               			<table class="table table-bordered" id="dynamic_field">  
                                    		<tr>  
												<td style = "width:65%">
													<?php $extra = array('class' => 'form-control', 'id' => 'current_csv',
													'maxlength' => '2', 'size' => '10', 'style'=> 'width:75%', 'placeholder' => 'Column # to Remove');  
													echo form_input('csv_field[]','', $extra); 
													echo form_error('csv_field[]', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
												</td>
												<td style="width:35%">
												<?php 
													$extra = array('class' => 'btn btn-info', 'id' => 'add');  
													echo form_button('add','Add More', $extra); ?>
												</td>
											</tr>  
                               			</table>  
									</div>
								</div>
							</div>
							<!-- Upload csv File -->
							<div class="form-group form-group-lg row">
							<?php if(isset($import_data[0]->csv_file)) 
									{
										$extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap;');
										echo form_label('Last Lottery CSV File Imported:', 'last_lottery_import_lb', $extra); ?>
										<div class="col-8">
										<?= form_label($import_data[0]->csv_file, 'last_lottery_import_csv_lb', $extra); ?>
										</div> 
								<?php }
								$extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Lottery Import CSV File:', 'lottery_import_csv_file_lb', $extra); ?>
								<div class="col-8">
										<?php $extra = array('class' => 'form-control', 'id' => 'lottery_upload_csv',
										'accept' => '.csv', 'style'=> 'width:80%'); 
										echo form_upload('lottery_upload_csv', set_value((isset($import_data[0]->csv_file) ? $import_data[0]->csv_file : '')), $extra); 
										echo form_error('lottery_upload_csv', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Import from URL (Zip File or csv File -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 control-label col-form-label-md');
									echo form_label('Import csv File (URL):', 'import_lottery_url_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control form-control-lg', 'id' => 'FormControlInput',
										'maxlength' => '550', 'size' => '50', 'style'=> 'width:80%');
										echo form_input('import_lottery_url',set_value('import_lottery_url', (isset($import_data[0]->csv_url) ? $import_data[0]->csv_url : '')), $extra); 
										echo form_error('import_lottery_url', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Hidden Value -->
								<div class="form-group row"> 
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'align' => 'center');
										echo form_hidden('hidden_field', '1'); ?>
								</div>
								<!-- Import Progress Bar for 0 to 100 % -->
								<div class="form-group" id = "process" style = "display:none;"> 
									<div class="col-8">
										<div class = "progress">
											<div class = "progress-bar progress-bar-striped progress-bar-animated active" 
											style = "width: 100%; display: inline-block; text-align:center; margin:5px;" role = "progressbar" 
											aria-valuemin="0" aria-valuemax = "100">
												<span id ="process_data">0</span> - <span id = "total_data">0</span>
											</div>
										</div>
									</div>
								</div>
								<div style = "text-align: center;">
									<?php echo form_submit('import', 'Begin Import / Upload', 'class="btn btn-primary btn-lg btn-info" id = "import"');
									$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info", 
										'id'		=> "lottery_list",
										'style' 	=> "margin-left:20px;"
									);
									$data = array('type'  => 'hidden', 'name'  => 'import_click', 'id' => 'import_click', 'value' => '0');
									echo form_input($data); 
									echo form_button('lottery_list', 'Back to Lotteries List', $attributes);
									echo form_close(); ?> <!-- </form> -->
								</div>
							</div>
						</div>
					</div>
				</div>
			<div class="col-3" style = "width:100%;">
				<div class="card border-danger" style="max-width: 20rem; display:block;">					
					<div class="card-header bg-transparent border-danger">Lottery Draw Import</div>
						<div class="card-body text-danger">
							<h5 class="card-title">
								<?php if ($lottery->last_draw=='nodraws') 
								{ ?>
									<h6 id = "nodraws" style="display:block" >No Draws Currently Exist</h6>
								<?php }
								elseif (!empty($lottery->last_draw->id))
								{
									$draw = "";
									$last_date = "";
									foreach ($lottery->last_draw as $key => $value) 
									{
										if(substr($key, 0, 4)=='ball') $draw .= $value." ";
										if($key=='extra') $draw .= " + ".$value;
										if($key=='draw_date') $last_date = date("D, M d, Y",strtotime(str_replace('/','-',$value)));
									}
								 
								echo "<h5 class='alert alert-primary'>Last Draw Date:</h5><h5 class='alert alert-danger'>".$last_date."</h5>";
								echo "<h6 class='alert alert-success text-dark'>".$draw."</h6>"; 
								} ?>
							<p class="card-text">Please Import NEW draws by clicking the Import Button below.</p>		
						</div>
					<div class="card-footer bg-transparent border-danger" id = "footer-on">Currently Imported: N/A</div>
				</div>			
			</div>
		</div>
	</div>
</section>
<script>
$(document).ready(function() {
	var clear_timer;
	var i=1;
	var csv=document.getElementsByName('csv_field[]');
	var url_import = '<?php echo base_url();?>admin/lotteries/import/<?=$lottery->id;?>';
	var url_start = '<?php echo base_url();?>admin/lotteries/import_process/<?=$lottery->id;?>';
	var url_get_import = '<?php echo base_url();?>admin/lotteries/process/<?=$lottery->table_name;?>';

	$('#add').click(function(){  
           i++;  
           $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="csv_field[]" value = "'+csv[0].value+'" style="width:90%" class="form-control field_list" /></td><td align="center"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove text-center">X</button></td></tr>');
		   $('#current_csv').val('');
		   $('#current_csv').placeholder = "Column # to Remove";   
	  });  
      $(document).on('click', '.btn_remove', function(){  
           var button_id = $(this).attr("id");   
           $('#row'+button_id+'').remove();  
      });
	  $("#lottery_list").click(function(){
		  var url_exit = "<?php echo base_url();?>admin/lotteries";
		  var clicked = document.getElementById('import_click').value;
		  if(clicked!=='0')
		  {
			if (window.confirm("Do you want to exit before the import is complete?"))
				{	
					clearInterval(clear_timer);
					window.location.href = url_exit;
				}
				else return false;
		  } else {
			clearInterval(clear_timer);
			window.location.href = url_exit;
		  }
	  });
	    
	$('#import_form').on('submit', function(event) {
		document.getElementById("import_click").value = '1'; // The submit has been clicked
		$('#import_message').html('');
			event.preventDefault();
			$.ajax({
				url:url_import, 
				method: "POST",
				data: new FormData(this),
				dataType: "json",
				contentType:false,
				cache:false,
				async: false,
				processData:false,
				beforeSend:function() {
					$('#import').attr('disabled','disabled');
					$('#import').val('Importing');
					$('#nodraws').css('display', 'none');
					$('#footer-on').html('Last Imported Draw #<span id= "draw_number"></span>');
				},
				success:function(data)
				{
					if(data.success)
					{
						$('#total_data').text(data.total_data);
						$('.card-title').html("<div class='alert alert-warning' role='alert'>DRAWS CURRENTLY BEING IMPORTED</div>");
						$('.card-text').html("<div class='alert alert-info' role='alert'>Adding draws ... please Wait ... </div>");
						start_import();
					}					
					if(data.error)
					{
						clearInterval(clear_timer);
						$('#import_message').html('<div class = "alert alert-danger">'+data.error+'</div>');
						$('#import').attr('disabled',false);
      					$('#import').val('Begin Import / Upload');
					}
				},
				error: function(jqXhr, textStatus, errorMessage){
      			//alert("Error: "+errorMessage+" text:"+textStatus);
   			}	 
			})
		});
	function start_import()
	{
		$('#process').css('display', 'block');
		$.ajax({
			url:url_start,
			dataType:"json",
			success:function(data)
			{
				var error = 0;
				if(data.success)
				{
					//clear_timer = setInterval(get_import_data, 1000);
					//alert(data.success);
				}
				else if(data.error)
				{
					$('.card-title').text("AN ERROR HAS OCCURRED");
					$('.card-text').text("The import could not continue. Please check csv File.");
					error = 1;
				} 
				else if(data.month_error)
				{
					$('.card-title').text("MONTH IMPORT ERROR");
					$('.card-text').html("This is what was attempted on import: "+data.draw_date+". <br /> Please check CSV File.");
					error = 1;
				} 
				else if(data.day_error)
				{
					$('.card-title').text("DAY OF MONTH IMPORT ERROR");
					$('.card-text').html("This is what was attempted on import: "+data.draw_date+". <br />Please check CSV File.");
					error = 1;
				} 
				else if(data.year_error)
				{
					$('.card-title').text("YEAR IMPORT ERROR");
					$('.card-text').html("This is what was attempted on import: "+data.draw_date+". <br />Please check CSV File.");
					error = 1;
				} 
				else if(data.range_error)
				{
					$('.card-title').text("OUT OF RANGE ERROR");
					$('.card-text').html("One or more numbers are out of range.  This was the last draw date attempted on import: "+data.draw_date+". <br /> Please check CSV File.");
					error = 1;
				}
				else if(data.duplicate_error)
				{
					$('.card-title').text("THE EXTRA/BONUS BALL IS NOT ALLOWED TO BE DUPLICATED FROM THE REGULAR DRAWN BALLS");
					$('.card-text').html("This was the last draw date attempted on import: "+data.draw_date+". <br /> Please check the numbers and try again for this date in the CSV file.");
					error = 1;
				}

				else if(data.zero_error)
				{
					$('.card-title').text("DRAWS WITH EXTRA/BONUS BALL EQUAL TO ZERO IS NOT ALLOWED");
					$('.card-text').html("This was the last draw date attempted on import: "+data.draw_date+". <br /> Please check the 'Allow Import of 0 Bonus / Extra Ball' option and import CSV file again.");
					error = 1;
				}
				else if(data.regular_duplicate_error)
				{
					$('.card-title').text("DRAW HAS REGULAR DRAWN NUMBERS THAT ARE DUPLICATE. DUPLICATES NOT ALLOWED.");
					$('.card-text').html("This was the last draw date attempted on import: "+data.draw_date+". <br /> Please check the numbers in this draw, correct and import CSV file again.");
					error = 1;
				}
				if (error)
				{
					$('#process').css('display', 'none');
					clearInterval(clear_timer);
					$('#import_message').html('<div class="alert alert-danger">Data Has Been Stopped.</div>');
					$('#import').attr('disabled',false);
					$('#import').val('Begin Import / Upload');
				}
			},
			complete:function(data) {
				clear_timer = setInterval(get_import_data, 1000);
			},
			error: function(jqXhr, textStatus, errorMessage){
      			//alert("Error: "+errorMessage+" text:"+textStatus);
   			}
		})
	}
	function get_import_data()
	{
	$.ajax({
		url:url_get_import,
		success:function(data)
			{
				var total_data = $('#total_data').text();
				var width = Math.round((data/total_data)*100);
				var error = 0;
				
				$('#process_data').text(data);
				$('.progress-bar').css('width', width+'%').attr('aria-valuenow', width); 
				$('#draw_number').html(data);
				if(width >= 100)
				{
					document.getElementById("import_click").value = '0'; // Import Completed, no warming message is required
					clearInterval(clear_timer);
					$('#process').css('display', 'none');
					$('#lottery_upload_csv').val('');
					$('#import_message').html('<div class="alert alert-success">Draw(s) Successfully Imported</div>');
					$('.card-title').html("<div class='alert alert-success' style = 'text-align:center' role='alert'>IMPORT COMPLETE</div>");
					$('.card-text').html("<div class='alert alert-info' style = 'text-align:center' role='alert'>Draw Importing <br /><br /> ... Done ... </div>");
					$('#import').attr('disabled',false);
					$('#import').val('Begin Import / Upload');
				}
				if (data.error)
				{
					clearInterval(clear_timer);
					$('#process').css('display', 'none');
					$('#lottery_upload_csv').val('');
					$('#import_message').html('<div class="alert alert-danger">Data Has Been Stopped.</div>');
					$('#import').attr('disabled',false);
					$('#import').val('Begin Import / Upload');
				}
			}
		})
	} 
})										
var columns = <?php echo json_encode($columns); ?>											
csv_field = document.getElementsByName('csv_field[]');
// Loop through the array and target the next available textbox
var count=0;
for(var i = 0; i < columns.length; i++){
// If there are any names to use, use one
	// Pop the next name off of your array and set the value
	// of your textbox
	csv_field[i].value = columns[i];
	count++;
	if(i < columns.length-1) $('#dynamic_field').append('<tr id="row'+i+'"><td><input type="text" name="csv_field[]" value = "'+csv_field[i].value+'" style="width:90%" class="form-control field_list" /></td><td align="center"><button type="button" name="remove" id="'+i+'" class="btn btn-danger btn_remove text-center">X</button></td></tr>');
} 
</script>