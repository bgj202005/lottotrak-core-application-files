<?php $this->load->view('admin/components/page_head'); ?>
<body style = "background-color:#f5f6fa;">
 <!-- <nav class="navbar navbar-static-top navbar-inverse"> -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">  
    <a class="navbar-brand" href="<?php echo site_url('admin/dashboard');?>"><?php // echo $meta_title; ?>
    <img src="<?php echo base_url();?>images/lottotrak-logo.png" width="186" height="50" class="d-inline-block align-top" alt="Lottotrak Administration" title = "Lottotrak Administration"></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav" style="font-weight: normal;">
        <li class="nav-item<?= (isset($current) && $current=='dashboard' ? ' active' : ''); ?>" style ="margin-top:8px;"><a href="<?php echo site_url('admin/dashboard');?>" style="font-weight: normal;">Dashboard</a></li>
        <li class="nav-item<?= (isset($current) && $current=='page' ? ' active' : ''); ?>"><?php echo anchor('admin/page', 'Pages', 'class = "nav-link" style="font-weight: normal;"');?></li>
        <li class="nav-item<?= (isset($current) && $current=='article' ? ' active' : ''); ?>"><?php echo anchor('admin/article', 'Lottery News Articles','class = "nav-link" style="font-weight: normal;"');?></li>
        <li class="nav-item<?= (isset($current) && $current=='membership' ? ' active' : ''); ?>"><?php echo anchor('admin/membership', 'Members', 'class = "nav-link" style="font-weight: normal;"');?></li>
        <li class="nav-item dropdown<?= (isset($current) && $current=='lotteries' ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/lotteries', 'Lotteries', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/lotteries', 'View Lotteries', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
            <?php echo anchor('admin/lotteries/edit', 'Add New Lottery Profile', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
          </div> 
        </li>
        <li class="nav-item dropdown<?= (isset($current) && $current=='statistics' ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/statistics', 'Statistics', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/statistics', 'View Lottery Stats', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
            <?php echo anchor('admin/history', 'View Lottery Win History', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
          </div> 
        </li>
        <li class="nav-item dropdown<?= (isset($current) && $current=='predictions' ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/predictions', 'Predictions', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/predictions', 'View Lottery Predictions', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
          </div> 
        </li>
        <li class="nav-item dropdown<?= (isset($current) && ($current=='0'||$current=='1'||$current=='2') ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/menuorder', 'Menu Order', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/page/order/0', 'Header Order', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
            <?php echo anchor('admin/page/order/1', 'Footer Inside Order', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
            <?php echo anchor('admin/page/order/2', 'Footer Outside Order', 'class = "dropdown-item" style="font-weight: normal;"'); ?>
          </div> 
        </li>
        <li class="nav-item<?= (isset($current) && $current=='user' ? ' active' : ''); ?>"><?php echo anchor('admin/user', 'Admins', 'class = "nav-link" style="font-weight: normal;"');?></li>
        <li class="nav-item"><?php echo anchor_popup(base_url(), '<i class="fa fa-globe" style="color:#fff; padding: 5px; margin-top:5px;"></i>')?></li>        
      </ul>
    </div><!-- /.navbar-collapse -->
  <!-- </div> --><!-- /.container-fluid -->
 </nav>
 <style>
	/* Admin navigation and sidebar font weight fixes - High specificity */
	body .navbar-dark .navbar-nav .nav-item a,
	body .navbar-dark .navbar-nav .nav-link,
	body .navbar-nav .nav-item a,
	body .navbar-nav .nav-link,
	body .nav-item a,
	body .nav-link,
	body a.nav-link {
		font-weight: 300 !important;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	
	body .dropdown-item,
	body a.dropdown-item {
		font-weight: 300 !important;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	
	/* Navbar brand */
	body .navbar-brand {
		font-weight: 300 !important;
	}
	
	/* Admin sidebar font weight fixes - Ultra high specificity */
	body .container-fluid .row .col-md-4 section a,
	body .col-md-4 section a,
	body .col-md-4 section span,
	body .col-md-4 section i,
	body .col-md-4 section,
	body .col-md-4 * {
		font-weight: 300 !important;
		-webkit-font-smoothing: antialiased;
		-moz-osx-font-smoothing: grayscale;
	}
	
	/* Override Bootstrap's bold font weights specifically */
	body .navbar-dark .navbar-nav .nav-item.active > .nav-link,
	body .navbar-dark .navbar-nav .nav-item > .nav-link:hover,
	body .navbar-dark .navbar-nav .nav-item > .nav-link:focus,
	body .navbar-nav > .nav-item > .nav-link,
	body .navbar-expand-lg .navbar-nav .nav-link {
		font-weight: 300 !important;
	}
	
	/* Force override any remaining bold styles */
	body nav *,
	body .navbar *,
	body .navbar-nav *,
	body .dropdown-menu * {
		font-weight: 300 !important;
	}

	/* Mobile responsiveness for admin layout */
	@media (max-width: 768px) {
		.container-fluid {
			padding: 5px !important;
			max-width: 100vw !important;
			overflow-x: hidden !important;
		}
		
		.row {
			margin: 0 !important;
		}
		
		.col-md-8, .col-md-4 {
			padding: 3px !important;
			max-width: 100% !important;
		}
		
		/* Stack admin sections on mobile */
		.col-md-4 {
			margin-top: 15px;
		}
	}
	
	@media (max-width: 576px) {
		.container-fluid {
			padding: 2px !important;
		}
		
		.col-md-8, .col-md-4 {
			padding: 1px !important;
		}
	}
 </style>
    <div class = "container-fluid">
    	<div class = "row">
		    <!--  Main Column -->
		    	<div class = "col-md-8" id="sandbox-container">
		    		<section><!--   <h3><?php //echo $status; ?></h3> -->
		    		 </section>
		    		<section>
		    			<?php if(isset($subview)) { $this->load->view($subview); } ?>
		    		</section>
		    	</div>
		    <!--  Sidebar -->
		    	<div class = "col-md-4">
		    	<section style = "padding: 20px; white-space: nowrap; font-weight: normal !important; font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;">
   	    		<?php echo anchor('admin/user/logout', '<i class="fa fa-power-off" style="margin-right:15px; color: #4183C4; text-decoration: none; background-color: transparent;"></i> logout', 'style="font-weight: normal !important;"')?>
            <br />
            <?php echo anchor('admin/user/edit/'.$this->session->userdata['id'], '<i class="fa fa-user" style="margin-right:15px; color: #4183C4; text-decoration: none; background-color: transparent;"></i> '.$this->session->userdata['email'], 'style="font-weight: normal !important;"');?>
            <br />
            <?php if(isset($maintenance) && $maintenance):
                echo anchor('admin/maintenance/', '<i class="fa fa-toggle-off" style="margin-right:10px; color: #4183C4; text-decoration: none; background-color: transparent;"></i> frontend offline', 'style="font-weight: normal !important;"');
                else: 
                echo anchor('admin/maintenance/', '<i class="fa fa-toggle-on" style="margin-right:10px; color: #4183C4; text-decoration: none; background-color: transparent;"></i> frontend online', 'style="font-weight: normal !important;"');
                endif;?><br />
            <i class="fa fa-user-circle-o" aria-hidden="true" style="margin-right:10px; color: #4183C4; text-decoration: none; background-color: transparent;">
            <span style = "margin-left:10px; font-weight: normal;"><span id = "admins" style="font-weight: normal;"><?php echo sprintf("%02d", isset($admins) ? $admins : 0); ?></span> Admins Online</span></i> <br />
            <i class="fa fa-users" aria-hidden="true" style="margin-right:10px; color: #4183C4; text-decoration: none; background-color: transparent;">
            <span style = "margin-left:10px; font-weight: normal;"><span id = "members" style="font-weight: normal;"><?php echo sprintf("%02d", isset($users) ? $users : 0); ?></span> Members Online</span></i><br />
            <i class="fa fa-user-times" aria-hidden="true" style="margin-right:10px; color: #4183C4; text-decoration: none; background-color: transparent;">
            <span style = "margin-left:10px; font-weight: normal;"><span id = "visitors" style="font-weight: normal;"><?php echo sprintf("%02d", isset($visitors) ? $visitors : 0); ?></span> Visitors Online</span></i><br />
		    	<br />
		    	</section>
		    </div>
		</div>
    </div>
  <?php $this->load->view('admin/components/page_tail'); ?>