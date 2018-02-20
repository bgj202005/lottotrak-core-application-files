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
                                                    'value' => '')); 
                                                    echo form_error('badusername'); ?>    
                                                </div>
            									<div class="form-group">
            										
            									   <?php echo form_input(array('id' => 'email', 'type' => 'email', 'tabindex' => '2', 'class' => 'form-control', 'placeholder' => 'Email',
                                                    'value' => '')); 
                                                    echo form_error('bademail'); ?> 
                                                </div>
            									<div class="form-group">
            										
            									   <?php echo form_password(array('id' => 'password', 'tabindex' => '3', 'class' => 'form-control', 'placeholder' => 'Password',
                                                    'value' => '')); 
                                                    echo form_error('badpassword'); ?> 
                                                </div>
            									<div class="form-group">
            										 
            									   <?php echo form_password(array('id' => 'password_confirm', 'tabindex' => '4', 'class' => 'form-control', 'placeholder' => 'Confirm Password',
                                                    'value' => '')); ?>
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
                                                    'value' => '')); 
                                                    echo form_error('badpassword'); ?> 
                                                </div>
            
                                                <div class="form-group">
                                                    <div class="row">
                                                        <div class="col-xs-7">
                                                            <input type="checkbox" tabindex="3" name="remember" id="remember">
                                                            <label for="remember"> Remember Me</label>
                                                        </div>
                                                        <div class="col-xs-5 pull-right">
                                                            <input type="submit" name="login-submit" id="login-submit" tabindex="4" class="form-control btn btn-primary" value="Log In">
                                                        	<?php echo form_error('badusernamepassword', 'This is a problem'); ?> 
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
	event.preventDefault();
	al
	var _form = $(this);
	
	var data = {
		email: $("input[type='email']", _form).val(),
		password: $("input[type='password']", _form).val()
	}
	
	console.log=(data);
	
})
</script>


 <!-- <script>
            document.addEventListener("DOMContentLoaded", function(event) { 

              // process the form
              $('#ajax-register-form').submit(function(event) {

                  // get the form data
                  // note, I've added ID tags to your form above

                var u = $('#username').val();
                var p = $('#password').val();

                  $.ajax({
                      url : "http://localhost/lottotrak/register", //enter the login controller URL here
                      type : "POST",
                      dataType : "json",
                      data : {
                          username : u, 
                          password : p
                          },
                      success : function(data) {
                          // do something, e.g. hide the login form or whatever
                          alert('logged in');
                      },
                      error : function(data) {
                          // do something
                          alert('pooped the bed');
                      }
                  });
                  // stop the form from submitting the normal way and refreshing the page
                  return false;
              });
            }); 
        </script>
 
-->

    
<?php $this->load->view('components/page_tail'); ?>