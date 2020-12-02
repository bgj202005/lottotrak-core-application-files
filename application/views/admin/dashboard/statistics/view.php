<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
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
		font-size: 0.80em;
	}
	table.stats tr{
		font-size: 0.54em;
	}
	td.fontincrease { 
		font-size: 	1.70em;
		text-align: center; 
	}
	#draws_paginate{
    float:right;
	}
	
</style>
	<h2><?php echo 'View Statistics for: '.$lottery->lottery_name; ?></h2>	
	<section>
		<table id="history"
		class = "stats"
		data-pagination="true"
		data-search="false">
			<thead>
				<tr>
					<th data-field="sum_10">S (last 10)</th>
					<th data-field="sum_100">S (last 100)</th>
					<th data-field="sum_200">S (last 200)</th>
					<th data-field="sum_300">S (last 300)</th>
					<th data-field="sum_400">S (last 400)</th>
					<th data-field="sum_500">S (last 500)</th>
					<th data-field="digits_10">S Digits (10)</th>
					<th data-field="digits_100">S Digits(100)</th>
					<th data-field="even_10">Ev (10)</th>
					<th data-field="even_100">Ev (100)</th>
					<th data-field="odd_10">Od (10)</th>
					<th data-field="odd_100">Od (100)</th>
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
					<td class="fontincrease"><?=$statistics->even_10; ?></td>
					<td class="fontincrease"><?=$statistics->even_100; ?></td>
					<td class="fontincrease"><?=$statistics->odd_10; ?></td>
					<td class="fontincrease"><?=$statistics->odd_100; ?></td>
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
		S = Sum			Ev = Evens			Od = Odds		M = Maximum		
	</section>
	<section>
		<div class="table-responsive">
		<table class="table table-striped"
			id="draws"
			data-pagination="true"
			data-search="true"
			data-show-toggle="true"
			data-use-row-attr-func="true"
			data-defer-url="json/data1.json"
			data-filter-control="true"
			data-show-search-clear-button="true"
			data-order='[[ 1, "desc" ]]'>
			<thead>
				<tr>
				<th data-field="draw" data-sortable="true">Draw #</th>
				<th data-field="date" data-sortable="true">Draw Date</th>
				<th data-field="ball1">Ball 1</th>
				<th data-field="ball2">Ball 2</th>
				<th data-field="ball3">Ball 3</th>
				<?php if (intval($lottery->balls_drawn)>=4): ?><th class="no-sort">Ball 4</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=5): ?><th class="no-sort">Ball 5</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=6): ?><th class="no-sort">Ball 6</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=7): ?><th class="no-sort">Ball 7</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)>=8): ?><th class="no-sort">Ball 8</th><?php endif; ?>
				<?php if (intval($lottery->balls_drawn)==9): ?><th class="no-sort">Ball 9</th><?php endif; ?>
				<?php if (intval($lottery->extra_ball)==1): ?><th class="no-sort">Extra Ball</th><?php endif; ?>
				<th data-field="sum_draw" data-sortable="true" data-filter-control="select">Sum</th>
				<th data-field="sum_digits" data-sortable="true" data-filter-control="select">Digits Sum</th>
				<th data-field="even" data-sortable="true" data-filter-control="select">Even</th>
				<th data-field="odd" data-sortable="true" data-filter-control="select">Odd</th>
				<th data-field="range_draw" data-sortable="true" data-filter-control="select">Range</th>
				<th data-field="repeat_decade" data-sortable="true" data-filter-control="select">Max Decade</th>
				<th data-field="repeat_last" data-sortable="true" data-filter-control="select">Max Last</th>
				</tr>
			</thead>
			<tbody>
				<?php foreach ($draws as $draw): ?>
				<tr>
					<td><?= $draw->draw ?></td>
					<td><?php echo date("D, M d, Y", strtotime(str_replace('/','-',$draw->draw_date))); ?></td>
					<td><?= $draw->ball1; ?></td>
					<td><?= $draw->ball2; ?></td>
					<td><?= $draw->ball3; ?></td>
					<?php if (intval($lottery->balls_drawn)>=4): ?><td><?= $draw->ball4; ?></td><?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=5): ?><td><?= $draw->ball5; ?></td><?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=6): ?><td><?= $draw->ball6; ?></td><?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=7): ?><td><?= $draw->ball7; ?></td><?php endif; ?>
					<?php if (intval($lottery->balls_drawn)>=8): ?><td><?= $draw->ball8; ?></td><?php endif; ?>
					<?php if (intval($lottery->balls_drawn)==9): ?><td><?= $draw->ball9; ?></td><?php endif; ?>			
					<?php if (intval($lottery->extra_ball)==1): ?><td><?= $draw->extra; ?><?php endif; ?></td>
					<td><?= $draw->sum_draw; ?></td>
					<td><?= $draw->sum_digits; ?></td>
					<td><?= $draw->even; ?></td>
					<td><?= $draw->odd; ?></td>
					<td><?= $draw->range_draw; ?></td>
					<td><?= $draw->repeat_decade; ?></td>
					<td><?= $draw->repeat_last; ?></td>
				</tr>
				<?php  endforeach; ?>
			</tbody>	
		</table>
		</div>
	<script>
	$(function() {
		$('#draws').bootstrapTable()
	})
	$(function() {
		$('#history').bootstrapTable()
	})
	</script>
	</section>
