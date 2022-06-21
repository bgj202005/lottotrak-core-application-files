<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">
<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.css" rel="stylesheet">

<script src="https://cdnjs.cloudflare.com/ajax/libs/TableDnD/1.0.3/jquery.tablednd.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/filter-control/bootstrap-table-filter-control.min.js"></script>
<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/extensions/reorder-rows/bootstrap-table-reorder-rows.min.js"></script>
<style>
	table{
    	width:100%;
	}
	tr{
		font-size: 0.90em;
	}
	th {
		text-align:center;
		font-size: 0.90em;
	}
	table.stats tr{
		font-size: 0.65em;
	}
	
	td.fontincrease { 
		font-size: 	1.21em;
		text-align:center; 
		white-space:nowrap;
	}
	th.datafont{
		text-align:center;
		font-size: 0.71em;
	}
	td.datafont { 
		font-size: 	0.95em;
		text-align: center; 
		white-space: nowrap;
	}

	.repeater {
		margin-left: 10px;
	}
	#draws_paginate{
    float:right;
	}
	
</style>
	<h2><?php echo 'View Statistics for: '.$lottery->lottery_name; ?></h2>	
	<h5 style = "text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>
	<section>
		<table id="history"
		class = "stats table-sm"
		data-pagination="false"
		data-search="false">
			<thead>
				<tr><th colspan = "18">AVERAGE STATISTICS</th></tr>
				<tr>
					<th data-field="sum_10">S (last 10)</th>
					<th data-field="sum_100">S (last 100)</th>
					<th data-field="sum_200">S (last 200)</th>
					<th data-field="sum_300">S (last 300)</th>
					<th data-field="sum_400">S (last 400)</th>
					<th data-field="sum_500">S (last 500)</th>
					<th data-field="digits_10">S Digits (10)</th>
					<th data-field="digits_100">S Digits(100)</th>
					<th data-field="odd_10">Od (10)</th>
					<th data-field="even_10">Ev (10)</th>
					<th data-field="odd_100">Od (100)</th>
					<th data-field="even_100">Ev (100)</th>
					<th data-field="range_10">Range (10)</th>
					<th data-field="range_100">Range (100)</th>
					<th data-field="repeat_decade_10">M Decade (10)</th>
					<th data-field="repeat_decade_100">M Decade (100)</th>
					<th data-field="repeat_last_10">M Last (10)</th>
					<th data-field="repeat_last_100">M Last (100)</th>
				</tr>
			</thead>
			<tbody>
				<tr>
					<td class="fontincrease"><?=$statistics->sum_10; ?></td>
					<td class="fontincrease" ><?=$statistics->sum_100; ?></td>
					<td class="fontincrease" ><?=$statistics->sum_200; ?></td>
					<td class="fontincrease" ><?=$statistics->sum_300; ?></td>
					<td class="fontincrease" ><?=$statistics->sum_400; ?></td>
					<td class="fontincrease" ><?=$statistics->sum_500; ?></td>
					<td class="fontincrease"><?=$statistics->digits_10; ?></td>
					<td class="fontincrease"><?=$statistics->digits_100; ?></td>
					<td class="fontincrease"><?=$statistics->odd_10; ?></td>
					<td class="fontincrease"><?=$statistics->even_10; ?></td>
					<td class="fontincrease"><?=$statistics->odd_100; ?></td>
					<td class="fontincrease"><?=$statistics->even_100; ?></td>
					<td class="fontincrease"><?=$statistics->range_10; ?></td>
					<td class="fontincrease"><?=$statistics->range_100; ?></td>
					<td class="fontincrease"><?=$statistics->repeat_decade_10; ?></td>
					<td class="fontincrease"><?=$statistics->repeat_decade_100; ?></td>
					<td class="fontincrease"><?=$statistics->repeat_last_10; ?></td>
					<td class="fontincrease"><?=$statistics->repeat_last_100; ?></td>
				</tr>
			</tbody>	
		</table>
	</section>
	<section>
		S = Sum			Ev = Evens			Od = Odds		M = Maximum<br />		
	</section>
	<section>
		<table>
		<tr>
			<td>		
		<?php $extra = array('class' => 'checkbox', 'id' => 'trends');
			echo form_checkbox($trend, 1, ($trend ? TRUE : FALSE), $extra); 
			$extra = array('class' => 'col-2 col-form-label col-form-label-md', 'style' => 'font-weight:bold; white-space: nowrap; text-left');
			echo form_label('Trends of Draws (Extra Draws Removed)', 'trends_lb', $extra); ?></td>	
		<td><div class="dropdown text-right" style = "margin-left: 50px; margin-bottom: 10px;">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Draw Range
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
			<?php if(!$interval) : ?>
				<a class="dropdown-item active" href="<?=base_url('admin/statistics/view_draws/'.$lottery->id.'/'.$trend.'/'.$all)?>">All Draws (<?=$range;?>) </a>
			<?php else:
				for($i = 1; $i <= $interval; $i++):
					$step = $i * 100;	// in multiples of 100
					if($i!=$interval): ?>
						<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/statistics/view_draws/'.$lottery->id.'/'.$trend.'/'.$step);?>">Last <?=$step;?></a>
					<?php else : ?>
						<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/statistics/view_draws/'.$lottery->id.'/'.$trend.'/'.$all);?>">All Draws (<?=$all;?>)</a>
					<?php endif;
				endfor; ?> 
				<?php endif;?>
			</div>
		</div>
			</td>
		</tr>
		</table>
		<div class = 'table-responsive'>
		<table id="evenodds"
		class="table table-striped table-sm"
		data-pagination="false"
		data-search="false"
		data-order='[[ 1, "asc" ]]'>
			<thead>
				<tr><th colspan = "10">ODD / EVEN HISTORY</th></tr>
				<tr>
					<th data-field="odds" data-halign="center" data-align="center">ODD</th>
					<th data-field="evens" data-halign="center" data-align="center">EVEN</th>
					<th data-field="All" data-halign="center" data-align="center" data-sortable="true">ALL <?=$evensodds[0]->total;?> DRAWS</th>
					<th data-field="All%" data-halign="center" data-align="center">ALL %</th>
					<th data-field="Last_10" data-halign="center" data-align="center" data-sortable="true">LAST 10</th>
					<th data-field="10%" data-halign="center" data-align="center">LAST 10 %</th>
					<th data-field="Last_100" data-halign="center" data-align="center" data-sortable="true">LAST 100</th>
					<th data-field="100%" data-halign="center" data-align="center">LAST 100 %</th>
					<th data-field="Last_200" data-halign="center" data-align="center" data-sortable="true">LAST 200</th>
					<th data-field="200%" data-halign="center" data-align="center">LAST 200 %</th>
					
				</tr>
			</thead>
			<tbody>
				<?php foreach($evensodds as $parity): ?>
				<tr>
					<td class="fontincrease"><?=$parity->odd;?></td>
					<td class="fontincrease"><?=$parity->even; ?></td>
					<td class="fontincrease"><?=$parity->count;?></td>
					<td class="fontincrease"><?=intval(($parity->count/$parity->total)*100).'%';?></td>
					<td class="fontincrease"><?=$parity->count_10;?></td>
					<td class="fontincrease"><?=intval(($parity->count_10/10)*100).'%';?></td>
					<td class="fontincrease"><?=(!empty($parity->count_100) ? $parity->count_100 : '-');?></td>
					<td class="fontincrease"><?=(!empty($parity->count_100) ? intval(($parity->count_100/100)*100).'%' : '-');?></td>
					<td class="fontincrease"><?=(!empty($parity->count_200) ? $parity->count_200 : '-');?></td>
					<td class="fontincrease"><?=(!empty($parity->count_200) ? intval(($parity->count_200/200)*100).'%' : '-');?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>	
		</table>
		</div>
	</section>
	<section>
		<div class="table-responsive ">
		<table class="table table-striped table-sm w-auto"
			id="draws"
			data-pagination="true"
			data-search="true"
			data-show-toggle="true"
			data-use-row-attr-func="true"
			data-filter-control="true"
			data-show-search-clear-button="true"
			data-order='[[ 1, "desc" ]]'>
			<thead>
				<tr>
				<th class = "datafont" data-field="draw" data-sortable="true">Draw #</th>
				<th class = "datafont" data-field="date">Draw Date</th>
				<th class = "datafont" data-field="ball1">Ball 1</th>
				<th class = "datafont" data-field="ball2">Ball 2</th>
				<th class = "datafont" data-field="ball3">Ball 3</th>
				<?php if (intval($lottery->balls_drawn)>=4): ?><th class="no-sort datafont">Ball 4</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=5): ?><th class="no-sort datafont">Ball 5</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=6): ?><th class="no-sort datafont">Ball 6</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=7): ?><th class="no-sort datafont">Ball 7</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=8): ?><th class="no-sort datafont">Ball 8</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)==9): ?><th class="no-sort datafont">Ball 9</th><?php endif; ?>
				<?php if (intval($lottery->extra_ball)==1): ?><th class="no-sort datafont">Extra Ball</th><?php endif; ?>
				<th class = "datafont" data-field="sum_draw" data-sortable="true" data-filter-control="select">Sum</th>
				<th class = "datafont" data-field="sum_digits" data-sortable="true" data-filter-control="select">Digits Sum</th>
				<th class = "datafont" data-field="odd" data-sortable="true" data-filter-control="select">Odd</th>
				<th class = "datafont" data-field="even" data-sortable="true" data-filter-control="select">Even</th>
				<th class = "datafont" data-field="range_draw" data-sortable="true" data-filter-control="select">Range</th>
				<th class = "datafont" data-field="repeat_decade" data-sortable="true" data-filter-control="select">Max Decade</th>
				<th class = "datafont" data-field="repeat_last" data-sortable="true" data-filter-control="select">Max Last</th>
				</tr>
			</thead>
			<tbody>
				<?php reset($draws);
				$curr = new \stdClass;
				foreach ($draws as $draw):
					$prev = next($draws);
					if (false!==$prev): 
					$curr = $draw;	// The current draw object begins with $draw object
					endif;?>
				<tr>
					<td class="datafont"><?=$draw->draw;?></td>
					<?php if(false!==$prev): 
						$r = $stat_method->last_repeaters($prev, $curr, $lottery->balls_drawn); 
					endif; ?>
					<td style = "white-space: nowrap;"><?php echo date("D, M d, Y", strtotime(str_replace('/','-',$draw->draw_date))); ?></td>
					<td class="datafont"><?=$draw->ball1; 
					if(isset($r->ball1)&&($prev!==false)&&($draw->extra)):
						echo $stat_method->icon_repeater(); 
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball1, $curr->ball1); endif; 
					endif;?>
					</td>
					<td class="datafont"><?=$draw->ball2; 
					if(isset($r->ball2)&&($prev!==false)&&($draw->extra)):
						echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball2, $curr->ball2); endif; 
					endif;?>
					</td>
					<td class="datafont"><?=$draw->ball3; 
					if(isset($r->ball3)&&($prev!==false)&&($draw->extra)):
						echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball3, $curr->ball3); endif; 
					endif;?>
					</td>
					<?php if (intval($lottery->balls_drawn)>=4): ?><td class="datafont"><?=$draw->ball4; 
					if(isset($r->ball4)&&($prev!==false)&&($draw->extra)):
						echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball4, $curr->ball4); endif; 
					endif;?>
					</td>
					<?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=5): ?><td class="datafont"><?=$draw->ball5;
					if(isset($r->ball5)&&($prev!==false)&&($draw->extra)):
						echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball5, $curr->ball5); endif; 
					endif;?>
					</td>
					<?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=6): ?><td class="datafont"><?=$draw->ball6; 
					if(isset($r->ball6)&&($prev!==false)&&($draw->extra)): 
						echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball6, $curr->ball6); endif;
					endif; ?>
					</td><?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=7): ?><td class="datafont"><?=$draw->ball7;
					if(isset($r->ball7)&&($prev!==false)): echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball7, $curr->ball7); endif;
					endif; ?> 
					</td>
					<?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=8): ?><td class="datafont"><?=$draw->ball8;
					if(isset($r->ball8)&&($prev!==false)): echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball8, $curr->ball8); endif;
					endif; ?> 
					</td>
					<?php endif; ?>
					<?php if (intval($lottery->balls_drawn)==9): ?><td class="datafont"><?=$draw->ball9;
					if(isset($r->ball9)&&($prev!==false)): echo $stat_method->icon_repeater();
					else: if($trend&&($prev!==false)): echo $stat_method->trend($prev->ball9, $curr->ball9); endif;
					endif; ?> 
					</td>
					<?php endif; ?>			
					<?php if (intval($lottery->extra_ball)==1): ?><td class="datafont"><?=$draw->extra; 
					if($trend&&($prev!==false)): echo $stat_method->trend($prev->extra, $curr->extra); endif;
					endif; ?></td>
					<td class="datafont"><?=$draw->sum_draw; ?></td>
					<td class="datafont"><?=$draw->sum_digits; ?></td>
					<td class="datafont"><?=$draw->odd; ?></td>
					<td class="datafont"><?=$draw->even; ?></td>
					<td class="datafont"><?=$draw->range_draw; ?></td>
					<td class="datafont"><?=$draw->repeat_decade; ?></td>
					<td class="datafont"><?=$draw->repeat_last; ?></td>
				</tr>
				<?php endforeach; ?>
			</tbody>	
		</table>
		</div>
	</section>
 	<script>
	 $(function() {
		$('#draws').bootstrapTable()
	}) 
	$(function() {
		$('#history').bootstrapTable()
	})
	var trends = document.getElementById("trends");
	document.getElementById("trends").onclick = function(){
		if(trends.checked==true) {
			location.href = "<?php echo base_url().'admin/statistics/view_draws/'.$lottery->id.'/1/';?>";
		}
		else {
			location.href = "<?php echo base_url().'admin/statistics/view_draws/'.$lottery->id.'/0/';?>";
		}
	}
	$(function() {
		$('#evenodds').bootstrapTable()
	})
 	</script>