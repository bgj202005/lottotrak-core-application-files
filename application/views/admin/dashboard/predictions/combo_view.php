<script src="//cdnjs.cloudflare.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap4.min.js"></script>
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
			<table id="draws" class="table table-striped table-bordered display" style="width:100%" data-order='[[ 1, "asc" ]]'>
				<thead>
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
				</thead>
				<tbody>
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
$(document).ready(function() {
    $('#draws').DataTable(
         {     
      "aLengthMenu": [[5, 10, 25, 100, 250, 500, 1000, -1], [5, 10, 25, 100, 250, 500, 1000, "All"]],
        "iDisplayLength": 10,
		"columnDefs": [ {
          "targets": 'no-sort',
          "orderable": true,
    } ]
       } 
        );
	}
);
</script>