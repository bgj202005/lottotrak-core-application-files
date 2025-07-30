<?php 
$this->load->view('components/page_head'); 
// Check if page_m is available before calling active_users
if(isset($page_m) && is_object($page_m)) {
    $page_m->active_users();
}
?>
<body id = "page1">
	 <div class="bg">
			 <div class="container-fluid">
			      <!-- Static navbar -->
			      <!--  <nav class="navbar navbar-default"> -->
			    <header> 
					   <div class="row no-gutters">
					   		<div class="col-lg-5 col-md-6 col-sm-12">
								<section class="text-center text-md-left">
									<?php echo anchor('', strtoupper(config_item('site_name')), 'class="navbar-brand"'); ?>
								</section>
							</div> <!--  class="col-lg-6" -->
							<div class="col-lg-5 col-md-4 col-sm-12 d-none d-md-block desktop-nav">
						   		<nav>
									<?php echo get_menu($menu, $maintenance); ?>
								</nav>							  
						   </div>
               <?php if ($this->session->userdata('member_logged_in')) 
              { ?>
              <div class="col-lg-2 col-md-2 col-sm-12 desktop-auth-buttons">
                <ul id="login_container" class="d-flex justify-content-center justify-content-lg-end align-items-center flex-wrap">
                  <li class="mr-2 mb-1"><span class="text-white">Welcome <span class="user-name"><?=$this->session->userdata('member_first_name'); ?></span></span></li> 
                  <li><div class="sm sm-clean"><?php echo anchor('member/logout/', '<i class="fa fa-power-off"></i> logout', 'class="btn btn-outline-light btn-sm"')?></div></li>
                </ul>
                </div>
              </div> 
        <?php }     
              else
              { ?>
               <div class="col-lg-2 col-md-2 col-sm-12 desktop-auth-buttons">     
                   <div class="d-flex justify-content-center justify-content-lg-end flex-wrap">
                       <button class="btn btn-dark btn-sm mr-2 mb-1" data-toggle="modal" data-target="#sem-login">Login</button>
                       <button class="btn btn-dark btn-sm mb-1" data-toggle="modal" data-target="#sem-reg">Register</button>
                   </div>
               </div>   
        <?php  } ?>
        
        				<!-- Mobile Hamburger Menu -->
        				<button class="mobile-menu-toggle" id="mobileMenuToggle">
        					<span></span>
        					<span></span>
        					<span></span>
        				</button>
        				
						  <div class="clear"></div>
			    </header>
			    
			    <!-- Mobile Navigation Menu -->
			    <div class="mobile-nav-menu" id="mobileNavMenu">
			    	<div class="mobile-menu-header">
			    		<h4 class="text-white mb-0"><?php echo strtoupper(config_item('site_name')); ?></h4>
			    		<button class="mobile-menu-close" onclick="closeMobileMenu()">
			    			<i class="fa fa-times"></i>
			    		</button>
			    	</div>
			    	<div class="mobile-menu-content">
			    		<?php 
			    		// Display mobile menu with better structure
			    		echo '<ul class="mobile-nav-list">';
			    		
			    		// Extract menu items from desktop menu if possible
			    		if(function_exists('get_menu') && isset($menu)) {
			    			$menu_html = get_menu($menu, $maintenance);
			    			// Try to extract links from menu HTML
			    			if(preg_match_all('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>([^<]+)<\/a>/i', $menu_html, $matches)) {
			    				for($i = 0; $i < count($matches[1]); $i++) {
			    					$url = $matches[1][$i];
			    					$title = trim(strip_tags($matches[2][$i]));
			    					echo '<li><a href="' . $url . '"><i class="fa fa-chevron-right"></i> ' . $title . '</a></li>';
			    				}
			    			} else {
			    				// Fallback menu items
			    				echo '<li><a href="' . base_url() . '"><i class="fa fa-home"></i> Home</a></li>';
			    				echo '<li><a href="' . base_url() . 'about"><i class="fa fa-info-circle"></i> About</a></li>';
			    				echo '<li><a href="' . base_url() . 'contact"><i class="fa fa-envelope"></i> Contact</a></li>';
			    				echo '<li><a href="' . base_url() . 'news"><i class="fa fa-newspaper-o"></i> News</a></li>';
			    				echo '<li><a href="' . base_url() . 'results"><i class="fa fa-trophy"></i> Results</a></li>';
			    			}
			    		} else {
			    			// Fallback menu if no menu data
			    			echo '<li><a href="' . base_url() . '"><i class="fa fa-home"></i> Home</a></li>';
			    			echo '<li><a href="' . base_url() . 'about"><i class="fa fa-info-circle"></i> About</a></li>';
			    			echo '<li><a href="' . base_url() . 'contact"><i class="fa fa-envelope"></i> Contact</a></li>';
			    			echo '<li><a href="' . base_url() . 'news"><i class="fa fa-newspaper-o"></i> News</a></li>';
			    			echo '<li><a href="' . base_url() . 'results"><i class="fa fa-trophy"></i> Results</a></li>';
			    		}
			    		
			    		echo '</ul>';
			    		?>
			    	</div>
			    	<div class="mobile-auth-buttons">
			    		<?php if ($this->session->userdata('member_logged_in')) { ?>
			    			<div class="user-welcome text-white mb-3 text-center">
			    				<i class="fa fa-user-circle"></i> Welcome<br>
			    				<strong><?=$this->session->userdata('member_first_name'); ?></strong>
			    			</div>
			    			<?php echo anchor('member/logout/', '<i class="fa fa-power-off"></i> Logout', 'class="btn btn-outline-light btn-block"'); ?>
			    		<?php } else { ?>
			    			<button class="btn btn-primary btn-block mb-2" data-toggle="modal" data-target="#sem-login" onclick="closeMobileMenu()">
			    				<i class="fa fa-sign-in"></i> Login
			    			</button>
			    			<button class="btn btn-outline-primary btn-block" data-toggle="modal" data-target="#sem-reg" onclick="closeMobileMenu()">
			    				<i class="fa fa-user-plus"></i> Register
			    			</button>
			    		<?php } ?>
			    	</div>
			    </div>
			  </div>
			</div><!--/.container-fluid -->
				<div class = "container-fluid">
			    	<section><?php $this->load->view('templates/'.$subview); ?></section>
				</div>
          <!-- The Register Modal -->
          <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-reg">
            <div class="modal-dialog modal-dialog-centered modal-md">
              <div class="modal-content">
                <!-- The Register Account Modal -->
                <div class="modal-body seminor-login-modal-body">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                      <span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                  </button>
                  <h5 class="modal-title text-center mb-4">REGISTER</h5>
            <?php echo form_open('', 'class="seminor-login-form" id="register_form"');  ?>
              <div class="form-group">
                <?php $extra = array('type' => 'text', 'class' => 'form-control', 'id' => 'username', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => 'User name'); 
                echo form_input('username','', $extra); ?>
              </div>
              <div class="form-group">
                <?php $extra = array('type' => 'email', 'class' => 'form-control', 'id' => 'email', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Email'); 
                echo form_input('email','', $extra); ?>
              </div>
              <div class="form-group text-center">
                <?php echo anchor(base_url().'terms-and-conditions/', '<span class="text-primary-fau">By Clicking "REGISTER NOW" you accept our<br>
                  Terms and Conditions</span>', 'class="text-secondary"'); ?>
              </div>
              
                <div id="validation_success_message" class="alert alert-success d-none"></div>
                <div id="validation_error" class="alert alert-danger d-none"></div>

              <div class="btn-check-log">
                    <?php $extra = array('class' => 'btn btn-primary btn-block btn-lg', 'id' => 'register_button');
                  echo form_submit('register', 'REGISTER NOW', $extra); ?>
                </div>
                <div class="text-center mt-3">
                <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-login" data-dismiss="modal">Already Have An Account</span></a>
              </div>
              <?php echo form_close(); ?> <!-- </form> -->
                </div>
              </div>
            </div>
          </div>
            <!-- The Login Modal -->
          <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-login">
            <div class="modal-dialog modal-dialog-centered modal-md">
              <div class="modal-content">
                  <!-- Modal body -->
                  <div class="modal-body seminor-login-modal-body">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                    </button>
                    <h5 class="modal-title text-center mb-4">LOGIN TO MY ACCOUNT</h5>
                <?php echo form_open('', 'class="seminor-login-form" id ="sem_login"'); ?>
              <div class="form-group">
                  <?php $extra = array('class' => 'form-control', 'id' => 'username_login', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Username');  
                echo form_input('username_login','', $extra); ?>
                </div>
                <div class="form-group">
                  <?php $extra = array('class' => 'form-control', 'id' => 'password_login', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Password');  
                echo form_password('password_login','', $extra); ?>
                </div>
                <div class="form-group">
                  <div class="custom-control custom-checkbox">
                    <?php echo form_checkbox('remember_me', 'lsRememberMe','', array('id' => "rememberMe", 'class' => 'custom-control-input')); ?>
                    <label class="custom-control-label" for="rememberMe">Remember Me On This Computer</label>
                  </div>
                </div>
              <div id="login_error_message" class="alert alert-danger d-none"></div>
              <div id="login_success_message" class="alert alert-success d-none"></div>
              <div class="btn-check-log">
                <?php $extra = array('class' => 'btn btn-primary btn-block btn-lg', 'id' => 'login_button', 'onClick' => 'lsRememberMe()"');
                  echo form_submit('login', 'LOGIN', $extra); ?>
                  </div>
                <div class="text-center mt-3">
              <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-forgotpassword" data-dismiss="modal">Forgot Password</span></a>
                </div>
                <div class="text-center mt-2">
                <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-reg" data-dismiss="modal">Create A New Account</span></a></div>
                <?php echo form_close(); ?> <!-- </form> -->
                  </div>
                </div>
              </div>
            </div>
              <!-- The Forgot Password Modal -->
          <div class="modal fade seminor-login-modal" data-backdrop="static" id="sem-forgotpassword">
            <div class="modal-dialog modal-dialog-centered modal-md">
              <div class="modal-content">
                  <!-- Modal body -->
                  <div class="modal-body seminor-login-modal-body">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                  <span aria-hidden="true"><i class="fa fa-times-circle" aria-hidden="true"></i></span>
                </button>
                <h5 class="modal-title text-center mb-4">FORGOT PASSWORD</h5>
                <?php echo form_open('', 'class="seminor-forgotpassword-form" id ="forgot_password"'); ?>
                <div class="form-group">
                <?php $extra = array('class' => 'form-control', 'required' => 'required', 'autocomplete' => 'off', 'placeholder' => 'Email Address');  
                  echo form_input('email_forgot','', $extra); ?>
                </div>
                <div id="forgot_error_message" class="alert alert-danger d-none"></div>
                <div id="forgot_success_message" class="alert alert-success d-none"></div>
                <div class="btn-check-log">
                <?php $extra = array('class' => 'btn btn-primary btn-block btn-lg', 'id' => 'forgotpassword');
                  echo form_submit('forgotpassword', 'RESET PASSWORD', $extra); ?>
                  </div>
              <div class="text-center mt-3">
                <a href="#" class="text-primary-fau"><span data-toggle="modal" data-target="#sem-login" data-dismiss="modal">Back to Sign In</span></a></div>
                    <?php echo form_close(); ?> <!-- </form> -->
                  </div>
              </div>
              </div>
            </div>

  <?php $this->load->view('components/page_tail'); ?>
<script>
$(document).ready(function() {
	// Enhanced form validation and submission
	$('#register_form').on('submit', function(event){
		event.preventDefault();
		$('#validation_error').addClass('d-none');
		$('#validation_success_message').addClass('d-none');
		
		$.ajax({
			url:"<?php echo base_url(); ?>member/register",
			method:"POST",
			data:$(this).serialize(),
			dataType:"json",
			beforeSend:function(){
				$('#register_button').attr('disabled', 'disabled').text('Processing...');
			},
			success:function(data) {
				console.log(data);
				if (data.error) {
					if (data.validation_error != '') {
						$('#validation_error').html(data.validation_error).removeClass('d-none');
					}
				}
				if(data.success) {
					$('#validation_success_message').html(data.success).removeClass('d-none');
					$('#validation_error').addClass('d-none');
					setTimeout(function() {
						window.location.href = "<?php echo base_url(); ?>member/validate_email";
					}, 2000);
					$('#register_form')[0].reset();
				}
			},
			complete: function() {
				$('#register_button').attr('disabled', false).text('REGISTER NOW');
			}
		});
	});

	$('#sem_login').on('submit', function(event){
		event.preventDefault();
		$('#login_error_message').addClass('d-none');
		$('#login_success_message').addClass('d-none');
		
		$.ajax({
			url:"<?php echo base_url(); ?>member/login",
			method:"POST",
			data:$(this).serialize(),
			dataType:"json",
			beforeSend:function(){
				$('#login_button').attr('disabled', 'disabled').text('Logging in...');
			},
			success:function(data) {
				console.log(data);
				if (data.error) {
					if (data.validation_error != '') {
						$('#login_error_message').html(data.validation_error).removeClass('d-none');
					}
				}
				if(data.success) {
					$('#login_success_message').html(data.success).removeClass('d-none');
					$('#login_error_message').addClass('d-none');
					setTimeout(function() {
						window.location.href = "<?php echo base_url(); ?>member/dashboard";
					}, 2000);
					$('#sem_login')[0].reset();
				}
			},
			complete: function() {
				$('#login_button').attr('disabled', false).text('LOGIN');
			}
		});
	});

	$('#forgot_password').on('submit', function(event){
		event.preventDefault();
		$('#forgot_error_message').addClass('d-none');
		$('#forgot_success_message').addClass('d-none');
		
		$.ajax({
			url:"<?php echo base_url(); ?>member/forgotpassword",
			method:"POST",
			data:$(this).serialize(),
			dataType:"json",
			beforeSend:function(){
				$('#forgotpassword').attr('disabled', 'disabled').text('Processing...');
			},
			success:function(data) {
				console.log(data);
				if (data.error) {
					if (data.validation_error != '') {
						$('#forgot_error_message').html(data.validation_error).removeClass('d-none');
					}
				}
				if(data.success) {
					$('#forgot_success_message').html(data.success).removeClass('d-none');
					$('#forgot_error_message').addClass('d-none');
					setTimeout(function() {
						window.location.href = "<?php echo base_url(); ?>member/validate_forgotpassword";
					}, 2000);
					$('#forgot_password')[0].reset();
				}
			},
			complete: function() {
				$('#forgotpassword').attr('disabled', false).text('RESET PASSWORD');
			}
		});
	});
});

// Remember me functionality
const RMCHECK = document.getElementById("rememberMe");
const usernameInput = document.getElementById("username_login");

if (localStorage.checkbox && localStorage.checkbox !== "") {
	RMCHECK.setAttribute("checked", "checked");
	usernameInput.value = localStorage.username;
} else {
	RMCHECK.removeAttribute("checked");
	usernameInput.value = "";
}

function lsRememberMe() {
	if (RMCHECK.checked && usernameInput.value !== "") {
		localStorage.username = usernameInput.value;
		localStorage.checkbox = RMCHECK.value;
	} else {
		localStorage.username = "";
		localStorage.checkbox = "";
	}
}

// Mobile Menu Functionality
function toggleMobileMenu() {
	const menuToggle = document.getElementById('mobileMenuToggle');
	const mobileMenu = document.getElementById('mobileNavMenu');
	
	menuToggle.classList.toggle('active');
	mobileMenu.classList.toggle('active');
	
	// Prevent body scroll when menu is open
	if (mobileMenu.classList.contains('active')) {
		document.body.style.overflow = 'hidden';
	} else {
		document.body.style.overflow = '';
	}
}

function closeMobileMenu() {
	const menuToggle = document.getElementById('mobileMenuToggle');
	const mobileMenu = document.getElementById('mobileNavMenu');
	
	menuToggle.classList.remove('active');
	mobileMenu.classList.remove('active');
	document.body.style.overflow = '';
}

// Add event listeners for mobile menu
document.addEventListener('DOMContentLoaded', function() {
	const menuToggle = document.getElementById('mobileMenuToggle');
	const mobileMenu = document.getElementById('mobileNavMenu');
	
	if (menuToggle) {
		menuToggle.addEventListener('click', toggleMobileMenu);
	}
	
	// Close menu when clicking outside
	if (mobileMenu) {
		mobileMenu.addEventListener('click', function(e) {
			if (e.target === mobileMenu) {
				closeMobileMenu();
			}
		});
	}
	
	// Close menu on window resize if it gets too wide
	window.addEventListener('resize', function() {
		if (window.innerWidth > 767) {
			closeMobileMenu();
		}
	});
});

</script>