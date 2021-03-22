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
		<?php echo form_open_multipart(base_url().'admin/lotteries/edit/'.$lottery->id); ?>
		<h2><?php echo empty($lottery->id) ? 'Add a new Lottery' : 'Edit Lottery: '.$lottery->lottery_name; ?></h2>
		<?php if (!empty($message)) ?> <h3 class="bg-warning" style = "text-align:center;"><?=$message; ?></h3>
		<div class="row">
			<div class="col-sm-8" style ="width:100%;">
				<div class ="card">
					<div class = "card-body">
						<div class="form-group">
							<!-- Lottery Name Field -->
							<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Lottery Name', 'lotteryname_lb', $extra); ?>
								<div class="col-8">
										<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');  
										echo form_input('lottery_name',set_value('lottery_name', $lottery->lottery_name), $extra); 
										echo form_error('lottery_name', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Lottery Description Field -->
								<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Lottery Description', 'lottery_description_lb', $extra); ?>
									<div class="col-8">
										<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '1000', 'row' => '10', 'cols' => '30', 'style'=> 'resize:none; width:100%');  
										echo form_textarea('lottery_description',set_value('lottery_description', $lottery->lottery_description), $extra); 
										echo form_error('lottery_description', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Current Lottery Image -->
								<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Current Lottery Image Logo:', 'current_lottery_image_logo_lb', $extra); ?>
								<div class="col-8">
										<?php   
										if (!empty($lottery->lottery_image)) 
										{ 
											$image_info = getimagesize(base_url().'images/uploads/'.$lottery->lottery_image); 
											$extra = array('width' => $image_info[0], 'height' => $image_info[1]);
											echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); } 
										else 
										{ 
											$extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'white-space: nowrap; overflow:visible');
											echo form_label('No Current Image Exists.', 'no_current_lottery_image_logo_lb', $extra);
										} ?>
									</div>
								</div>
								<!-- Lottery Image Field -->
								<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Lottery Image Logo', 'lottery_image_logo_lb', $extra); ?>
								<div class="col-8">
										<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'accept' => 'image/x-png,image/gif,image/jpeg', 'style'=> 'width:100%');  
										echo form_upload('lottery_image',set_value('lottery_image', $lottery->lottery_image), $extra); 
										echo form_hidden('image', $lottery->lottery_image);
										echo form_error('lottery_image', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Current State or Province Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Current State / Province:', 'state_province', $extra); ?>
									<div class="col-8" style="margin-top:10px;">
										<span class="bfh-states" data-country="<?=$lottery->lottery_country_id; ?>" data-state="<?=$lottery->lottery_state_prov; ?>"></span></label>
									</div>
								</div>
								<!-- State or Province Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('State / Province (If Different)', 'state_province_lb', $extra); ?>
									<div class="col-8">
										<div class="bfh-selectbox bfh-states" data-country="countries_states2" data-name="lottery_state_prov"></div>
									</div>
								</div>
								<!-- Country Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Country', 'country_lb', $extra); ?>
									<div class="col-8">
										<div id="countries_states2" class="bfh-selectbox bfh-countries" data-flags="true" data-country="CA" data-name="lottery_country_id"></div>
									</div>
								</div>
								<!-- Pick / Balls Drawn Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label-md');
									echo form_label('Number of Balls Drawn:', 'balls_drawn_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%', 'onchange' => 'changedBallsDrawn(this.value)');    
										echo form_input('balls_drawn',set_value('balls_drawn', $lottery->balls_drawn), $extra); 
										echo form_error('balls_drawn', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Lowest Ball Drawn Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label-md');
									echo form_label('Lowest Ball Drawn', 'minimum_ball_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');
										echo form_input('minimum_ball', set_value('minimum_ball', $lottery->minimum_ball), $extra); 
										echo form_error('minimum_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Highest Ball Drawn Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Highest Ball Drawn', 'maximum_ball_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');
										echo form_input('maximum_ball', set_value('maximum_ball', $lottery->maximum_ball), $extra); 
										echo form_error('maximum_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Extra / Bonus Ball Checkbox Yes / No? -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Extra / Bonus Ball?', 'extra_ball_lb', $extra); ?>
									<div class="col-8" style="margin-top:10px;">
										<input type = "checkbox" name = "extra_ball" value = "1" <?php echo (!empty($lottery->extra_ball) ? 'checked' : ''); ?> onchange = 'changedExtraBall(this.value)'/> 
										<?php //echo form_checkbox('extra_ball', '1', set_checkbox('extra_ball', '1', (!empty($lottery->extra_ball)))); ?>
									</div>
								</div>
								<!-- Allow Duplicate Extra / Bonus Ball Checkbox Yes / No?  -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Allow Duplicate Extra / Bonus Ball?', 'duplicate_extra_lb', $extra); ?>
									<div class="col-8" style="margin-top:10px;">
										<input type = "checkbox" name = "duplicate_extra_ball" value = "1" <?php echo (!empty($lottery->duplicate_extra_ball) ? 'checked' : ''); ?> /> 
										<?php //echo form_checkbox('duplicate_extra_ball', '1', set_checkbox('duplicate_extra_ball', '1', (!empty($lottery->duplicate_extra_ball)))); ?>
									</div>
								</div>
								<!-- Lowest Extra / Bonus Ball Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 control-label col-form-label-md');
									echo form_label('Lowest Extra / Bonus Ball', 'extra_minimum_ball_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');
										echo form_input('minimum_extra_ball',(!empty($lottery->minimum_extra_ball) ? set_value('minimum_extra_ball', $lottery->minimum_extra_ball) : ''), $extra); 
										echo form_error('extra_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>');
										echo form_error('minimum_extra_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Highest Extra / Bonus Ball Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Highest Extra / Bonus Ball', 'extra_maximum_ball_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');
										echo form_input('maximum_extra_ball',(!empty($lottery->maximum_extra_ball) ? set_value('maximum_extra_ball', $lottery->maximum_extra_ball) : ''), $extra);
										echo form_error('extra_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>');
										echo form_error('maximum_extra_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								
							<div style = "text-align: center;">
							<?php if ($lottery->id) // If $id
								{ 
									$extra = array('style' => 'padding:5px;', 'class' => 'btn btn-primary btn-lg btn-info');
									echo form_submit('submit', 'Update Lottery Profile', $extra);
								}
								else
								{
									echo form_submit('submit', 'Create Lottery Profile', 'style = "padding:5px;" class="btn btn-primary btn-lg btn-info"');
								}
								$js = "location.href='".base_url()."admin/lotteries/import/".$lottery->id."'";
								$class = ($lottery->id ? "btn btn-primary btn-lg btn-info" : "btn btn-secondary btn-lg disabled");
								if ($lottery->id) 
								{
									$attributes = array(
										'class' 	=> "$class", 
										'onClick' 	=> "$js", 
										'style' 	=> "margin-left:20px; padding:5px;",
										'role'		=> 'button'
									);
								}
								else
								{
									$attributes = array(
										'class' 	=> "$class", 
										'style' 	=> "margin-left:20px; padding:5px;",
										'role'		=> 'button',
										'disabled'	=> 'disabled'
									);
								}
								echo form_button('lotteries_import', 'Lottery Import', $attributes); 
								$js = "location.href='".base_url()."admin/lotteries/view_draws/".$lottery->id."'";
								$class = ($lottery->id ? "btn btn-primary btn-lg btn-info" : "btn btn-secondary btn-lg disabled");
								if ($lottery->id) 
								{
									$attributes = array(
										'class' 	=> "$class", 
										'onClick' 	=> "$js", 
										'style' 	=> "margin-left:20px; padding:5px;",
										'role'		=> 'button'
									);
								}
								else
								{
									$attributes = array(
										'class' 	=> "$class", 
										'style' 	=> "margin-left:20px; padding:5px;",
										'role'		=> 'button',
										'disabled'	=> 'disabled'
									);
								}
								echo form_button('lotteries_manual', 'Manual Entry', $attributes); 

								$js = "location.href='".base_url()."admin/lotteries'";
								$attributes = array(
									'class' 	=> "btn btn-primary btn-lg btn-info", 
									'onClick' 	=> "$js", 
									'style' 	=> "padding:5px; margin: 0 auto; display: block; margin-top:20px;"
								);
								echo form_button('lotteries_list', 'Back to Lotteries List', $attributes); 
								?>
							</div>
						</div>
					</div>				
				</div>
			</div>
			<div class="col-sm-4">
				<!-- Last Draw Date field -->
				<div class="card text-white bg-info mb-3" style="width: 100%;">
					<div class="card-header">
						<div class = "form group" style ="display:flex; flex-direction: row; justify-content: center; align-items: center; white-space:nowrap">
						<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'margin-left:-25px;');
										echo form_label('Last Draw Date:', 'last_draw_date_lb', $extra); ?>
						<?php $extra = array('class' => 'datepicker', 'id' => 'formGroupInputLarge',
											'maxlength' => '50', 'size' => '50', 'style'=> 'width:70%; margin-left:2em;'); ?>
							<div class="input-group date" id="datepicker1" data-provide="datepicker"> 
								<?php /* if (empty($lottery->id)) */ $lottery->last_draw_date = date('d-m-Y'); // Only on a New Lottery
								echo form_input('last_draw_date', set_value('last_draw_date', date("D, M-d-Y",strtotime(str_replace('/','-',$lottery->last_draw_date)))), $extra); ?>
								<span class="input-group-addon"><i class="fa fa-calendar" style = "padding:5px;"></i></span>
							</div>
						</div>
						<!-- Days of the Week for Draw -->
						<h6>Days of the Draw?</h6>
						<div class="form-group"> 
							<?php $extra = array('class' => 'col-4 col-form-label col-form-label-sm');
							echo form_label('Monday', 'day_monday_lb', $extra); 
							echo form_checkbox('monday', set_value('monday', '1'), set_checkbox('monday', '1', (!empty($lottery->monday)))); 
							echo form_label('Tuesday', 'day_tuesday_lb', $extra); 
							echo form_checkbox('tuesday', '1', set_checkbox('tuesday', '1', (!empty($lottery->tuesday))));
							echo form_label('Wednesday', 'day_wednesday_lb', $extra); 
							echo form_checkbox('wednesday', '1', set_checkbox('wednesday', '1', (!empty($lottery->wednesday))));
							echo form_label('Thursday', 'day_thursday_lb', $extra); 
							echo form_checkbox('thursday', '1', set_checkbox('thursday', '1', (!empty($lottery->thursday))));
							echo form_label('Friday', 'day_friday_lb', $extra); 
							echo form_checkbox('friday', '1', set_checkbox('friday', '1', (!empty($lottery->friday))));
							echo form_label('Saturday', 'day_saturday_lb', $extra); 
							echo form_checkbox('saturday', '1', set_checkbox('saturday', '1', (!empty($lottery->saturday))));
							echo form_label('Sunday', 'day_sunday_lb', $extra); 
							echo form_checkbox('sunday', '1', set_checkbox('sunday', '1', (!empty($lottery->sunday)))); 
							echo form_error('monday', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
						</div>
					<?php echo form_close(); ?> <!-- </form> -->
					</div>		
					<div class="card-body">
						<h5 class="card-title">Most Recent Draw:</h5>
						<?php if (!empty($lastdraw)&&!$lastdraw) 
						{ 
							echo "<p>This Lottery Database is missing. Please Delete this profile and recreate a new Lottery Profile.</p>";
						} 
						elseif (!empty($lastdraw)&&$lastdraw==='nodraws')
						{ 
							echo "<p>Although, their is a Lottery Database.  There are no draws in the database.</p>";
						}
						else  
						{ 
							echo "<p><small>The latest draws will go here.</small></p>";
						} ?>
					</div>
				</div>
				<div class="card text-white bg-dark mb-3" style="width: 100%;">
					<div class="card-header">Optimized Win Snapshot</div>
					<div class="card-body">
						<h5 class="card-title">Panel title</h5>
						<p>This card has supporting text below as a natural lead-in to additional content.</p>
						<p><small>Last updated 3 mins ago</small></p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
$(function() {
  $('.input-group').datepicker({
	Default: 'D, M-d-yyyy',
	format: 'D, M-d-yyyy',  
    orientation: 'bottom auto',
    todayBtn: "linked",
    autoclose: true,
    clearBtn: true
  });
});
</script>
<script>
var balls_drawn  = document.getElementsByName("balls_drawn")[0].value;

function changedBallsDrawn(val)
{
	if (parseInt(val) > parseInt(balls_drawn)) return confirm("You have changed the Number of Balls drawn from "+balls_drawn+" To "+val+". This will expand the Database. Press OK and the Update Lottery Profile button to Proceed.");
	else if (parseInt(val) < parseInt(balls_drawn)) return confirm("You have changed the Number of Balls drawn from "+balls_drawn+" To "+val+". This will reduce the Database. Press OK and the Update Lottery Profile button to Proceed.");
}
function changedExtraBall(val)
{
	return confirm("You have changed the Extra / Bonus Ball and will change the structure of the database. Press OK and the Update Lottery Profile button to Proceed.");
}
</script>