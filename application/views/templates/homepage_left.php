<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
	   	<!--  Left Sidebar -->
	   	<div class="col-xs-12 col-md-3 sidebar">
			<?php $this->load->view('sidebar'); ?>
		</div>
            <div class="col-xs-12 col-md-9">
				<?php if (strtolower($page->slug)=='home'&&$page->position=='top_home') 
				{ 
					echo "<div class='row'><H1 style = 'text-align:left'>".$page->title."</H1></div>";
					echo "<div class='row' style = 'margin-left:5%'>".$page->body."</div>"; 
				}	?>
				<div class="row" id= "first_exerpt">	
					<?php if (isset($articles[0])) echo get_excerpt($articles[0], TRUE, 50); ?>	
				</div>	
				<div class="row" id = "lotto_one">	
					<h1>Predicted Canada Lotto 649</h1>
				</div>
				<div class="row" style = "margin-bottom:1em;">	
					<div class = "white_ball_container">
						<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
						<div class="lottery_number">02</div>
					</div>
					<div class = "white_ball_container">
						<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
						<div class="lottery_number">14</div>
					</div>
					<div class = "white_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
							<div class="lottery_number">19</div>
						</div>
					</div>
					<div class = "white_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
							<div class="lottery_number">34</div>
						</div>
					</div>
					<div class = "white_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
							<div class="lottery_number">45</div>
						</div>
					</div>
					<div class = "white_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
							<div class="lottery_number">46</div>
						</div>
					</div>
					<div class = "red_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
							<div class="lottery_number">05</div>
						</div>
					</div>
				</div>
				<div class="row">
					<div class="col-lg-12 col-md-12 col-sm-12">
						<div class="home-tabs-background">
							<div class="home-tabs-title">Lotto Analysis</div>
							<ul class="nav nav-tabs nav-justified mt-0" id="myTab" role="tablist">
								<li class="nav-item">
									<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true">Most Common Numbers</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false">Most Overdue Numbers</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="contact-tab" data-toggle="tab" href="#contact" role="tab" aria-controls="contact" aria-selected="false">Lucky Pairs</a>
								</li>
								<li class="nav-item">
									<a class="nav-link" id="contact-tab2" data-toggle="tab" href="#contact2" role="tab" aria-controls="contact2" aria-selected="false">All Numbers</a>
								</li>
							</ul>
							<div class="tab-content" id="myTabContent">
								<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
									<?php if(isset($most_common_numbers)) {
										echo '<div class="row">';
										echo '<div class="col-md-12">';
										if(is_array($most_common_numbers) && count($most_common_numbers) > 0) {
											echo '<table class="table table-bordered table-striped">';
											echo '<thead>';
											echo '<tr>';
											echo '<th class="text-center">Number</th>';
											echo '<th class="text-center">Times Drawn</th>';
											echo '<th class="text-center">Last Drawn</th>';
											echo '</tr>';
											echo '</thead>';
											echo '<tbody>';
											foreach($most_common_numbers as $number) {
												echo '<tr>';
												echo '<td class="text-center">' . $number->number . '</td>';
												echo '<td class="text-center win-value">' . $number->count . '</td>';
												echo '<td class="text-center">' . $number->last_drawn . '</td>';
												echo '</tr>';
											}
											echo '</tbody>';
											echo '</table>';
										} else {
											echo '<p class="text-center">No data available.</p>';
										}
										echo '</div>';
										echo '</div>';
									} else {
										echo '<p class="text-center">No data available.</p>';
									} ?>
								</div>
								<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
									<?php if(isset($most_overdue_numbers)) {
										echo '<div class="row">';
										echo '<div class="col-md-12">';
										if(is_array($most_overdue_numbers) && count($most_overdue_numbers) > 0) {
											echo '<table class="table table-bordered table-striped">';
											echo '<thead>';
											echo '<tr>';
											echo '<th class="text-center">Number</th>';
											echo '<th class="text-center">Times Drawn</th>';
											echo '<th class="text-center">Last Drawn</th>';
											echo '</tr>';
											echo '</thead>';
											echo '<tbody>';
											foreach($most_overdue_numbers as $number) {
												echo '<tr>';
												echo '<td class="text-center">' . $number->number . '</td>';
												echo '<td class="text-center win-value">' . $number->count . '</td>';
												echo '<td class="text-center">' . $number->last_drawn . '</td>';
												echo '</tr>';
											}
											echo '</tbody>';
											echo '</table>';
										} else {
											echo '<p class="text-center">No data available.</p>';
										}
										echo '</div>';
										echo '</div>';
									} else {
										echo '<p class="text-center">No data available.</p>';
									} ?>
								</div>
								<div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
								<?php if(isset($lucky_pairs)) {
									echo '<div class="row">';
									echo '<div class="col-md-12">';
									if(is_array($lucky_pairs) && count($lucky_pairs) > 0) {
										echo '<table class="table table-bordered table-striped">';
										echo '<thead>';
										echo '<tr>';
										echo '<th class="text-center">Pair</th>';
										echo '<th class="text-center">Times Drawn Together</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										foreach($lucky_pairs as $pair) {
											echo '<tr>';
											echo '<td class="text-center">' . $pair->number1 . ' - ' . $pair->number2 . '</td>';
											echo '<td class="text-center win-value">' . $pair->count . '</td>';
											echo '</tr>';
										}
										echo '</tbody>';
										echo '</table>';
									} else {
										echo '<p class="text-center">No data available.</p>';
									}
									echo '</div>';
									echo '</div>';
								} else {
									echo '<p class="text-center">No data available.</p>';
								} ?>
								</div>
								<div class="tab-pane fade" id="contact2" role="tabpanel" aria-labelledby="contact-tab2">
								<?php if(isset($all_numbers_with_stats)) {
									echo '<div class="row">';
									echo '<div class="col-md-12">';
									if(is_array($all_numbers_with_stats) && count($all_numbers_with_stats) > 0) {
										echo '<table class="table table-bordered table-striped">';
										echo '<thead>';
										echo '<tr>';
										echo '<th class="text-center">Number</th>';
										echo '<th class="text-center">Times Drawn</th>';
										echo '<th class="text-center">Last Drawn</th>';
										echo '</tr>';
										echo '</thead>';
										echo '<tbody>';
										foreach($all_numbers_with_stats as $number) {
											echo '<tr>';
											echo '<td class="text-center">' . $number->number . '</td>';
											echo '<td class="text-center win-value">' . $number->count . '</td>';
											echo '<td class="text-center">' . $number->last_drawn . '</td>';
											echo '</tr>';
										}
										echo '</tbody>';
										echo '</table>';
									} else {
										echo '<p class="text-center">No data available.</p>';
									}
									echo '</div>';
									echo '</div>';
								} else {
									echo '<p class="text-center">No data available.</p>';
								} ?>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- Home Page Bottom Sections -->
				<?php if (!empty($page_bottom_left) || !empty($page_bottom_middle) || !empty($page_bottom_right) || !empty($page_bottom_full)): ?>
				<div class="bottom-sections">
					<!-- Check if full width bottom section exists -->
					<?php if (!empty($page_bottom_full)): ?>
						<div class="bottom-section-full">
							<div class="bottom-full-content">
								<div class="bottom-section-title">
									<h2><?php echo $page_bottom_full->title; ?></h2>
								</div>
								<div class="bottom-section-body">
									<?php echo $page_bottom_full->body; ?>
								</div>
							</div>
						</div>
					<?php else: ?>
						<!-- Three column layout -->
						<div class="row">
							<?php if (!empty($page_bottom_left)): ?>
							<div class="col-md-4 bottom-section-left">
								<div class="bottom-left-content">
									<div class="bottom-section-title">
										<h3><?php echo $page_bottom_left->title; ?></h3>
									</div>
									<div class="bottom-section-body">
										<?php echo $page_bottom_left->body; ?>
									</div>
								</div>
							</div>
							<?php endif; ?>
							
							<?php if (!empty($page_bottom_middle)): ?>
							<div class="col-md-4 bottom-section-middle">
								<div class="bottom-middle-content">
									<div class="bottom-section-title">
										<h3><?php echo $page_bottom_middle->title; ?></h3>
									</div>
									<div class="bottom-section-body">
										<?php echo $page_bottom_middle->body; ?>
									</div>
								</div>
							</div>
							<?php endif; ?>
							
							<?php if (!empty($page_bottom_right)): ?>
							<div class="col-md-4 bottom-section-right">
								<div class="bottom-right-content">
									<div class="bottom-section-title">
										<h3><?php echo $page_bottom_right->title; ?></h3>
									</div>
									<div class="bottom-section-body">
										<?php echo $page_bottom_right->body; ?>
									</div>
								</div>
							</div>
							<?php endif; ?>
						</div>
					<?php endif; ?>
				</div>
				<?php endif; ?>
				<!--  end Home Page Bottom Sections -->
			</div>
        <div class="wrapper">
            <div class="content-menu">
            	<?php echo get_footer_menu($footer_menu_inside, $maintenance); ?>
            	
                <div class="clear"></div>
                </div>
	       </div>
        </div>
    </div>
</section>
