<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-md-8">
			<div class="row">	
						<?php if (isset($articles[0])) echo get_excerpt($articles[0], TRUE, 50); ?>
			</div>
			<div class="row">
				<div class="col-md-4">
						<?php if (isset($articles[1])) echo get_excerpt($articles[1], FALSE, 30); ?>
					</div>
				<div class="col-md-4">
						<?php if (isset($articles[2])) echo get_excerpt($articles[2], FALSE, 30); ?>
					</div>
				<div class="col-md-4">
						<?php if (isset($articles[3])) echo get_excerpt($articles[3], FALSE, 30); ?>
					</div>
			</div>
				<div class="row">
					<div class="col-md-4">
							<?php if (isset($articles[4])) echo get_excerpt($articles[4], FALSE, 30); ?>
					</div>
					<div class="col-md-4">
							<?php if (isset($articles[5])) echo get_excerpt($articles[5], FALSE, 30); ?>
					</div>
				</div>
			</div>
	
			<!--  Sidebar -->
			<div class="col-xs-12 col-md-4 sidebar">
				<section>
					<h2>Latest Lottery News</h2>
							<?php // echo anchor($news_article_link, '+ News archive'); ?>
							<?php $this->load->view('sidebar'); ?>
				</section>
					<div class="left-pad top-pad" style = "margin-right: 30px;">
						<div class="wrapper">
							<a href="#"><img class = "home" src="<?php echo base_url() ?>images/banner-1.jpg" alt=""></a>
							<a href="#"><img class = "home" src="<?php echo base_url() ?>images/banner-2.jpg" alt=""></a>
						</div>
						<a class="link" href="#">More propositions</a>
					</div>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="wrapper">
						<div class="wrapper indent-bot4">
								 <div class="left-pad">
									<h3 class="indent-bot">best software &amp; game <strong>selection</strong></h3>
									<figure class="img-pos indent-bot float-sw2"><img src="<?php echo base_url() ?>images/page1-img1.jpg" alt=""></figure>
									<p class="bot-indent">Lorem ipsum dolor sit amet, consec tetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi.</p>
									<a class="link" href="#">Read more</a>
								</div>
							</div>
					</div>
				</div>
					<div class="col-md-4">
						<div class="left-pad">
							<h3 class="indent-bot">free download &amp; play <strong>start today!</strong></h3>
							<figure class="img-pos indent-bot float-sw2"><img src="<?php echo base_url() ?>images/page1-img2.jpg" alt=""></figure>
							<p class="bot-indent">Lorem ipsum dolor sit amet, consec tetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi.</p>
							<a class="link" href="#">Read more</a>
						</div>
					</div>
			    </div>
			</div>
			<div class="wrapper">
				<div class="content-menu">
					<?php echo get_footer_menu($footer_menu_inside); ?>
					<div class="clear"></div>
				</div>
			</div>
          </div>
	</section>
