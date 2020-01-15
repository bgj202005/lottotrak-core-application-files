<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>	
<?php echo form_open_multipart(base_url().'admin/lotteries/import/'.$lottery->id); ?>
	<h2><?php echo 'Import Lottery: '.$lottery->lottery_name; ?></h2>
	<?php if (!empty($message)) ?> <h3 style = "text-align:center;"><?=$message; ?></h3>
	<div class = "modal-body" style = "width:90%">
	<div class="form-group">
			<!-- Current Lottery Image -->
			<div class="form-group form-group-lg row"> 
				<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
				echo form_label('Current Lottery Image Logo:', 'current_lottery_image_logo_lb', $extra); ?>
				<div class="col-8">
						<?php $image_info = getimagesize(base_url().'images/uploads/'.$lottery->lottery_image); 
							$extra = array('width' => $image_info[0], 'height' => $image_info[1]);
							echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); ?>
				</div>
			</div>
			<!-- Lottery Description  -->
			<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg', 'style' => 'white-space: nowrap;');
					echo form_label('Lottery Description:', 'lottery_descriptiuon_lb', $extra); ?>
				<div class="col-8">
					<?php echo form_label($lottery->lottery_description, 'lottery_description_lb', $extra); ?>
				</div>
			</div>
			<!-- Current State or Province Field -->
			<div class="form-group form-group-lg row"> 
				<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
				echo form_label('Current State / Province:', 'state_province', $extra); ?>
				<div class="col-8" style="margin-top:10px;">
					<span class="bfh-states col-form-label-lg" data-country="<?=$lottery->lottery_country_id; ?>" data-state="<?=$lottery->lottery_state_prov; ?>"></span></label>
				</div>
			</div>
			<!-- Country Field -->
			<div class="form-group form-group-lg row"> 
				<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
				echo form_label('Country', 'country_lb', $extra); ?>
				<div class="col-8">
					<span id="countries_states2" class="bfh-selectbox bfh-countries col-form-label-lg" data-flags="true" data-country=<?=$lottery->lottery_country_id; ?> data-name="lottery_country_id"></div>
				</div>
			</div>
			<!-- Lottery Pick Game -->
			<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Pick:', 'lottery_pick_lb', $extra); ?>
				<div class="col-8">
					<?php echo form_label($lottery->balls_drawn, 'lottery_balls_drawn_lb', $extra); ?>
				</div>
			</div>
			<!-- Lottery Range  -->
			<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Lottery Range:', 'lottery_Range_lb', $extra); ?>
				<div class="col-8">
					<?php echo form_label('From '.$lottery->minimum_ball.' To '.$lottery->maximum_ball, 'lottery_range_lb', $extra); ?>
				</div>
			</div>
			<!-- Extra / Bonus Ball  -->
			<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Extra / Bonus Ball?', 'extra_ball_lb', $extra); ?>
				<div class="col-8">
					<?php echo form_label((!empty($lottery->extra_ball) ? 'YES' : 'NO'), 'extra_ball_lb', $extra); ?>
				</div>
			</div>
			<!-- Allow Duplicates? / Extra / Bonus Balls  -->
			<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Allow Duplicate Extra / Bonus Ball?', 'duplicate_extra_lb', $extra); ?>
				<div class="col-8">
					<?php echo form_label((!empty($lottery->duplicate_extra_ball) ? 'YES' : 'NO'), 'duplicate_extra_ball_lb', $extra); ?>
				</div>
			</div>
			<!-- Extra Ball Range  -->
			<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Lottery Range:', 'Extra_Ball_Range_lb', $extra); ?>
				<div class="col-8">
					<?php echo form_label('From '.$lottery->minimum_extra_ball.' To '.$lottery->maximum_extra_ball, 'lottery_extra_ball_range_lb', $extra); ?>
				</div>
			</div>
			<!-- Upload CVS File -->
			<div class="form-group form-group-lg row"> 
				<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
				echo form_label('Lottery Import CVS File:', 'lottery_import_cvs_file_lb', $extra); ?>
				<div class="col-8">
						<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'accept' => 'image/x-png,image/gif,image/jpeg', 'style'=> 'width:80%');  
						echo form_upload('lottery_upload_cvs',set_value('lottery_upload_cvs', ''), $extra); 
						echo form_error('lottery_upload_cvs', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Import from URL (Zip File or CVS File -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 control-label col-form-label-lg');
					echo form_label('Import CVS File (URL):', 'import_lottery_url_lb', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:80%');
						echo form_input('import_lottery_url',set_value('import_lottery_url', ''), $extra); 
						echo form_error('import_lottery_url', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<div style = "text-align: center;">
				<?php echo form_submit('submit', 'Begin Import / Upload', 'class="btn btn-primary btn-lg btn-info"');
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