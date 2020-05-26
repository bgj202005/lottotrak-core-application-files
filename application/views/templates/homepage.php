<!--  Main Content -->
<section id="content">
    <div class="content-inner  col-centered">
	   <div class="row">
            <div class="col-xs-12 col-md-8">
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
							<div class="lottery_number">23</div>
						</div>
					</div>
					<div class = "white_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
							<div class="lottery_number">31</div>
						</div>
					</div>
					<div class = "white_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/white_ball.png">
							<div class="lottery_number">45</div>
						</div>
					</div>
				</div>
				<div class="row home-tabs-background">
					<p class = "home-tabs-title">Overall Historic Prediction Wins</p>
					<div class = "home-tabs">
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-wins-tab" data-toggle="tab" href="#nav-wins" role="tab" aria-controls="nav-wins" aria-selected="true">Wins</a>
							<a class="nav-item nav-link" id="nav-draws-tab" data-toggle="tab" href="#nav-draws" role="tab" aria-controls="nav-draws" aria-selected="false">Draws</a>
							<a class="nav-item nav-link" id="nav-last-win-tab" data-toggle="tab" href="#nav-last-win" role="tab" aria-controls="nav-last-win" aria-selected="false">Last Win</a>
						</div>
						<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav-wins" role="tabpanel" aria-labelledby="nav-wins-tab">
							<table class="table table-bordered">
								<thead>
									<tr>
									<th class = "wins" scope="col">6/6</th>
									<th class = "wins" scope="col">5/6+</th>
									<th class = "wins" scope="col">5/6</th>
									<th class = "wins" scope="col">4/6</th>
									<th class = "wins" scope="col">2/6+</th>
									<th class = "wins" scope="col">2/6</th>
									<th class = "wins" scope="col">2/6+</th>
									</tr> 
								</thead>
								<tbody>
									<tr>
										<td class = "win-value">0</td>
										<td class = "win-value">02</td>
										<td class = "win-value">04</td>
										<td class = "win-value">23</td>
										<td class = "win-value">82</td>
										<td class = "win-value">64</td>
										<td class = "win-value">102</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav-draws" role="tabpanel" aria-labelledby="nav-draws-tab">
						<table class="table table-bordered">
								<tbody>
									<tr>
										<td style = "padding: 25px;"><span class = "draws-title">Number of Draws: </span><span class = "draws-title-number">1862</span></td>
									</tr>
									<tr>
										<td style = "padding: 25px;"><span class = "draw-date-text">Last Draw Date: September 21, 2018</span>
										<span class = "draw-numbers">07 21 23 25 31 45 </span><span class = "draw-date-text">Bonus: </span><span class = "draw-numbers">44</span></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav-last-win" role="tabpanel" aria-labelledby="nav-last-win-tab">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td style = "padding: 25px; text-align:center"><span class = "win-text">Winning Numbers: </span><span class = "number-of-wins">4</span><span class = "win-text"> Numbers</span></td>
									</tr>
									<tr>
										<td style = "padding: 25px;"><span class = "draw-date-text">Last Draw Date: September 21, 2018</span>
										<span class = "draw-numbers">07 <span class = "win-number">21</span> <span class = "win-number">23</span> 25 <span class = "win-number">31</span><span class = "win-number">45</span><span class = "draw-date-text">Bonus: </span> 44</span></span></td>
									</tr>
								</tbody>
							</table>
						</div>
						</div>
					</div>
				</div>
				<div class="row" id = "lotto_one">	
					<h1>Predicted Canada Lotto Max</h1>
				</div>
				<div class="row" style = "margin-bottom:1em;">	
					<div class = "red_ball_container">
						<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
						<div class="lottery_number">05</div>
				</div>
					<div class = "red_ball_container">
						<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
						<div class="lottery_number">19</div>
					</div>
					<div class = "red_ball_container">
						<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
						<div class="lottery_number">22</div>
					</div>
					<div class = "red_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
							<div class="lottery_number">24</div>
						</div>
					</div>
					<div class = "red_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
							<div class="lottery_number">29</div>
						</div>
					</div>
					<div class = "red_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
							<div class="lottery_number">35</div>
						</div>
					</div>
					<div class = "red_ball_container">
						<div class = "blur">
							<img src = "<?php echo base_url(); ?>images/assets/red_ball.png">
							<div class="lottery_number">47</div>
						</div>
					</div>
				</div>
				<div class="row home-tabs-background">
					<p class = "home-tabs-title">Overall Historic Prediction Wins</p>
					<div class = "home-tabs">
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav2-wins-tab" data-toggle="tab" href="#nav2-wins" role="tab" aria-controls="nav2-wins" aria-selected="true">Wins</a>
							<a class="nav-item nav-link" id="nav2-draws-tab" data-toggle="tab" href="#nav2-draws" role="tab" aria-controls="nav2-draws" aria-selected="false">Draws</a>
							<a class="nav-item nav-link" id="nav-last-win-tab" data-toggle="tab" href="#nav2-last-win" role="tab" aria-controls="nav2-last-win" aria-selected="false">Last Win</a>
						</div>
						<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav2-wins" role="tabpanel" aria-labelledby="nav2-wins-tab">
							<table class="table table-bordered">
								<thead>
									<tr>
									<th class = "wins" scope="col">7/7</th>
									<th class = "wins" scope="col">6/7+</th>
									<th class = "wins" scope="col">6/7</th>
									<th class = "wins" scope="col">5/7</th>
									<th class = "wins" scope="col">4/7</th>
									<th class = "wins" scope="col">3/7+</th>
									<th class = "wins" scope="col">3/7</th>
									</tr> 
								</thead>
								<tbody>
									<tr>
										<td class = "win-value">0</td>
										<td class = "win-value">01</td>
										<td class = "win-value">02</td>
										<td class = "win-value">13</td>
										<td class = "win-value">68</td>
										<td class = "win-value">89</td>
										<td class = "win-value">98</td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav2-draws" role="tabpanel" aria-labelledby="nav2-draws-tab">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td style = "padding: 25px;"><span class = "draws-title">Number of Draws: </span><span class = "draws-title-number">869</span></td>
									</tr>
									<tr>
										<td style = "padding: 25px;"><span class = "draw-date-text">Last Draw Date: Nov 12, 2019</span>
										<span class = "draw-numbers">01 14 16 22 32 39 45</span><span class = "draw-date-text">Bonus: </span><span class = "draw-numbers">31</span></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav2-last-win" role="tabpanel" aria-labelledby="nav2-last-win-tab">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td style = "padding: 25px; text-align:center"><span class = "win-text">Winning Numbers: </span><span class = "number-of-wins">3+B</span><span class = "win-text"> Numbers</span></td>
									</tr>
									<tr>
										<td style = "padding: 25px;"><span class = "draw-date-text">Last Draw Date: Nov 12, 2019</span>
										<span class = "draw-numbers">01 <span class = "win-number">14</span> <span class = "win-number">16</span> 22 <span class = "win-number">32</span> 39 45<span class = "draw-date-text">Bonus: </span><span class = "win-number"> 31</span></span></td>
									</tr>
								</tbody>
							</table>
						</div>
						</div>
					</div>
				</div>
				<div style = "margin-top: 2em;" class="row home-tabs-background">
					<p class = "home-tabs-title">Latest Lottery Results</p>
					<div class = "home-tabs">
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							<a class="nav-item nav-link active" id="nav-lotto-1-tab" data-toggle="tab" href="#nav-lotto-1" role="tab" aria-controls="nav2-wins" aria-selected="true">Canada 649</a>
							<a class="nav-item nav-link" id="nav-lotto-2-tab" data-toggle="tab" href="#nav-lotto-2" role="tab" aria-controls="nav-lotto-2" aria-selected="false">LottoMax</a>
						</div>
						<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade show active" id="nav-lotto-1" role="tabpanel" aria-labelledby="nav-lotto-1-tab">
						<table class="table table-bordered">
								<tbody>
									<tr>
										<td style = "padding:30px; text-align:center;"><span class = "draw-date">September 21, 2018</span></td>
									</tr>
									<tr>
										<td style = "padding: 25px;"><span class = "draw-date-numbers">01 14 16 22 32 45 <span class = "draw-date"> Bonus:</span><span class = "draw-date-numbers"> 31</span></span></td>
									</tr>
								</tbody>
							</table>
						</div>
						<div class="tab-pane fade" id="nav-lotto-2" role="tabpanel" aria-labelledby="nav-lotto-2-tab">
							<table class="table table-bordered">
								<tbody>
									<tr>
										<td style = "padding:30px; text-align:center;"><span class = "draw-date">November 12, 2019</span></td>
									</tr>
									<tr>
										<td style = "padding: 25px;"><span class = "draw-date-numbers">01 14 16 22 32 39 45<span class = "draw-date"> Bonus: </span><span class = "draw-date-numbers">31</span></span></td>
									</tr>
								</tbody>
							</table>
						</div>
						</div>
					</div>
				</div>

			</div>
			<!--  Sidebar -->
			<div class="col-xs-12 col-md-4 sidebar">
				<?php $this->load->view('sidebar'); ?>
			</div>
			<div class="row">
				<div class="col-md-4">
					<div class="wrapper">
						<div class="wrapper indent-bot4">
								 <div class="left-pad">
								 <?php if ($page_bottom_left) 
									{ 
										echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_left->title."</H1></div>";
										echo "<div class='row' style = 'margin-left:5%'>".$page_bottom_left->body."</div>"; 
									}	?>
								 	<!-- <h3 class="indent-bot">best software &amp; game <strong>selection</strong></h3>
									<figure class="img-pos indent-bot float-sw2"><img src="<?php //echo base_url() ?>images/page1-img1.jpg" alt=""></figure>
									<p class="bot-indent">Lorem ipsum dolor sit amet, consec tetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi.</p>
									<a class="link" href="#">Read more</a> -->
								</div>
							</div>
					</div>
				</div>
					<div class="col-md-4">
						<div class="left-pad">
						<?php if ($page_bottom_right) 
									{ 
										echo "<div class='row'><H1 style = 'text-align:left'>".$page_bottom_right->title."</H1></div>";
										echo "<div class='row' style = 'margin-left:5%'>".$page_bottom_right->body."</div>"; 
							}	?>
							<!-- <h3 class="indent-bot">free download &amp; play <strong>start today!</strong></h3>
							<figure class="img-pos indent-bot float-sw2"><img src="<?php echo base_url() ?>images/page1-img2.jpg" alt=""></figure>
							<p class="bot-indent">Lorem ipsum dolor sit amet, consec tetuer adipiscing elit. Praesent vestibulum molestie lacus. Aenean nonummy hendrerit mauris. Phasellus porta. Fusce suscipit varius mi.</p>
							<a class="link" href="#">Read more</a> -->
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
<script>
$('#nav-wins a').on('click', function (e) {
  e.preventDefault()
  $(this).tab('show')
})
$('#nav-draws a').on('click', function (e) {
  e.preventDefault()
  $(this).tab('show')
})
$('#nav-last-win a').on('click', function (e) {
  e.preventDefault()
  $(this).tab('show')
})

</script>