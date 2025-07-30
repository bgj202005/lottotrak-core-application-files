<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-sm-6 col-md-8">
				<div class="row" style = "text-align:center;">	
					<?php if (!$verified) 
					{ ?>
						<h1 class="bg-danger" style = "color:#FFFFFF">The Link Could Not Be Validated Or Is Expired.</h1>
						<div class="row">
						<div class="col-xs-10 col-md-8"></div></div></div>
					<?php } 
					else 
					{ ?> <!--  if ($verified=='active') -->
						<h3 class="bg-default" style = "color:#FFFFFF">Enter a new password and type the password in again to confirm it is correct</h3>	
				</div>
					<div class="row">
						<div class="col-xs-10 col-md-8">
						<?php $hidden = array('id' => $id);
							echo form_open(base_url().'member/update_password', 'class="form-horizontal"', $hidden);
							echo form_hidden('verified', $verified);  
							if (isset($email_hash, $email_code)) { 
								echo form_hidden('email_hash', $email_hash); 
								echo form_hidden('email_code', $email_code);
							}
							if (!isset($email)) $email = '';
							echo form_hidden('email', $email);
							?>
							<!-- Password Field -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-sm-2 control-label');
                    			echo form_label('Password', 'password', $extra); ?>
                    			<div class="col-sm-10">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_password('password',set_value('password', ''), $extra); 
									echo form_error('password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<!-- Confirm Password -->
							<div class="form-group form-group-lg row"> 
                    			<?php $extra = array('class' => 'col-sm-2 control-label');
                    			echo form_label('Confirm Password', 'confirm_password_label', $extra); ?>
                    			<div class="col-sm-10">
								<?php $extra = array('class' => 'form-control', 'id' => 'formGroupInputLarge',
								   'maxlength' => '50', 'size' => '50', 'style'=> 'width:100%');
									echo form_password('confirm_password',set_value('confirm_password', ''), $extra); 
									echo form_error('confirm_password', '<div class="bg-warning" style = "margin-top:10px; padding: 10px; text-align: center; color:#ffffff; font-size:16px;">', '</div>'); ?>
								</div>
							</div>
							<div style = "text-align: center;">
								<?php echo form_submit('submit', 'Update Password', 'class="btn btn-primary btn-lg btn-info"');
								echo form_close(); ?> <!-- </form> -->
							</div>
						</div>
					</div>
				</div>
				<?php } ?> <!-- if ($verified=='expired') -->
			<!--  Sidebar -->
			<div class="col-xs-12 col-md-4 sidebar">
				<?php $this->load->view('sidebar'); ?>
			</div>
          </div>
			<div class="row">
				<div class="col-xs-12 col-md-8">
					<div class="wrapper">
						<div class="content-menu">
							<?php echo get_footer_menu($footer_menu_inside, $maintenance); ?>
							<div class="clear"></div>
					  </div>
				   </div>
				</div>
			</div>	
		</div>
	</div>
</section>