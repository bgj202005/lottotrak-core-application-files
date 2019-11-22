<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>	
<?php echo validation_errors(); ?>
<?php echo form_open(); ?>
	<h2><?php echo empty($member->id) ? 'Add a new Member' : 'Edit Member: '.$member->username; ?></h2>
	<div class = "modal-body" style = "width:90%">
	<div class="form-group">
		<!-- Username Field -->
		<div class="form-group form-group-lg row"> 
				<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
				echo form_label('Username', 'username', $extra); ?>
				<div class="col-8">
						<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');  
						echo form_input('username',set_value('username', $member->username), $extra); 
						echo form_error('username', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Email Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label-lg');
					echo form_label('Email:', 'email', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');    
						echo form_input('email',set_value('email', $member->email), $extra); 
						echo form_error('email', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<p class="bg-info" style = "padding: 1em; text-align: center; color:snow;">Please enter a new password and confirm the new password.</p>
				<!-- Password Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label-lg');
					echo form_label('Password', 'password', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						echo form_password('password',set_value('password', ''), $extra); 
						echo form_error('password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Confirm Password -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Confirm Password', 'confirm_password', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						echo form_password('confirm_password',set_value('confirm_password', ''), $extra); 
						echo form_error('confirm_password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Firstname Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 control-label col-form-label-lg');
					echo form_label('First Name', 'first_name', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						echo form_input('first_name',set_value('first_name', $member->first_name), $extra); 
						echo form_error('first_name', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Lastname Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Last Name', 'last_name', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						echo form_input('last_name',set_value('last_name', $member->last_name), $extra); ?>
					</div>
				</div>
				<!-- City Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('City', 'city', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						echo form_input('city',set_value('city', $member->city), $extra); ?>
					</div>
				</div>
				<!-- Current State or Province Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Current State / Province:', 'state_province', $extra); ?>
					<div class="col-8" style="margin-top:10px;">
						<span class="bfh-states" data-country="<?=$member->country_id; ?>" data-state="<?=$member->state_prov; ?>"></span></label>
					</div>
				</div>
				<!-- State or Province Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('State / Province (If Different)', 'state_province', $extra); ?>
					<div class="col-8">
						<?php // $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge', 'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						// echo form_input('state_prov',set_value('state_prov', $member->state_prov), $extra); ?>
						<div class="bfh-selectbox bfh-states" data-country="countries_states2" data-name="state_prov"></div>
						<?php echo form_error('state_prov', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Country Field -->
					<div class="form-group form-group-lg row"> 
						<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
						echo form_label('Country', 'country', $extra); ?>
					<div class="col-8">
						<?php //$extra = array('class' => 'btn btn-secondary btn-lg dropdown-toggle'); 
						//echo form_dropdown('country_id', array('0' => 'No Country Listed','1' => 'Canada', '2' => 'United States'), $member->country_id, $extra); ?>
						<div id="countries_states2" class="bfh-selectbox bfh-countries" data-flags="true" data-country="CA" data-name="country_id"></div>
						<?php echo form_error('country_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div><!-- Current Lottery Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Lottery', 'lottery', $extra); ?>
					<div class="col-8">
						<?php $extra = array('class' => 'btn btn-secondary btn-lg dropdown-toggle');  
						echo form_dropdown('lottery_id', array('0' => 'No Lottery Listed',
					'1' => 'Canada 649', '2' => 'Power Ball'), $member->lottery_id, $extra); 
					echo form_error('lottery_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>	
					</div>
				</div>
				<!-- Registration Date -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Registration Date and Time:', 'registration_date_time', $extra); ?>
					<div class="col-8" style = "margin-top:10px;">
						<?=date('l, F, d Y h:i:s A', strtotime($member->reg_time));?>
					</div>
				</div>
				<!-- Account Active? -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Account Active', 'account_active', $extra); ?>
					<div class="col-8" style="margin-top:10px;">
						<?php echo form_checkbox('member_active', set_value('member_active', $member->member_active), 
						($member->member_active ? TRUE : FALSE)); ?>
					</div>
				</div>
				<div style = "text-align: center;"><?php echo form_submit('submit', 'Update Profile', 'class="btn btn-primary btn-lg btn-info"');
			echo form_close(); ?> <!-- </form> -->
		</div>
	</div>
</div>