<?php $this->load->view('admin/components/page_head'); ?>
<body>
 <!-- <nav class="navbar navbar-static-top navbar-inverse"> -->
 <nav class="navbar navbar-expand-lg navbar-dark bg-dark">  
    <a class="navbar-brand" href="<?php echo site_url('admin/dashboard');?>"><?php echo $meta_title; ?></a>
    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    <span class="navbar-toggler-icon"></span>
  </button>
    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item active" style ="margin-top:8px;"><a href="<?php echo site_url('admin/dashboard');?>">Dashboard<span class="sr-only">(current)</span></a></li>
        <li class="nav-item"><?php echo anchor('admin/page', 'Pages', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/article', 'Lottery News Articles','class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/membership', 'Members', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/lotteries', 'Lotteries', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/page/order/0', 'Header Menu Order ', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/page/order/1', 'Footer Insider Menu Order ', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/page/order/2', 'Footer Outside Menu Order ', 'class = "nav-link"');?></li>
        <li class="nav-item"><?php echo anchor('admin/user', 'Admins', 'class = "nav-link"');?></li>
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
		    	<section style = "padding: 10px;">
		    		<?php echo anchor('admin/user/login', '<i class="fa fa-user"></i> '.$this->session->userdata['email']); ?><br>
		    		<?php echo anchor('admin/user/logout', '<i class="fa fa-power-off"></i>   logout')?>
		    	</section>
		    </div>
		</div>
    </div>
    
<?php $this->load->view('admin/components/page_tail'); ?>