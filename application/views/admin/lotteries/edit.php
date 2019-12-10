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
		<?php echo form_open(base_url().'admin/lotteries/edit/'.$lottery->id); ?>
		<h2><?php echo empty($lottery->id) ? 'Add a new Lottery' : 'Edit Lottery: '.$lottery->name; ?></h2>
		<?php if (!empty($message)) ?> <h3 style = "text-align:center;"><?=$message; ?></h3>
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
										echo form_input('lottery_name',set_value('lottery_name', $lottery->name), $extra); 
										echo form_error('lottery_name', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Lottery Description Field -->
								<div class="form-group form-group-lg row"> 
								<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
								echo form_label('Lottery Description', 'lottery_description_lb', $extra); ?>
									<div class="col-8">
										<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');  
										echo form_textarea('description',set_value('description', $lottery->description), $extra); 
										echo form_error('lottery_name', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- State or Province Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('State / Province (If Different)', 'state_province_lb', $extra); ?>
									<div class="col-8">
										<div class="bfh-selectbox bfh-states" data-country="countries_states2" data-name="state_prov"></div>
										<?php echo form_error('state_prov', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Country Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Country', 'country_lb', $extra); ?>
									<div class="col-8">
										<div id="countries_states2" class="bfh-selectbox bfh-countries" data-flags="true" data-country="CA" data-name="country_id"></div>
									<?php echo form_error('country_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Pick / Balls Drawn Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label-md');
									echo form_label('Number of Balls Drawn:', 'balls_drawn_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');    
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
										<?php echo form_checkbox('member_active', ($lottery->extra_ball ? 1 : 0), ($lottery->extra_ball ? TRUE : FALSE)); ?>
									</div>
								</div>
								<!-- Allow Duplicate Extra / Bonus Ball Checkbox Yes / No?  -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Allow Duplicate Extra / Bonus Ball?', 'duplicate_extra_lb', $extra); ?>
									<div class="col-8" style="margin-top:10px;">
										<?php echo form_checkbox('duplicate_extra', ($lottery->duplicate_extra ? 1 : 0), ($lottery->duplicate_extra ? TRUE : FALSE)); ?>
									</div>
								</div>
								<!-- Lowest Extra / Bonus Ball Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 control-label col-form-label-md');
									echo form_label('Lowest Extra / Bonus Ball', 'extra_minimum_ball_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');
										echo form_input('extra_minimum_ball',set_value('minimum_extra_ball', $lottery->extra_minimum_ball), $extra); 
										echo form_error('extra_minimum_ball', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
									</div>
								</div>
								<!-- Highest Extra / Bonus Ball Field -->
								<div class="form-group form-group-lg row"> 
									<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md');
									echo form_label('Highest Extra / Bonus Ball', 'extra_maximum_ball_lb', $extra); ?>
									<div class="col-8">
									<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
										'maxlength' => '50', 'size' => '50', 'style'=> 'width:15%');
										echo form_input('extra_maximum_ball',set_value('maximum_extra_ball', $lottery->extra_maximum_ball), $extra); ?>
									</div>
								</div>
								
							<div style = "text-align: center;"><?php echo form_submit('submit', 'Update Lottery Profile', 'class="btn btn-primary btn-lg btn-info"');
								$js = "location.href='".base_url()."admin/lotteries/import'";
								$attributes = array(
									'class' 	=> "btn btn-primary btn-lg btn-info", 
									'onClick' 	=> "$js", 
									'style' 	=> "margin-left:20px;"
								);
								echo form_button('lotteries_import', 'Lottery Import', $attributes); 

								$js = "location.href='".base_url()."admin/lotteries'";
								$attributes = array(
									'class' 	=> "btn btn-primary btn-lg btn-info", 
									'onClick' 	=> "$js", 
									'style' 	=> "margin-left:20px;"
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
						<?php $extra = array('class' => 'col-4 col-form-label col-form-label-md', 'style' => 'margin-left:-15px;');
										echo form_label('Last Draw Date:', 'last_draw_date_lb', $extra); ?>
						<?php $extra = array('class' => 'datepicker', 'id' => 'formGroupInputLarge',
											'maxlength' => '50', 'size' => '50', 'style'=> 'width:70%; margin-left:2em;'); ?>
							<div class="input-group date" id="datepicker1" data-provide="datepicker"> 
								<?php if (empty($lottery->id)) $lottery->last_draw_date = date('Y-m-d'); // Only on a New Lottery
								echo form_input('last_draw_date', set_value('last_draw_date', date("l, d-m-Y",strtotime(str_replace('/','-',$lottery->last_draw_date)))), $extra); ?>
								<span class="input-group-addon"><i class="fa fa-calendar" style = "padding:5px;"></i></span>
							</div>
						</div>
						<!-- Days of the Week for Draw -->
						<h6>Days of Draw?</h6>
						<div class="form-group"> 
							<?php $extra = array('class' => 'col-4 col-form-label col-form-label-sm');
							echo form_label('Monday', 'day_monday_lb', $extra); 
							echo form_checkbox('monday', ($lottery->days['monday'] ? 1 : 0), ($lottery->days['monday'] ? TRUE : FALSE)); 
							echo form_label('Tuesday', 'day_tuesday_lb', $extra); 
							echo form_checkbox('tuesday', ($lottery->days['tuesday'] ? 1 : 0), ($lottery->days['tuesday'] ? TRUE : FALSE));
							echo form_label('Wednesday', 'day_wednesday_lb', $extra); 
							echo form_checkbox('wednesday', ($lottery->days['wednesday'] ? 1 : 0), ($lottery->days['wednesday'] ? TRUE : FALSE));
							echo form_label('Thursday', 'day_thursday_lb', $extra); 
							echo form_checkbox('thursday', ($lottery->days['wednesday'] ? 1 : 0), ($lottery->extra_ball ? TRUE : FALSE));
							echo form_label('Friday', 'day_friday_lb', $extra); 
							echo form_checkbox('friday', ($lottery->days['wednesday'] ? 1 : 0), ($lottery->extra_ball ? TRUE : FALSE));
							echo form_label('Saturday', 'day_saturday_lb', $extra); 
							echo form_checkbox('saturday', ($lottery->days['wednesday'] ? 1 : 0), ($lottery->extra_ball ? TRUE : FALSE));
							echo form_label('Sunday', 'day_sunday_lb', $extra); 
							echo form_checkbox('sunday', ($lottery->days['wednesday'] ? 1 : 0), ($lottery->extra_ball ? TRUE : FALSE)); ?>
						</div>
					<?php echo form_close(); ?> <!-- </form> -->
					</div>		
					<div class="card-body">
						<h5 class="card-title">Most Recent Draw:</h5>
						<p>This card has supporting text below as a natural lead-in to additional content.</p>
						<p><small>Last updated 3 mins ago</small></p>
					</div>
				</div>
				<div class="card text-white bg-dark mb-3" style="width: 100%;">
					<div class="card-header">Optimized Wins</div>
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
	Default: 'dd-mm-yyyy',
	format: 'dd-mm-yyyy',  
    orientation: 'bottom auto',
    todayBtn: "linked",
    autoclose: true,
    clearBtn: true
  });
});
</script>