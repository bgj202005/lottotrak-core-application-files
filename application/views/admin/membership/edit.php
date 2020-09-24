<!-- Bootstrap Form Helpers -->
<link href="<?php echo site_url('css/bootstrap-formhelpers.min.css');?>" rel="stylesheet" media="screen">
<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
	<!--[if lt IE 9]>
	  <script src="js/html5shiv.js"></script>
	  <script src="js/respond.min.js"></script>
	<![endif]-->
<!-- Bootstrap Form Helpers -->	
<script src="<?php echo site_url('js/bootstrap-formhelpers.min.js');?>"></script>	
<?php echo form_open(base_url().'admin/membership/edit/'.$member->id); ?>
	<h2><?php echo empty($member->id) ? 'Add a new Member' : 'Edit Member: '.$member->username; ?></h2>
	<?php if (!empty($message)) ?> <h3 style = "text-align:center;"><?=$message; ?></h3>
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
				<!-- Password Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label-lg');
					echo form_label('Password', 'password', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
						'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
						echo form_password('password', '' , $extra); 
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
						echo form_password('password_confirm', '', $extra); 
						echo form_error('password_confirm', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
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
						<div class="bfh-selectbox bfh-states" data-country="countries_states2" data-name="state_prov"></div>
						<?php echo form_error('state_prov', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div>
				<!-- Country Field -->
					<div class="form-group form-group-lg row"> 
						<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
						echo form_label('Country', 'country', $extra); ?>
					<div class="col-8">
						<div id="countries_states2" class="bfh-selectbox bfh-countries" data-flags="true" data-country="<?=$member->country_id; ?>" data-name="country_id"></div>
						<?php echo form_error('country_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
					</div>
				</div><!-- Current Lottery Field -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Lottery', 'lottery', $extra); ?>
					<div class="col-8">
					<?php $extra = array('class' => 'btn btn-secondary btn-lg');
					echo form_multiselect('lottery_id[]', $lotteries['list'], $lotteries['selected'], $extra);
					echo form_error('lottery_id', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>	
					</div>
				</div>
				<!-- Registration Date -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Registration Date and Time:', 'registration_date_time', $extra); ?>
					<div class="col-8" style = "margin-top:10px;">
						<?=date('l, F, d Y h:i:s A', strtotime($member->reg_time)); ?>
					</div>
				</div>
				<!-- Account Active? -->
				<div class="form-group form-group-lg row"> 
					<?php $extra = array('class' => 'col-4 col-form-label col-form-label-lg');
					echo form_label('Account Active', 'account_active', $extra); ?>
					<div class="col-8" style="margin-top:10px;">
						<?php echo form_checkbox('member_active', ($member->member_active ? 1 : 0), ($member->member_active ? TRUE : FALSE)); ?>
					</div>
				</div>
				<div style = "text-align: center;">
				<?php if ($member->id) // If id exists
					{
						echo form_submit('submit', 'Update Member Profile', 'class="btn btn-primary btn-lg btn-info"');
					}
					else
					{
						echo form_submit('submit', 'Create Member Profile', 'class="btn btn-primary btn-lg btn-info"');
					}
				$js = "location.href='".base_url()."admin/membership'";
				$attributes = array(
					'class' 	=> "btn btn-primary btn-lg btn-info", 
					'onClick' 	=> "$js", 
					'style' 	=> "margin-left:20px;"
				);
				echo form_button('members_list', 'Back to Members List', $attributes);
			echo form_close(); ?> <!-- </form> -->
		</div>
	</div>
</div>