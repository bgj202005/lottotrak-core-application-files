<?php $this->load->view('components/page_head'); ?>
<body id = "page1">
	 <div class="bg">
			 <div class="container">
			      <!-- Static navbar -->
			      <!--  <nav class="navbar navbar-default"> -->
			       <header> 
			       <div class="row">
			          <div class="col-lg-5">
			            <section>
			            	<?php echo anchor('', strtoupper(config_item('site_name')), 'class="navbar-brand"'); ?>
			          	</section>
			          </div> <!--  class="col-lg-6" -->
			          <div class = "col-lg-6">
			          <nav id="main-menu">
			              <?php echo get_menu($menu); ?>
    			           <ul class="nav navbar-nav navbar-inverse navbar-right">
                                <li class="dropdown">
                                    <a href="register" class="dropdown-toggle" data-toggle="dropdown">Register <span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-lr animated flipInX" role="menu">
                                        <div class="col-lg-12">
                                            <div class="text-center"><h3><b>Register</b></h3></div>
            								<?php echo form_open('page/register',array('id'=>'ajax-register-form', 'action' => 'register', 'method' => 'post', 'role' => 'form', 'autocomplete' => 'off')); ?>
            									<div class="form-group">
            									    <?php echo form_input(array('id' => 'username', 'tabindex' => '1', 'class' => 'form-control', 'placeholder' => 'Username',
                                                    'value' => '')); ?>    
                                                </div>
            									<div class="form-group">
            										
            									   <?php echo form_input(array('id' => 'email', 'type' => 'email', 'tabindex' => '2', 'class' => 'form-control', 'placeholder' => 'Email',
                                                    'value' => '')); ?> 
                                                </div>
            									<div class="form-group">
            										
            									   <?php echo form_password(array('id' => 'password', 'tabindex' => '3', 'class' => 'form-control', 'placeholder' => 'Password',
                                                    'value' => '')); ?> 
                                                </div>
            									<div class="form-group">
            										 
            									   <?php echo form_password(array('id' => 'password_confirm', 'tabindex' => '4', 'class' => 'form-control', 'placeholder' => 'Confirm Password',
                                                    'value' => '')); ?>
                                                </div>
            									<div class="form-group">
													<div class="alert alert-danger js-reg-error" style = "display:none"></div>
												</div>
											     <div class="form-group">
            										<div class="row">
            											<div class="col-xs-6 col-xs-offset-3">
															<input type="submit" name="register-submit" id="register-submit" tabindex="5" class="form-control btn btn-primary" value="Register Now">

            											</div>
            										</div>
            									</div>
            								<?php echo form_close(); ?>
                                            <!-- </form> -->
                                        </div>
                                    </ul>
                                </li>
                                <li class="dropdown">
                                    <a href="/login" class="dropdown-toggle" data-toggle="dropdown">Log In <span class="caret"></span></a>
                                    <ul class="dropdown-menu dropdown-lr animated slideInRight" role="menu">
                                        <div class="col-lg-12">
                                            <div class="text-center"><h3><b>Log In</b></h3></div>
                                            <?php echo form_open('page/login',array('id'=>'login-form', 'action' => 'login', 'method' => 'post', 'role' => 'form', 'autocomplete' => 'off')); ?>
											
                                                <div class="form-group">
                                                    <label for="username">Username</label>
                                                    <?php echo form_input(array('id' => 'username_login', 'tabindex' => '1', 'class' => 'form-control', 'placeholder' => 'Username',                                   'value' => '')); ?>
											</div>
            
                                                <div class="form-group">
                                                    <label for="password">Password</label>
                                                      <?php echo form_password(array('id' => 'password_login', 'tabindex' => '2', 'class' => 'form-control', 'placeholder' => 'Password',
                                                    'value' => '')); ?>
                                                </div>
            
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-7">
                                                            <input type="checkbox" tabindex="3" name="remember" id="remember">
                                                            <label for="remember"> Remember Me</label>
                                                        </div>
                                                        <div class="form-group">
														<?php echo validation_errors('<div class="alert alert-danger js-login-error">', '</div>'); ?>
														</div>
														<div class="col-xs-5 pull-right">
                                                            <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-primary" value="Log In">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-lg-12">
                                                            <div class="text-center">
                                                                <a href="/login/recover" tabindex="5" class="forgot-password">Forgot Password?</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                   </ul>
                               </li>
                           </ul>
			           </nav>
			          </div>
			          <div class="clear"></div>
			          </header>
			          </div>
			        </div><!--/.container-fluid -->
				<div class = "container-fluid">
			    	<section><?php $this->load->view('templates/'.$subview); ?></section>
				</div>
		    </div>
    </div>

 <script>
$(document)
.on("submit", "#ajax-register-form", function(event) {
	event.defaultPrevented;
	
	var _form = $(this);
	var _error = $(".js-reg-error", _form);
	var dataObj = {
		username: $("input[id='username']", _form).val(),
		email: $("input[type='email']", _form).val(),
		password: $("input[type='password']", _form).val(),
		password_confirm: $("input[id='password_confirm']", _form).val()
	} 
	
	if (!dataObj.username || !dataObj.email || !dataObj.password || !dataObj.password_confirm) {
		_error
			.text("No Field(s) can be left blank.")
			.show();
		return false;
	} else if (dataObj.email.length < 16) {
		_error
			.text("Please enter a longer email address.")
			.show();
		return false;
	} else if (dataObj.password.length < 8) {
		_error
			.text("Please enter a passphrase at least 8 characters for the password.")
			.show();
		return false;
	} else if (dataObj.password != dataObj.password_confirm) {
		_error
		.text("Password and the confirmed password don't match.")
		.show();
		return false;
	}

	_error.hide(); 
	
	 $.ajax({
		type: 'POST',
		url: '<?php // echo site_url(); ?>page/register',
		data: dataObj,
		dataType: 'json',
		async: true,
	}) 
	
	.done(function ajaxDone(data) {
		  // whatever the data is
		console.log(data);
		if (data.redirect !== undefined) {
			window.location = data.redirect;
		} 
	})
    
	.fail(function ajaxFailed(e) {
		// This failed
		console.log(e);
	})
	
	.always(function ajaxAlwaysDoThis(data) {
		// This will alway do
		console.log('Always');
	})
	
	return false;
})
</script>

<?php $this->load->view('components/page_tail'); ?>