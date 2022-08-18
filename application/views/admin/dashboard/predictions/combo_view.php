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
	#draws_filter{
    float:right;
	}
	#draws_paginate{
    float:right;
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
}	
</style>
	<section>
		<h2><?php echo 'View the Combination File: '.$file[0]->file_name;?>.txt</h2>
		<h3 style = "text-align:center;" id="view_message" class = "text-danger"><?= $message; ?></h3>
			<div class="table-responsive">
			<table id="draws" 
			class="table table-striped table-sm"
			style="width:100%"
			data-height="800" 
			data-pagination="true"
			data-page-size="20" 
			data-order='[[ 1, "asc" ]]'>
				<thead>
					<tr>
						<th data-sortable="true">Row #</th>
						<th data-sortable="true">Pick 1</th>
						<th data-sortable="true">Pick 2</th>
						<th data-sortable="true">Pick 3</th>
							<?php if (intval($lottery->balls_drawn)>=4): ?><th data-sortable="true">Pick 4</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=5): ?><th data-sortable="true">Pick 5</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=6): ?><th data-sortable="true">Pick 6</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=7): ?><th data-sortable="true">Pick 7</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=8): ?><th data-sortable="true">Pick 8</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)==9): ?><th data-sortable="true">Pick 9</th><?php endif; ?>
					</tr>
				</thead>
				<tbody>
				<tr></td><div class="dropdown text-right" style = "margin-left: 50px; margin-bottom: 10px;">
			<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
				Combination Range
			</button>
			<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
			<?php if(!$interval) : ?>
				<a class="dropdown-item active" href="<?=base_url('admin/statistics/view_draws/'.$lottery->id.'/'.$file[0]->file_name.'/'.$all)?>">All Combinations (<?=$range;?>) </a>
			<?php else:
				for($i = 1; $i <= $interval; $i++):
					$step = $i * 400;	// in multiples of 100
					if($i!=$interval): ?>
						<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/predictions/combo_view/'.$lottery->id.'/'.$file[0]->file_name.'/'.$step);?>">Last <?=$step;?></a>
					<?php else : ?>
						<a class="dropdown-item <?php if($i==$sel_range) echo 'active'; ?>" href="<?=base_url('admin/predictions/combo_view/'.$lottery->id.'/'.$file[0]->file_name.'/'.$all);?>">All Combinations (<?=$all;?>)</a>
					<?php endif;
				endfor; ?> 
				<?php endif;?>
			</div>
		</div></td></tr>	
				<?php $c = 1; // Start at Combination 1 
					foreach ($draws as $draw): ?>
				<tr>
					<td><?= $c;?></td>
						<td><?= $draw->ball1; ?></td>
						<td><?= $draw->ball2; ?></td>
						<td><?= $draw->ball3; ?></td>
						<?php if (intval($lottery->balls_drawn)>=4): ?><td><?= $draw->ball4; ?></td><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=5): ?><td><?= $draw->ball5; ?></td><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=6): ?><td><?= $draw->ball6; ?></td><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=7): ?><td><?= $draw->ball7; ?></td><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=8): ?><td><?= $draw->ball8; ?></td><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)==9): ?><td><?= $draw->ball9; ?></td><?php endif; ?>			
				</tr>
				<?php $c++; endforeach; ?>		
				</tbody>
				<tfoot>
					<tr>
						<th>Row #</th>
						<th class="no-sort">Pick 1</th>
						<th class="no-sort">Pick 2</th>
						<th class="no-sort">Pick 3</th>
						<?php if (intval($lottery->balls_drawn)>=4): ?><th class="no-sort">Pick 4</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=5): ?><th class="no-sort">Pick 5</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=6): ?><th class="no-sort">Pick 6</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=7): ?><th class="no-sort">Pick 7</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)>=8): ?><th class="no-sort">Pick 8</th><?php endif; ?>
						<?php if (intval($lottery->balls_drawn)==9): ?><th class="no-sort">Pick 9</th><?php endif; ?>
					</tr>	
				</tfoot>
			</table>
		</div>
	<div style = "text-align: center;">
	<?php $js = "location.href='".base_url()."admin/predictions/'";
	$attributes = array(
			'class' 	=> "btn btn-primary btn-lg btn-info", 
			'onClick' 	=> "$js", 
			'style' 	=> "margin-left:20px; padding:5px;",
			'role'		=> 'button'
		);
	echo form_button('combo_list', 'Back to the Predictions List', $attributes); ?>
	</div>
	</section>
<script>
$(function() {
		$('#draws').bootstrapTable()
	}) 
</script>