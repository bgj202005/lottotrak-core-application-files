<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">
<style>
	.card {
    background-color: #ffffff;
    border: 1px solid rgba(0, 34, 51, 0.1);
    box-shadow: 2px 4px 10px 0 rgba(0, 34, 51, 0.05), 2px 4px 10px 0 rgba(0, 34, 51, 0.05);
    border-radius: 0.15rem;
	}
	/* Tabs Card */
	.tab-card {
	border:1px solid #eee;
	}
	.tab-card-header {
	background:none;
	}
	/* Default mode */
	.tab-card-header > .nav-tabs {
	border: none;
	margin: 0px;
	}
	.tab-card-header > .nav-tabs > li {
	margin-right: 2px;
	}
	.tab-card-header > .nav-tabs > li > a {
	border: 0;
	border-bottom:2px solid transparent;
	margin-right: 0;
	color: #737373;
	padding: 2px 15px;
	}
	.tab-card-header > .nav-tabs > li > a.show {
		border-bottom:2px solid #007bff;
		color: #007bff;
	}
	.tab-card-header > .nav-tabs > li > a:hover {
		color: #007bff;
	}
	/* Custom class for nav-tabs */
        .nav-tabs.custom-nav-tabs {
            margin-left: 20px;
            margin-right: 20px;
    }
	.tab-card-header > .tab-content {
	padding-bottom: 0;
	}
	.card-title {
		color:#000000;
	}
	.card-text {
		color:steelblue; 
	}
	table{
  		border: 2px solid #000;
  		border-top: 4px solid #000;
  		width: 100%;
  		margin: 0 auto;
  		margin-bottom: 15px;
  		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
  		table-layout: fixed;
	}
	/* pos */
	table.pos{
 		border: 2px solid #000;
 		border-top: 4px solid #000;
  		width: 100%;
		margin: 0 auto;
		margin-bottom: 15px;
		box-shadow: 0 2px 4px rgba(0,0,0,0.1);
		table-layout: fixed;
	}
	
	/* Center table content */
	table th, table td {
		text-align: center !important;
		vertical-align: middle;
		padding: 8px;
	}
	
	/* Prevent header text wrapping */
	table th {
		white-space: nowrap;
		font-size: 0.85em;
		font-weight: bold;
		min-width: 80px;
		padding: 6px 4px;
	}
	
	/* Smaller font for second row headers to fit within borders */
	table thead tr:nth-child(2) th {
		font-size: 0.75em;
		padding: 4px 2px;
		line-height: 1.2;
	}
	
	/* Ensure data cells have consistent sizing */
	table td {
		font-size: 0.95em;
		min-width: 80px;
	}
	
	/* Table container for mobile-friendly layout */
	.table-container {
		display: flex;
		flex-wrap: wrap;
		gap: 20px;
		justify-content: flex-start;
		align-items: flex-start;
	}
	
	/* Table pair wrapper - groups related tables together */
	.table-pair {
		display: flex;
		gap: 15px;
		flex: 1;
		min-width: 300px;
		align-items: flex-start;
	}
	
	/* Individual table wrapper */
	.table-wrapper {
		flex: 1;
		min-width: 140px;
		display: flex;
		justify-content: center;
		align-items: flex-start;
	}
	
	.table-wrapper table {
		width: 100%;
		max-width: 100%;
	}
	
	/* Extra ball table - full width when present */
	.extra-table-wrapper {
		flex: 1 1 100%;
		min-width: 280px;
		max-width: 400px;
		margin: 0 auto;
		display: flex;
		justify-content: center;
		align-items: flex-start;
	}
	
	.extra-table-wrapper table {
		width: 100%;
		max-width: 100%;
	}
	
	/* Mobile responsiveness */
	@media (max-width: 768px) {
		.table-pair {
			flex-direction: column;
			min-width: 100%;
		}
		
		.table-wrapper {
			flex: 1 1 100%;
			min-width: 100%;
		}
		
		.extra-table-wrapper {
			min-width: 100%;
			max-width: 100%;
		}
		
		.table-container {
			flex-direction: column;
		}
	}
	
	@media (min-width: 769px) and (max-width: 1200px) {
		.table-pair {
			flex: 1 1 100%;
			margin-bottom: 20px;
		}
	}
	th.datafont{
		text-align:center;
		font-size: 0.95em;
	}
	td.datafont { 
		font-size: 	0.95em;
		text-align: center; 
		white-space: nowrap;
	}
	.last-draws {
		margin-top:3em;
		text-align: center;
	}
	.h1, .h2, .h3, .h4, .h5, .h6, h1, h2, h3, h4, h5, h6 {
    color: #000000;
	}	
.shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0,0,0,.075)!important;
	}
.nav-tabs {
  margin: 20px 0;
}
.tab-card-header > .tab-content {
  padding-bottom: 0;
  margin: 25px;
}
/* New styles for tab margins and padding */
    .tab-content {
        margin-left: 20px;
        margin-right: 20px;
        padding-bottom: 20px;
        border: 1px solid #eee; /* Add border around tab content */
        padding: 20px; /* Add padding inside the tab content */
        margin-bottom: 10px; /* Add bottom margin */
        border-radius: 0.15rem; /* Rounded corners for all sides */
        margin-top: -20px; /* Ensure border starts below tabs */
    }
    /* Add border around tabs */
    .nav-tabs {
        border: none; /* Remove border from tabs */
    }
    .nav-tabs .nav-item.show .nav-link, .nav-tabs .nav-link.active {
        border-color: #eee #eee #fff; /* Ensure active tab blends with content border */
    }
    
    /* H-W-C Winners Table Styling - No Horizontal Scroll */
    #hwc-winners-table {
        table-layout: auto;
        width: 100%;
        max-width: 100%;
        word-wrap: break-word;
        overflow-wrap: break-word;
    }
    
    .table-responsive {
        overflow-x: hidden !important;
        overflow-y: auto;
    }
    
    #hwc-winners-table .hwc-row {
        cursor: pointer;
        transition: all 0.2s ease-in-out;
    }
    
    #hwc-winners-table .hwc-row:hover {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3;
        box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        transform: translateY(-1px);
    }
    
    #hwc-winners-table .hwc-row:hover td {
        font-weight: 600;
        color: #1976d2;
    }
    
    /* Flexible column width optimization - no fixed widths */
    #hwc-winners-table th,
    #hwc-winners-table td {
        white-space: nowrap;
        text-align: center;
        padding: 0.5rem 0.25rem;
        font-size: 0.9em;
    }
    
    #hwc-winners-table th.col-rank,
    #hwc-winners-table td.col-rank { 
        min-width: 50px;
        max-width: 60px;
    }
    
    #hwc-winners-table th.col-hot,
    #hwc-winners-table td.col-hot,
    #hwc-winners-table th.col-warm,
    #hwc-winners-table td.col-warm,
    #hwc-winners-table th.col-cold,
    #hwc-winners-table td.col-cold { 
        min-width: 40px;
        max-width: 50px;
    }
    
    #hwc-winners-table th.col-separator,
    #hwc-winners-table td.col-separator { 
        min-width: 15px;
        max-width: 20px;
        padding: 0.5rem 0.1rem;
    }
    
    #hwc-winners-table th.col-prize,
    #hwc-winners-table td.col-prize { 
        min-width: 45px;
        max-width: 65px;
    }
    
    #hwc-winners-table th.col-points,
    #hwc-winners-table td.col-points { 
        min-width: 60px;
        max-width: 70px;
        font-weight: bold;
    }
    
    #hwc-winners-table td.col-rank,
    #hwc-winners-table td.col-hot,
    #hwc-winners-table td.col-separator,
    #hwc-winners-table td.col-warm,
    #hwc-winners-table td.col-cold,
    #hwc-winners-table td.col-prize,
    #hwc-winners-table td.col-points {
        vertical-align: middle;
        padding: 0.5rem 0.25rem;
    }
    
    /* Sortable rank header styling */
    .sortable-header {
        cursor: pointer;
        user-select: none;
        position: relative;
        padding-right: 25px !important;
        transition: background-color 0.2s ease;
    }
    
    .sortable-header:hover {
        background-color: rgba(255,255,255,0.1);
    }
    
    .sort-arrow {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        font-size: 12px;
        opacity: 0.7;
        transition: transform 0.2s ease, opacity 0.2s ease;
    }
    
    .sortable-header:hover .sort-arrow {
        opacity: 1;
    }
    
    .sort-arrow.asc {
        transform: translateY(-50%) rotate(0deg);
    }
    
    .sort-arrow.desc {
        transform: translateY(-50%) rotate(180deg);
    }
    
    /* Enhanced row highlighting */
    #hwc-winners-table tbody tr {
        transition: all 0.2s ease-in-out;
        border-left: 4px solid transparent;
    }
    
    #hwc-winners-table tbody tr:nth-child(odd) {
        background-color: #f9f9f9;
    }
    
    #hwc-winners-table tbody tr:hover {
        background-color: #e3f2fd !important;
        border-left: 4px solid #2196f3 !important;
        box-shadow: 0 2px 8px rgba(33, 150, 243, 0.3);
        transform: scale(1.01);
    }
    
    /* Responsive table for smaller screens - NO HORIZONTAL SCROLL */
    @media (max-width: 992px) {
        #hwc-winners-table {
            font-size: 0.85em;
        }
        
        #hwc-winners-table th,
        #hwc-winners-table td {
            padding: 0.35rem 0.2rem;
        }
    }
    
    @media (max-width: 768px) {
        #hwc-winners-table {
            font-size: 0.75em;
        }
        
        #hwc-winners-table th,
        #hwc-winners-table td {
            padding: 0.25rem 0.1rem;
        }
        
        /* Hide separators on mobile to save space */
        #hwc-winners-table th.col-separator,
        #hwc-winners-table td.col-separator {
            display: none;
        }
        
        /* Compact prize columns on mobile */
        #hwc-winners-table th.col-prize,
        #hwc-winners-table td.col-prize {
            min-width: 35px;
            max-width: 45px;
            font-size: 0.8em;
        }
    }
    
    @media (max-width: 576px) {
        #hwc-winners-table {
            font-size: 0.7em;
        }
        
        #hwc-winners-table th,
        #hwc-winners-table td {
            padding: 0.2rem 0.05rem;
        }
        
        /* Further compress columns for very small screens */
        #hwc-winners-table th.col-rank,
        #hwc-winners-table td.col-rank,
        #hwc-winners-table th.col-hot,
        #hwc-winners-table td.col-hot,
        #hwc-winners-table th.col-warm,
        #hwc-winners-table td.col-warm,
        #hwc-winners-table th.col-cold,
        #hwc-winners-table td.col-cold {
            min-width: 30px;
            max-width: 35px;
        }
        
        #hwc-winners-table th.col-prize,
        #hwc-winners-table td.col-prize {
            min-width: 28px;
            max-width: 35px;
        }
    }
    
    /* Ensure container never overflows */
    .table-responsive {
        overflow-x: hidden !important;
        max-width: 100% !important;
    }
    
    .container-fluid {
        overflow-x: hidden !important;
        max-width: 100% !important;
    }
