<?php $this->load->view('admin/components/page_head'); ?>
<body>
 <nav class="navbar navbar-static-top navbar-inverse">
  <div class="container-fluid">
    <!-- Brand and toggle get grouped for better mobile display -->
    <div class="navbar-header">
     
      <a class="navbar-brand" href="<?php echo site_url('admin/dashboard');?>"><?php echo $meta_title; ?></a>
    </div>

    <!-- Collect the nav links, forms, and other content for toggling -->
    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
      <ul class="nav navbar-nav">
        <li class="active"><a href="<?php echo site_url('admin/dashboard');?>">Dashboard<span class="sr-only">(current)</span></a></li>
        <li><?php echo anchor('admin/page', 'Pages');?></li>
        <li><?php echo anchor('admin/article', 'News Articles');?></li>
        <li><?php echo anchor('admin/lotteries', 'Lotteries');?></li>
        <li><?php echo anchor('admin/page/order/0', 'Header Menu Order ');?></li>
        <li><?php echo anchor('admin/page/order/1', 'Footer Insider Menu Order ');?></li>
        <li><?php echo anchor('admin/page/order/2', 'Footer Outside Menu Order ');?></li>
        <li><?php echo anchor('admin/user', 'Users');?></li>
        <li><?php echo anchor_popup(base_url(), '<span class="glyphicon glyphicon-globe"></span>')?></li>        
      </ul>
    </div><!-- /.navbar-collapse -->
  </div><!-- /.container-fluid -->

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
		    	<section>
		    		<?php echo anchor('admin/user/login', '<span class="glyphicon glyphicon-user"></span>  '.$this->session->userdata['email']); ?><br>
		    		<?php echo anchor('admin/user/logout', '<span class="glyphicon glyphicon-off"></span>   logout')?>
		    	</section>
		    </div>
		</div>
    </div>
    
<?php $this->load->view('admin/components/page_tail'); ?>