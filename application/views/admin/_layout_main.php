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
      <ul class="navbar-nav">
        <li class="nav-item<?= ($current=='dashboard' ? ' active' : ''); ?>" style ="margin-top:8px;"><a href="<?php echo site_url('admin/dashboard');?>">Dashboard</a></li>
        <li class="nav-item<?= ($current=='page' ? ' active' : ''); ?>"><?php echo anchor('admin/page', 'Pages', 'class = "nav-link"');?></li>
        <li class="nav-item<?= ($current=='article' ? ' active' : ''); ?>"><?php echo anchor('admin/article', 'Lottery News Articles','class = "nav-link"');?></li>
        <li class="nav-item<?= ($current=='membership' ? ' active' : ''); ?>"><?php echo anchor('admin/membership', 'Members', 'class = "nav-link"');?></li>
        <li class="nav-item dropdown<?= ($current=='lotteries' ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/lotteries', 'Lotteries', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/lotteries', 'View Lotteries', 'class = "dropdown-item"'); ?>
            <?php echo anchor('admin/lotteries/edit', 'Add New Lottery Profile', 'class = "dropdown-item"'); ?>
          </div> 
        </li>
        <li class="nav-item dropdown<?= ($current=='statistics' ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/statistics', 'Statistics', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/lotteries', 'View Lottery Stats', 'class = "dropdown-item"'); ?>
          </div> 
        </li>
        <li class="nav-item dropdown<?= ($current=='predictions' ? ' active' : ''); ?>">
        <?php $attr = array('class' => "nav-link dropdown-toggle", 'id' => "navbarDropdown", 'role'=> "button", 
        'data-toggle'=> "dropdown",  'aria-haspopup' => "true", 'aria-expanded' => "false");
        echo anchor('admin/predictions', 'Predictions', $attr);?>
          <div class="dropdown-menu" aria-labelledby="navbarDropdown">
            <?php echo anchor('admin/lotteries', 'View Lottery Predictions', 'class = "dropdown-item"'); ?>
          </div> 
        </li>
        <li class="nav-item<?= ($current=='0' ? ' active' : ''); ?>"><?php echo anchor('admin/page/order/0', 'Header Order ', 'class = "nav-link"');?></li>
        <li class="nav-item<?= ($current=='1' ? ' active' : ''); ?>"><?php echo anchor('admin/page/order/1', 'Footer Inside Order ', 'class = "nav-link"');?></li>
        <li class="nav-item<?= ($current=='2' ? ' active' : ''); ?>"><?php echo anchor('admin/page/order/2', 'Footer Outside Order ', 'class = "nav-link"');?></li>
        <li class="nav-item<?= ($current=='user' ? ' active' : ''); ?>"><?php echo anchor('admin/user', 'Admins', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor_popup(base_url(), '<i class="fa fa-globe" style="color:#fff; padding: 5px; margin-top:5px;"></i>')?></li>        
      </ul>
    </div><!-- /.navbar-collapse -->
  <!-- </div> --><!-- /.container-fluid -->
 </nav>
    <div class = "container-fluid">
    	<div class = "row">
		    <!--  Main Column -->
		    	<div class = "col-md-8" id="sandbox-container">
		    		<section><!--   <h3><?php //echo $status; ?></h3> -->
		    		 </section>
		    		<section>
		    			<?php   $this->load->view($subview); ?>
		    		</section>
		    	</div>
		    <!--  Sidebar -->
		    	<div class = "col-md-4">
		    	<section style = "padding: 20px; white-space: nowrap;">
		    		<?php echo anchor('admin/user/edit/'.$this->session->userdata['id'], '<i class="fa fa-user" style="margin-right:15px;"></i> '.$this->session->userdata['email']); ?><br>
		    		<?php echo anchor('admin/user/logout', '<i class="fa fa-power-off" style="margin-right:15px;"></i>   logout')?>
		    	</section>
		    </div>
		</div>
    </div>
    
<?php $this->load->view('admin/components/page_tail'); ?>