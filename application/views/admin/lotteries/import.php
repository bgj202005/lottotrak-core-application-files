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
									<?php echo form_label($lottery->lottery_description, 'lottery_description_lb', $extra); ?>
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
									<?php echo form_checkbox('allow_zero_extra', set_value('allow_zero_extra', '1'), set_checkbox('allow_zero_extra', '1', (!empty($lottery->allow_zero_extra))), 'style = "margin:15px 0px 0px 15px;"'); ?>
								</div>
							</div>
							<!-- Upload CVS File -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Lottery Import CVS File:', 'lottery_import_cvs_file_lb', $extra); ?>
								<div class="col-8">
										<?php $extra = array('class' => 'form-control', 'id' => 'lottery_upload_cvs',
										'accept' => '.csv', 'style'=> 'width:80%');  
										echo form_upload('lottery_upload_csv',set_value('lottery_upload_cvs', ''), $extra); 
										echo form_error('lottery_upload_csv', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Import from URL (Zip File or CVS File -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 control-label col-form-label-md');
									echo form_label('Import CVS File (URL):', 'import_lottery_url_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control form-control-lg', 'id' => 'FormControlInput',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:80%');
										echo form_input('import_lottery_url',set_value('import_lottery_url', ''), $extra); 
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
											<div class = "progress-bar progress-bar-striped active bg-success" role = "progressbar" 
											aria-valuemin="0" aria-valuemax = "100">
												<span id ="process_data">0<span> - <span id = "total_data">0</span>
											</div>
										</div>
									</div>
								</div>
								<div style = "text-align: center;">
									<?php echo form_submit('import', 'Begin Import / Upload', 'class="btn btn-primary btn-lg btn-info" id = "import"');
									$js = "location.href='".base_url()."admin/lotteries'";
									$attributes = array(
										'class' 	=> "btn btn-primary btn-lg btn-info", 
										'onClick' 	=> "$js", 
										'style' 	=> "margin-left:20px;"
									);
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
						<div class="card-body text-danger" style = "display:block;">
							<h5 class="card-title">Lottery Draws Do Not Exist</h5>
							<p class="card-text">Please Import your draws by clicking the Import Button below.</p>		
						</div>
					<div class="card-footer bg-transparent border-danger" id = "footer-on">Current Draw(s) N/A</div>
				</div>			
			</div>
		</div>
	</div>
</section>
<script>
$(document).ready(function() {
	var clear_timer;
	$('#import_form').on('submit', function(event) {
		$('#import_message').html('');
			event.preventDefault();
			$.ajax({
				url:"<?php echo base_url(); ?>admin/lotteries/import/<?=$lottery->id; ?>", 
				method: "POST",
				data: new FormData(this),
				dataType: 'json',
				contentType:false,
				cache:false,
				processData:false,
				beforeSend:function() {
					$('#import').attr('disabled','disabled');
					$('#import').val('Importing');
					$('#footer-on').html('Draw #<span id= "draw_number"></span>');
				},
				success:function(data)
				{
					if(data.success)
					{
						$('#total_data').text(data.total_data);
						$('.card-title').html("<div class='alert alert-warning' role='alert'>DRAWS CURRENTLY BEING IMPORTED</div>");
						$('.card-text').html("<div class='alert alert-info' role='alert'>Please adding draws ... please Wait ... </div>");
						start_import();
						clear_timer = setInterval(get_import_data, 2000);

						// $('#import_error_message').html('<div class = "alert alert-success">CSV File Uploaded Successfully</div>');
					}					
					if(data.error)
					{
						$('#import_message').html('<div class = "alert alert-danger">'+data.error+'</div>');
						$('#import').attr('disabled',false);
      					$('#import').val('Begin Import / Upload');
					}
				}	 
			})
		});
	function start_import()
	{
		$('#process').css('display', 'block');
		$.ajax({
			url:"<?php echo base_url(); ?>admin/lotteries/import_process/<?=$lottery->id; ?>",
			dataType: 'json',
			processData: false,
			success:function(data)
			{
				var error = 0;
				if(data.success)
				{
					//alert("successful");
					//alert(data);
				}
				else if(data.error)
				{
					$('.card-title').text("AN ERROR HAS OCCURRED");
					$('.card-text').text("The import could not continue. Please check CVS File.");
					error = 1;
				} else if(data.month_error)
				{
					$('.card-title').text("MONTH IMPORT ERROR");
					$('.card-text').html("This is what was attempted on import: "+data.draw_date+". <br /> Please check CVS File.");
					error = 1;
				} else if(data.day_error)
				{
					$('.card-title').text("DAY OF MONTH IMPORT ERROR");
					$('.card-text').html("This is what was attempted on import: "+data.draw_date+". <br />Please check CVS File.");
					error = 1;
				} else if(data.year_error)
				{
					$('.card-title').text("YEAR IMPORT ERROR");
					$('.card-text').html("This is what was attempted on import: "+data.draw_date+". <br />Please check CVS File.");
					error = 1;
				} else if(data.range_error)
				{
					$('.card-title').text("OUT OF RANGE ERROR");
					$('.card-text').html("One or more numbers are out of range.  This was the last draw date attempted on import: "+data.draw_date+". <br /> Please check CVS File.");
					error = 1;
				}

				if (error)
				{
					$('#lottery_upload_cvs').val('');
					$('#import_message').html('<div class="alert alert-danger">Data Has Been Stopped.</div>');
					$('#import').attr('disabled',false);
					$('#import').val('Begin Import / Upload');
				}
			}
		})
	}
	function get_import_data()
	{
	$.ajax({
		url:"<?php echo base_url(); ?>admin/lotteries/process/<?=$this->lotteries_m->lotto_table_convert($lottery->lottery_name); ?>",
		success:function(data)
			{
				var total_data = $('#total_data').text();
				var width = Math.round((data/total_data)*100);
				$('#process_data').text(data);
				$('.progress-bar').css('width', width + '%');
				$('#draw_number').text(data);

				if(width >= 100)
				{
					clearInterval(clear_timer);
					$('#process').css('display', 'none');
					$('#lottery_upload_cvs').val('');
					$('#import_message').html('<div class="alert alert-success">Draw(s) Successfully Imported</div>');
					$('.card-title').html("<div class='alert alert-success' style = 'text-align:center' role='alert'>IMPORT COMPLETE</div>");
					$('.card-text').html("<div class='alert alert-info' style = 'text-align:center' role='alert'>Draw Importing <br /><br /> ... Done ... </div>");
					$('#import').attr('disabled',false);
					$('#import').val('Begin Import / Upload');
				}
				if (data.error)
				{
					$('#lottery_upload_cvs').val('');
					$('#import_message').html('<div class="alert alert-danger">Data Has Been Stopped.</div>');
					$('#import').attr('disabled',false);
					$('#import').val('Begin Import / Upload');
				}
			}
		})
	} 
})
</script>