</style>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/smoothness/jquery-ui.css">
	<script src="//code.jquery.com/jquery-1.12.4.js"></script>
  	<script src="//code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
	<h2><?php echo 'H (Hots), W (Warms) and C (Colds) winners for: '.$lottery->lottery_name; ?></h2>
	<?php $max = $lottery->balls_drawn; 
	   $b = 1; 
	   ?>	
	<h5 style = "text-align:left"><?php echo anchor('admin/history', 'Back to the Win History Dashboard', 'title="Back to History"'); ?></h5>
	<section>
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="card mt-3 tab-card">
						<div id = "error"></div>
						<div class="card-header tab-card-header">
							<div class="d-flex flex-row-reverse">
								<div class="col-md-6">
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">Draw Range: <?=$lottery->last_drawn['range'];?> Draws</h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Extra (Bonus) Ball Included?', 'extra_lb', $extra);
											echo (!empty($lottery->extra_included)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
											?></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $format = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Last Draw Date:', 'extra_lb', $format);
											echo "<br />";
											echo date("l, M-d-Y",strtotime(str_replace('/','-',$lottery->last_drawn['draw_date']))); 
											echo "<br /><br />";
											$mx = 1;
											$extra_ball = 0;	// set extra ball to something, even if not used.
											$xtr = FALSE;
											foreach($lottery->draw as $count => $drawn):
												echo $drawn." ";
												if($xtr) $extra_ball = $drawn; // capture the extra / bonus ball
												$mx++;
												if(($mx>$lottery->balls_drawn)&&($lottery->extra_ball)&&(!$xtr)):
													echo " + ";
													$xtr = TRUE;
												endif;
											endforeach;
											?></h4>
										</div>
									</div>
								</div>
								<div class="col-md-6">
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<div class = "text-center" style = "display:inline-block;">
											<?php 	$hot = array('style' =>'margin-right:10px; color:red;');
													$warm = array('style' =>'margin-right:10px; color:orange;');
													$cold = array('style' =>'color:blue;');
													echo form_label("Hots:", "id => 'lb_hots'", $extra);
													echo form_label($lottery->H, "id => 'hots'", $hot);
													echo form_label("Warms:", "id => 'lb_warms'", $extra);
													echo form_label($lottery->W, "id => 'warms'", $warm);
													echo form_label("Colds:", "id => 'lb_colds'", $extra);
													echo form_label($lottery->C, "id => 'colds'", $cold);
												?>
											</div></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Extra Draws Included?', 'extra_lb', $extra);
											echo (!empty($lottery->extra_draws)) ? form_label(' YES', 'extra_lb', $extra) : form_label(' NO', 'extra_lb', $extra);
											?></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php $extra = array('for' => 'extra_lb', 'style' =>'margin-right:10px;');
											echo form_label('Last Drawn HWC:', 'extra_lb', $extra);
											$hot = array('style' =>'margin-right:10px; color:red;');
													$warm = array('style' =>'margin-right:10px; color:orange;');
													$cold = array('style' =>'color:blue;');
													echo form_label("H:", "id => 'lb_hots'", $extra);
													echo form_label($lottery->hwc[0], "id => 'hots'", $hot);
													echo form_label("W:", "id => 'lb_warms'", $extra);
													echo form_label($lottery->hwc[1], "id => 'warms'", $warm);
													echo form_label("C:", "id => 'lb_colds'", $extra);
													echo form_label($lottery->hwc[2], "id => 'colds'", $cold);
											?></h4>
										</div>
									</div>
									<div class="bg-white card last-draws mb-4 shadow-sm">
										<div class="p-4">
											<h4 class="mb-1">
											<?php 
											$pool_style = array('for' => 'pool_lb', 'style' =>'margin-right:10px;');
											$pool_number_style = array('style' =>'margin-right:10px; color:#007bff; font-weight:bold;');
											echo form_label('Prediction Number Pool:', 'pool_lb', $pool_style);
											
											// Get prediction pool from lottery_h_w_c table prediction_pool field
											$prediction_pool = isset($lottery->prediction_pool) ? $lottery->prediction_pool : 18; // Default to 18 if not available
											
											echo form_label($prediction_pool . ' Numbers', 'pool_numbers', $pool_number_style);
											?></h4>
										</div>
									</div>
								</div>		
							</div>
						</div>
						<ul class="nav nav-tabs" id="myTab" role="tablist" style="margin-left: 20px; margin-right: 20px;">
							<li class="nav-item">
								<a class="nav-link active" id="home-tab" data-toggle="tab" href="#home" role="tab" aria-controls="home" aria-selected="true"><div class = "card-heading">Last Draw</div></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="profile-tab" data-toggle="tab" href="#profile" role="tab" aria-controls="profile" aria-selected="false"><div class = "card-heading">Next Draw</div></a>
							</li>
							<li class="nav-item">
								<a class="nav-link" id="winners-tab" data-toggle="tab" href="#winners" role="tab" aria-controls="winners" aria-selected="false"><div class = "card-heading">H-W-C Winners</div></a>
							</li>
						</ul>
						<div class="tab-content" id="myTabContent">
						<div class="tab-pane fade show active" id="home" role="tabpanel" aria-labelledby="home-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="table-container">
									<!-- Hot Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Last Hots</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE; 
														foreach($lottery->hots_last as $ball => $count):	
														if($ball) :
															echo "<tr class='table-danger'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															if($sym=='*'&&$ball!=$extra_ball) :
																echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																echo "<td class='text-center bg-danger text-white'>".$count."</td>";
															elseif($sym=='*'&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																echo "<td class='text-center bg-info text-white'>".$count."</td>";
															else:
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														else:
															echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Hot Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														foreach($lottery->hots_pos_last as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions_last);
															$position = rtrim($position,'h');  // Remove the special 'h' symbol
															if($exists) :
																if($noEX&&($cntr==$position)):
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																else:	
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																endif;
															else:
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Warm Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Last Warms</th>
													</tr>
													<tr>
														<th class="text-text-center">Ball</th>
														<th class="text-text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php  
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->warms_last as $ball => $count):	
														if($ball) :
															echo "<tr class='table-warning'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															if($sym=='*'&&$ball!=$extra_ball) :
																echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																echo "<td class='text-center bg-danger text-white'>".$count."</td>";
															elseif($sym=='*'&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																echo "<td class='text-center bg-info text-white'>".$count."</td>";
															else:
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														else:
															echo "<tr class='table-warning'><td colspan = '2'>No Warms</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Warm Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->warms_pos_last as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions_last);
															$position = rtrim($position,'w');  // Remove the special '*' symbol
															if($exists) :
																if($noEX&&($cntr==$position)):
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																else:	
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																endif;
															else:
																echo "<td class='text-center'>".$position."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Cold Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Last Colds</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->colds_last as $ball => $count):	
														if($ball) :
															echo "<tr class='table-primary'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															if($sym=='*'&&$ball!=$extra_ball) :
																$ball = rtrim($ball,'*'); // Remove the special '*' symbol
																echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																echo "<td class='text-center bg-danger text-white'>".$count."</td>";
															elseif($sym=='*'&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																echo "<td class='text-center bg-info text-white'>".$count."</td>";
															else:
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
														else:
															echo "<tr class='table-primary'><td colspan = '2'>No Colds</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Cold Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->colds_pos_last as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions_last);
															$position = rtrim($position,'c');  // Remove the special '*' symbol
															if($exists) :
																if($noEX&&($cntr==$position)):
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																else:	
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																endif;
															else:
																echo "<td class='text-center'>".$position."</td>";
																echo "<td class='text-center'>".$count."</td>";
															endif;
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Extra Ball Table (standalone if present) -->
									<?php if(isset($lottery->dupextra_last)) : ?>
									<div class="extra-table-wrapper">
										<table class="table">
											<thead>
												<tr>
													<th class="text-center" colspan="2">Last Extra Ball</th>
												</tr>
												<tr>
													<th class="text-center">Ball</th>
													<th class="text-center">Occurrences</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($lottery->dupextra_last as $ball => $count):	
														echo "<tr class='table-success'>";
														if($ball==$lottery->last_drawn['extra']):
															echo "<td class='text-center bg-info'>".$ball."</td>";
															echo "<td class='text-center bg-info'>".$count."</td>";
														else:
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
														endif;
														echo "</tr>";
												endforeach; ?>
											</tbody>
										</table>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="table-container">
									<!-- Hot Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Next Hots</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE; 
														foreach($lottery->hots as $ball => $count):	
														if($ball) :
															echo "<tr class='table-danger'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
														else:
															echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Hot Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														foreach($lottery->hots_pos as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$position = rtrim($position,'h');  // Remove the special 'h' symbol
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
														endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Warm Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Next Warms</th>
													</tr>
													<tr>
														<th class="text-text-center">Ball</th>
														<th class="text-text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php  
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->warms as $ball => $count):	
														if($ball) :
															echo "<tr class='table-warning'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																echo "<td class='text-center'>".$ball."</td>";
																echo "<td class='text-center'>".$count."</td>";
																echo "</tr>";
														else:
															echo "<tr class='table-warning'><td colspan = '2'>No Warms</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Warm Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->warms_pos as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions);
															$position = rtrim($position,'w');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Cold Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Next Colds</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE;
														foreach($lottery->colds as $ball => $count):	
														if($ball) :
															echo "<tr class='table-primary'>";
															$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
															$ball = rtrim($ball,'*');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
														else:
															echo "<tr class='table-primary'><td colspan = '2'>No Colds</td></tr>";
														endif;
														if($sym=='*'&&!$lottery->extra_included&&$ball==$extra_ball):
															$noEX = TRUE;
														endif;
														if(!$noEX):
															$cntr++;
														endif;
													endforeach; ?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Cold Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php foreach($lottery->colds_pos as $position => $count):	
															$exists = FALSE;
															echo "<tr class='table-light'>";
															$exists = array_key_exists($position, $lottery->positions);
															$position = rtrim($position,'c');  // Remove the special '*' symbol
															echo "<td class='text-center'>".$position."</td>";
															echo "<td class='text-center'>".$count."</td>";
															echo "</tr>";
													endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Extra Ball Table (standalone if present) -->
									<?php if(isset($lottery->dupextra)) : ?>
									<div class="extra-table-wrapper">
										<table class="table">
											<thead>
												<tr>
													<th class="text-center" colspan="2">Next Extra Ball</th>
												</tr>
												<tr>
													<th class="text-center">Ball</th>
													<th class="text-center">Occurrences</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($lottery->dupextra as $ball => $count):	
														echo "<tr class='table-success'>";
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
														echo "</tr>";
												endforeach; ?>
											</tbody>
										</table>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>
						
						<!-- Next Draw Tab Content -->
						<div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="table-container">
									<!-- Hot Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Next Hots</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														$cntr = 0;
														$noEX = FALSE; 
														if(isset($lottery->hots)) {
															foreach($lottery->hots as $ball => $count):	
															if($ball) :
																echo "<tr class='table-danger'>";
																$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
																$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																if($sym=='*'&&$ball!=$extra_ball) :
																	echo "<td class='text-center bg-danger text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																elseif($sym=='*'&&isset($lottery->extra_included)&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$ball."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															else:
																echo "<tr class='table-danger'><td colspan = '2'>No Hots</td></tr>";
															endif;
															if($sym=='*'&&!isset($lottery->extra_included)&&$ball==$extra_ball):
																$noEX = TRUE;
															endif;
															if(!$noEX):
																$cntr++;
															endif;
														endforeach;
														} else {
															echo "<tr class='table-danger'><td colspan='2'>No Next Draw Hots Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Hot Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->hots_pos)) {
															foreach($lottery->hots_pos as $position => $count):	
																$exists = FALSE;
																echo "<tr class='table-light'>";
																if(isset($lottery->positions)) {
																	$exists = array_key_exists($position, $lottery->positions);
																}
																$position = rtrim($position,'h');  // Remove the special 'h' symbol
																if($exists) :
																	echo "<td class='text-center bg-danger text-white'>".$position."</td>";
																	echo "<td class='text-center bg-danger text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-light'><td colspan='2'>No Hot Positions Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Warm Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Next Warms</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->warms)) {
															foreach($lottery->warms as $ball => $count):	
																echo "<tr class='table-warning'>";
																$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
																$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																if($sym=='*'&&$ball!=$extra_ball) :
																	echo "<td class='text-center bg-warning text-dark'>".$ball."</td>";
																	echo "<td class='text-center bg-warning text-dark'>".$count."</td>";
																elseif($sym=='*'&&isset($lottery->extra_included)&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$ball."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-warning'><td colspan='2'>No Next Draw Warms Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Warm Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->warms_pos)) {
															foreach($lottery->warms_pos as $position => $count):	
																$exists = FALSE;
																echo "<tr class='table-light'>";
																if(isset($lottery->positions)) {
																	$exists = array_key_exists($position, $lottery->positions);
																}
																$position = rtrim($position,'w');  // Remove the special 'w' symbol
																if($exists) :
																	echo "<td class='text-center bg-warning text-dark'>".$position."</td>";
																	echo "<td class='text-center bg-warning text-dark'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-light'><td colspan='2'>No Warm Positions Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Cold Tables Pair -->
									<div class="table-pair">
										<div class="table-wrapper">
											<table class="table">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Next Colds</th>
													</tr>
													<tr>
														<th class="text-center">Ball</th>
														<th class="text-center">Occurrences</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->colds)) {
															foreach($lottery->colds as $ball => $count):	
																echo "<tr class='table-info'>";
																$sym = substr($ball, -1);  // extract only the asterisk, '*'symbol
																$ball = rtrim($ball,'*');  // Remove the special '*' symbol
																if($sym=='*'&&$ball!=$extra_ball) :
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																elseif($sym=='*'&&isset($lottery->extra_included)&&$lottery->extra_included&&$ball==$extra_ball&&!$lottery->duplicate_extra_ball):
																	echo "<td class='text-center bg-info text-white'>".$ball."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$ball."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-info'><td colspan='2'>No Next Draw Colds Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
										<div class="table-wrapper">
											<table class="table pos">
												<thead>
													<tr>
														<th class="text-center" colspan="2">Cold Positions</th>
													</tr>
													<tr>
														<th class="text-center">Position</th>
														<th class="text-center">Count</th>
													</tr>
												</thead>
												<tbody>
													<?php 
														if(isset($lottery->colds_pos)) {
															foreach($lottery->colds_pos as $position => $count):	
																$exists = FALSE;
																echo "<tr class='table-light'>";
																if(isset($lottery->positions)) {
																	$exists = array_key_exists($position, $lottery->positions);
																}
																$position = rtrim($position,'c');  // Remove the special 'c' symbol
																if($exists) :
																	echo "<td class='text-center bg-info text-white'>".$position."</td>";
																	echo "<td class='text-center bg-info text-white'>".$count."</td>";
																else:
																	echo "<td class='text-center'>".$position."</td>";
																	echo "<td class='text-center'>".$count."</td>";
																endif;
																echo "</tr>";
															endforeach;
														} else {
															echo "<tr class='table-light'><td colspan='2'>No Cold Positions Available</td></tr>";
														}
														?>
												</tbody>
											</table>
										</div>
									</div>
									
									<!-- Extra Ball Table (standalone if present) -->
									<?php if(isset($lottery->dupextra)) : ?>
									<div class="extra-table-wrapper">
										<table class="table">
											<thead>
												<tr>
													<th class="text-center" colspan="2">Next Extra Ball</th>
												</tr>
												<tr>
													<th class="text-center">Ball</th>
													<th class="text-center">Occurrences</th>
												</tr>
											</thead>
											<tbody>
												<?php foreach($lottery->dupextra as $ball => $count):	
														echo "<tr class='table-success'>";
															echo "<td class='text-center'>".$ball."</td>";
															echo "<td class='text-center'>".$count."</td>";
														echo "</tr>";
												endforeach; ?>
											</tbody>
										</table>
									</div>
									<?php endif; ?>
								</div>
							</div>
						</div>

						<!-- H-W-C Winners Tab Content -->
						<div class="tab-pane fade" id="winners" role="tabpanel" aria-labelledby="winners-tab">
							<div class="container-fluid" style="margin: 25px;">
								<div class="row">
									<div class="col-12">
										<div class="card">
											<div class="card-header">
												<h5 class="card-title">H-W-C Winners Analysis</h5>
												<p class="card-text">H-W-C patterns sorted by total points based on follower win system</p>
											</div>
											<div class="card-body">
												<?php if(!isset($hwc_winners) || empty($hwc_winners)): ?>
													<div class="alert alert-warning" role="alert">
														<strong>No H-W-C winner data available.</strong> 
														Please recalculate H-W-C statistics from the Statistics page to generate winner analysis.
													</div>
												<?php else: ?>
													<div class="table-responsive">
														<table class="table table-hover table-striped" id="hwc-winners-table">
															<thead class="thead-dark">
																<tr>
																	<th class="text-center col-rank sortable-header" onclick="toggleSort()">
																		Rank
																		<span class="sort-arrow desc" id="sort-arrow">▲</span>
																	</th>
																	<th class="text-center col-hot">Hot</th>
																	<th class="text-center col-separator">-</th>
																	<th class="text-center col-warm">Warm</th>
																	<th class="text-center col-separator">-</th>
																	<th class="text-center col-cold">Cold</th>
																	<?php if(isset($hwc_winners[0]['enabled_categories'])): ?>
																		<?php foreach($hwc_winners[0]['enabled_categories'] as $category): ?>
																			<?php if($category == 'extra'): ?>
																				<th class="text-center col-prize">Extra</th>
																			<?php elseif($category == '1_win'): ?>
																				<th class="text-center col-prize">1</th>
																			<?php elseif($category == '1_win_extra'): ?>
																				<th class="text-center col-prize">1 + Extra</th>
																			<?php elseif($category == '2_win'): ?>
																				<th class="text-center col-prize">2</th>
																			<?php elseif($category == '2_win_extra'): ?>
																				<th class="text-center col-prize">2 + Extra</th>
																			<?php elseif($category == '3_win'): ?>
																				<th class="text-center col-prize">3</th>
																			<?php elseif($category == '3_win_extra'): ?>
																				<th class="text-center col-prize">3 + Extra</th>
																			<?php elseif($category == '4_win'): ?>
																				<th class="text-center col-prize">4</th>
																			<?php elseif($category == '4_win_extra'): ?>
																				<th class="text-center col-prize">4 + Extra</th>
																			<?php elseif($category == '5_win'): ?>
																				<th class="text-center col-prize">5</th>
																			<?php elseif($category == '5_win_extra'): ?>
																				<th class="text-center col-prize">5 + Extra</th>
																			<?php elseif($category == '6_win'): ?>
																				<th class="text-center col-prize">6</th>
																			<?php elseif($category == '6_win_extra'): ?>
																				<th class="text-center col-prize">6 + Extra</th>
																			<?php elseif($category == '7_win'): ?>
																				<th class="text-center col-prize">7</th>
																			<?php elseif($category == '7_win_extra'): ?>
																				<th class="text-center col-prize">7 + Extra</th>
																			<?php elseif($category == '8_win'): ?>
																				<th class="text-center col-prize">8</th>
																			<?php elseif($category == '8_win_extra'): ?>
																				<th class="text-center col-prize">8 + Extra</th>
																			<?php elseif($category == '9_win'): ?>
																				<th class="text-center col-prize">9</th>
																			<?php elseif($category == '9_win_extra'): ?>
																				<th class="text-center col-prize">9 + Extra</th>
																			<?php endif; ?>
																		<?php endforeach; ?>
																	<?php endif; ?>
																	<th class="text-center col-points">Points</th>
																</tr>
															</thead>
															<tbody>
																<?php 
																$rank = 1;
																foreach($hwc_winners as $winner): 
																	$hwc_parts = explode('-', $winner['hwc_pattern']);
																	$hot_count = isset($hwc_parts[0]) ? $hwc_parts[0] : 0;
																	$warm_count = isset($hwc_parts[1]) ? $hwc_parts[1] : 0;
																	$cold_count = isset($hwc_parts[2]) ? $hwc_parts[2] : 0;
																?>
																	<tr class="hwc-row" data-hwc="<?= $winner['hwc_pattern'] ?>" data-points="<?= $winner['total_points'] ?>" data-rank="<?= $rank ?>">
																		<td class="text-center font-weight-bold col-rank"><?= $rank ?></td>
																		<td class="text-center text-danger font-weight-bold col-hot"><?= $hot_count ?></td>
																		<td class="text-center col-separator">-</td>
																		<td class="text-center text-warning font-weight-bold col-warm"><?= $warm_count ?></td>
																		<td class="text-center col-separator">-</td>
																		<td class="text-center text-info font-weight-bold col-cold"><?= $cold_count ?></td>
																		<?php foreach($winner['enabled_categories'] as $category): ?>
																			<td class="text-center col-prize">
																				<?= isset($winner['win_breakdown'][$category]) ? $winner['win_breakdown'][$category] : 0 ?>
																			</td>
																		<?php endforeach; ?>
																		<td class="text-center font-weight-bold text-success col-points"><?= $winner['total_points'] ?></td>
																	</tr>
																<?php 
																	$rank++; 
																endforeach; 
																?>
															</tbody>
														</table>
													</div>
												<?php endif; ?>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
						
					</div>
				</div>
			</div>
		</div>
	</section>

	<script>
		let isDescending = true; // Start with descending order (highest points first)
		
		function toggleSort() {
			const table = document.getElementById('hwc-winners-table');
			const tbody = table.getElementsByTagName('tbody')[0];
			const rows = Array.from(tbody.getElementsByTagName('tr'));
			const arrow = document.getElementById('sort-arrow');
			
			// Toggle sort direction
			isDescending = !isDescending;
			
			// Update arrow appearance and direction
			if (isDescending) {
				arrow.textContent = '▲';
				arrow.className = 'sort-arrow desc';
			} else {
				arrow.textContent = '▼';
				arrow.className = 'sort-arrow asc';
			}
			
			// Sort rows based on points (since ranks correspond to points)
			rows.sort((a, b) => {
				const pointsA = parseInt(a.getAttribute('data-points'));
				const pointsB = parseInt(b.getAttribute('data-points'));
				
				if (isDescending) {
					return pointsB - pointsA; // Descending order (highest points first)
				} else {
					return pointsA - pointsB; // Ascending order (lowest points first)
				}
			});
			
			// Clear tbody first
			tbody.innerHTML = '';
			
			// Re-append rows in new order and update rank numbers based on sort direction
			rows.forEach((row, index) => {
				let newRank;
				
				if (isDescending) {
					// Descending: Rank 1 = highest points (best)
					newRank = index + 1;
				} else {
					// Ascending: Rank 1 = lowest points (worst), so reverse the ranking
					newRank = rows.length - index;
				}
				
				const rankCell = row.querySelector('.col-rank');
				rankCell.textContent = newRank;
				row.setAttribute('data-rank', newRank);
				tbody.appendChild(row);
			});
		}
		
		// Enhanced row highlighting with smooth transitions
		document.addEventListener('DOMContentLoaded', function() {
			const tableRows = document.querySelectorAll('#hwc-winners-table .hwc-row');
			
			tableRows.forEach(row => {
				row.addEventListener('mouseenter', function() {
					// Add enhanced highlighting class
					this.style.backgroundColor = '#e3f2fd';
					this.style.borderLeft = '4px solid #2196f3';
					this.style.boxShadow = '0 2px 8px rgba(33, 150, 243, 0.3)';
					this.style.transform = 'scale(1.01)';
					
					// Highlight the H-W-C pattern
					const hwcPattern = this.getAttribute('data-hwc');
					const points = this.getAttribute('data-points');
					
					// Optional: Show tooltip or additional info
					this.title = `H-W-C Pattern: ${hwcPattern} | Total Points: ${points}`;
				});
				
				row.addEventListener('mouseleave', function() {
					// Remove highlighting
					this.style.backgroundColor = '';
					this.style.borderLeft = '';
					this.style.boxShadow = '';
					this.style.transform = '';
					this.title = '';
				});
			});
		});
	</script>	