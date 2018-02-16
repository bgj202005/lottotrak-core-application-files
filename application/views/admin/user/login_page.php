<?php $this->load->view('admin/components/page_head'); 
?>
<body>
<!-- BEGIN # BOOTSNIP INFO -->

<div class="container">
	<div class="row">
		<h1 class="text-center">Sea Shell Delivery</h1>
        <p class="text-center"><a href="#" class="btn btn-primary btn-lg" role="button" data-toggle="modal" data-target="#login-modal">To Login Click Here</a></p>
	</div>
</div>
<!-- END # BOOTSNIP INFO -->

<!-- BEGIN # MODAL LOGIN -->
<div class="modal fade" id="login-modal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" style="display: none;">
    	<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header" align="center">
					<img class="img-circle" id="img_logo" src="<?php echo site_url('images/Bradley-Jacobsen_51616_sm.png');?>"> <!--  http://bootsnipp.com/img/logo.jpg  -->
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
					</button>
				</div>
                
                <!-- Begin # DIV Form -->
                <div id="div-forms">
                
                    <!-- Begin # Login Form -->
                    <?php $attributes = array('id' => 'login-form');
                    echo form_open('admin/user/login', $attributes); ?>
                    <!--  <form id="login-form"> -->
		                <div class="modal-body">
				    		<div id="div-login-msg">
                                <div id="icon-login-msg" class="glyphicon glyphicon-chevron-right"></div>
                                <span id="text-login-msg">Type your username and password.</span>
                            </div>
				    		<?php echo form_input('username', '', 'autocomplete="off" id="login_username" class="form-control" placeholder="Username (No Spaces or Weird Characters)" required'); ?>
				    		<?php echo form_password('password', '', 'autocomplete="off" id="login_password" class="form-control" placeholder="Password" required'); ?>
                            <div class="checkbox">
                                <label>
                                    <?php echo form_checkbox('remember_me', 'Remember me', FALSE) ?> Remember me
                                  
                                </label>
                            </div>
        		    	</div> 
				        <div class="modal-footer">
                            <div>
                                <button name = "submit" type = "submit" class="btn btn-primary btn-lg btn-block">Login</button>
                            </div>
				    	    <div style = "text-align: center; margin: auto;">
                                <?php echo form_button('lost_password', 'Lost Password?', 'id="login_lost_btn" class="btn btn-link"'); ?>
                            </div>
				        </div>
                    <?php echo form_close(); ?>
                    <!-- End # Login Form -->
                    
                    <!-- Begin | Lost Password Form -->
                    <form id="lost-form" style="display:none;">
    	    		    <div class="modal-body">
		    				<div id="div-lost-msg">
                                <div id="icon-lost-msg" class="glyphicon glyphicon-chevron-right"></div>
                                <span id="text-lost-msg">Type your e-mail.</span>
                            </div>
		    				<?php echo form_input('lost_email', '', 'id="lost_email" class="form-control" placeholder="E-Mail (type ERROR for error effect)" required'); ?>
            			</div>
		    		    <div class="modal-footer">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Send</button>
                            </div> 
                            <div style = "text-align: center; margin: auto;">
                                <?php echo form_button('lost_login_btn', 'Log In', 'id="lost_login_btn" class="btn btn-link"'); ?>
                            </div>
		    		    </div>
                    </form>
                    <!-- End | Lost Password Form -->
                    
                    <!-- Begin | Register Form -->
                    <!--  <form id="register-form" style="display:none;">
            		    <div class="modal-body">
		    				<div id="div-register-msg">
                                <div id="icon-register-msg" class="glyphicon glyphicon-chevron-right"></div>
                                <span id="text-register-msg">Register an account.</span>
                            </div>
		    				<input id="register_username" class="form-control" type="text" placeholder="Username (type ERROR for error effect)" required>
                            <input id="register_email" class="form-control" type="text" placeholder="E-Mail" required>
                            <input id="register_password" class="form-control" type="password" placeholder="Password" required>
            			</div>
		    		    <div class="modal-footer">
                            <div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">Register</button>
                            </div>
                            <div>
                                <button id="register_login_btn" type="button" class="btn btn-link">Log In</button>
                                <button id="register_lost_btn" type="button" class="btn btn-link">Lost Password?</button>
                            </div>
		    		    </div>
                    </form> -->
                    <!-- End | Register Form -->
                </div>
                <!-- End # DIV Form -->
			</div>
		</div>
	</div>
    <!-- END # MODAL LOGIN -->
 </body>
 </html>