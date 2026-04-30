<style>
	table{
    	width:100%;
	}
	tr{
		font-size: 0.60em; /* Minimum size before horizontal toolbar appears under list */
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
}
/* Reset button styling - match prize history */
.reset-followers {
	padding: 4px 8px;
	font-size: 11px;
	background-color: #ffc107;
	border-color: #ffc107;
	color: #212529;
}
.reset-followers:hover {
	background-color: #e0a800;
	border-color: #d39e00;
	color: #212529;
}
.reset-followers:disabled {
	opacity: 0.6;
	cursor: not-allowed;
}
</style>	
<section>
	<h2>Lottery Profile Statistics</h2>
	<?php if (isset($message)) : ?> <h4 class="bg-warning" id = "message" style = "margin-top: 20px; text-align:center;"><?=$message; endif; ?></h4>
	<h4 id = "status" style = "margin-top: 20px; text-align:center; display:none;"></h4>	
	<h3 class="bg-success"  id = "counter" style = "margin-top:20px; text-align:center; display:none;">Current Draw has Statistics updated: <div id = "count"></div></h3>
	<div class = "status"></div>
	<div class="table-responsive">
	<div class ="table-responsive-xl">
	<table class="table table-striped">
		<thead>
			<tr> 
				<td style = "text-align:center; white-space: nowrap;">Logo</td>
				<td style = "text-align:center; white-space: nowrap;">Lottery</td>
				<td style = "text-align:center; white-space: nowrap;">State/Prov</td>
				<td style = "text-align:center; white-space: nowrap;">Country</td>
				<td style = "text-align:center; white-space: nowrap;">Date</td>
				<td style = "text-align:center; white-space: nowrap;">Drawn Numbers</td>
				<td style = "text-align:center; white-space: nowrap;">Sum(100)</td>
				<td style = "text-align:center; white-space: nowrap;">Sum(LD)</td>
				<td style = "text-align:center; white-space: nowrap;">Repeaters</td>
				<th>Stats</th>
				<th style = "text-align:center; white-space: nowrap;">H-W-C</th>
				<th>Followers</th>
				<th>Friends</th>
				<th>Calculate</th>
				<th style = "text-align:center; white-space: nowrap;">ReCalc?</th>
				<th style = "text-align:center; white-space: nowrap;">Reset</th>
			</tr>
		</thead>
		<tbody>
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	<tr>
		<td style = "text-align:center;"><?php if (!empty($lottery->lottery_image)) 
			{ 
				if (DIRECTORY_SEPARATOR === '/') 
				{
    			// unix, linux, mac
					$path = $this->input->server('DOCUMENT_ROOT').'/images/uploads/'.$lottery->lottery_image;
				}
				else
				{
				// windows	
					$path =	base_url().'images/uploads/'.$lottery->lottery_image;
				}
				$image_info = safe_getimagesize($path); 
				$extra = get_responsive_image_attrs($image_info, 100);
				echo img(base_url().'images/uploads/'.$lottery->lottery_image, FALSE, $extra); 
			} ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?php echo anchor('admin/statistics/view_draws/'.$lottery->id, $lottery->lottery_name);?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=$lottery->lottery_state_prov; ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=$lottery->lottery_country_id; ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=($lottery->last_date!='NA' ? date("D, M-d-Y",strtotime(str_replace('/','-',$lottery->last_date))) : 'N/A'); ?></td>
		<td style = "text-align:center; white-space: nowrap;"><?=($lottery->last_draw!='NA' ? $lottery->last_draw : 'N/A'); ?></td>
		<td style = "text-align:center;"><?=$lottery->average_sum; ?></td>
		<td style = "text-align:center;"><?=$lottery->sum_last; ?></td>
		<td style = "text-align:center;"><?=$lottery->repeaters; ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_stat('admin/statistics/view_draws/'.$lottery->id, $lottery->draw_count == 0); ?></td>
		<?php if(!$lottery->min_draws_met): ?>
		<td colspan="3" style="text-align:center; vertical-align:top; padding:8px;">
			<span style="color: #d9534f; font-weight: bold; font-size:1.25em;">
				<?=$lottery->draws_remaining;?> Draws left to reach the minimum of <?=$lottery->required_draws;?> draws for the next prediction. 
				The Earliest prediction draw date is <?=$lottery->next_prediction_date;?>.
			</span>
		</td>
		<?php elseif($lottery->needs_recalc): ?>
		<td colspan="3" style="text-align:center; vertical-align:top; padding:8px;">
			<span style="color: #d9534f; font-weight: bold; font-size:0.875em;">ReCalc Required</span>
		</td>
		<?php else: ?>
		<td style = "text-align:center;"><?php echo $statistics->btn_hwc('admin/statistics/h_w_c/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_followers('admin/statistics/followers/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_friends('admin/statistics/friends/'.$lottery->id); ?></td>
		<?php endif; ?>
		<td style = "text-align:center;"><?php echo $statistics->btn_calculate('admin/statistics/calculate/'.$lottery->id, $lottery->draw_count == 0); ?></td>
		<td style = "text-align:center;"><input type="checkbox" name="recalc" value="<?=$lottery->id;?>" class="recalc<?=$lottery->id;?>" id="recalc" <?=(!$lottery->min_draws_met ? 'disabled' : '');?> >
		<td style = "text-align:center;">
			<button type="button" class="btn btn-sm btn-warning reset-followers" 
					data-lottery-id="<?=$lottery->id;?>"
					data-lottery-name="<?=htmlspecialchars($lottery->lottery_name);?>"
					title="Reset follower statistics to start from scratch"
					<?=($lottery->last_draw!='NA' ? '' : 'disabled');?>>
				<i class="fa fa-undo fa-lg" aria-hidden="true"></i> reset
			</button>
		</td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="16" style = "text-align:center">No Lotteries are available.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
	</div>
</section>
<script>
$(document).ready(function(){
 var url;
  $('.calculate').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Updating Lottery. Please Wait.";
	setTimeout(fade_out, 1500);
	});
	<?php if (count($lotteries)): foreach($lotteries as $lottery): ?>
	$('.recalc<?=$lottery->id;?>').click(function(){
	url = "<?=base_url();?>admin/statistics/recalc/<?=$lottery->id;?>";
	$('#message').css('display', 'none'); 
	$('#status').css('display', 'block');
	document.getElementById("status").innerHTML = "Updating the <?=$lottery->lottery_name;?> H-W-C, Followers and Friends Statistics to <?=date("l, M d, Y",strtotime(str_replace('/','-',$lottery->last_date)))?>.";
	redirect(url);
	});
	<?php endforeach; ?>
	<?php endif; ?>
	$('.stats').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving the Culumlative Statistics and History. Please Wait.";
	setTimeout(fade_out, 10500);
	});
	$('.h-w-c').click(function(){
		$('#status').css('display', 'block');
		$('#message').css('display', 'none'); 
		document.getElementById("status").innerHTML = "Retrieving the Hot - Warm - Cold (H-W-C) Numbers and History. Please Wait.";
		setTimeout(fade_out, 10500);
	});
	$('.followers').click(function(){
		$('#status').css('display', 'block');
		$('#message').css('display', 'none'); 
		document.getElementById("status").innerHTML = "Retrieving the Followers and History for the next draw. Please Wait.";
		setTimeout(fade_out, 10500);
	});
	$('.friends').click(function(){
		$('#status').css('display', 'block');
		$('#message').css('display', 'none'); 
		document.getElementById("status").innerHTML = "Retrieving the Friends of numbers and History for the next draw. Please Wait.";
		setTimeout(fade_out, 10500);
	});
	
	// Handle reset followers button clicks
	$('.reset-followers').click(function(e) {
		e.preventDefault();
		var lotteryId = $(this).data('lottery-id');
		var lotteryName = $(this).data('lottery-name');
		
		if (confirm('Are you sure you want to reset the follower statistics for ' + lotteryName + '?\n\nThis will clear all cached follower data and force the next ReCalc to start from scratch.')) {
			resetFollowerStatistics(lotteryId, lotteryName);
		}
	});
	
	function fade_out() {
      $("#status").fadeOut();
    }
});

function redirect(url) {
   window.location.href = url; 
}

function resetFollowerStatistics(lotteryId, lotteryName) {
	// Show status message
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Resetting follower statistics for " + lotteryName + ". Please wait...";
	
	$.ajax({
		url: '<?php echo site_url("admin/statistics/reset_followers"); ?>',
		type: 'POST',
		data: {
			lottery_id: lotteryId
		},
		dataType: 'json',
		success: function(response) {
			$('#status').css('display', 'none');
			if (response.success) {
				$('#message').removeClass('bg-warning').addClass('bg-success');
				$('#message').html(response.message);
				$('#message').css('display', 'block');
				
				// Update UI to show ReCalc Required - replace the 3 icon cells with colspan cell
				var row = $('button[data-lottery-id="' + lotteryId + '"]').closest('tr');
				var allCells = row.find('td');
				
// Find the H-W-C, Followers, Friends cells
			// Columns: 0=Logo, 1=Lottery, 2=State, 3=Country, 4=Date, 5=Drawn, 6=Sum100, 7=SumLD, 8=Repeaters, 9=Stats, 10=HWC, 11=Followers, 12=Friends
			var hwcCell = allCells.eq(10);      // H-W-C column
			var followersCell = allCells.eq(11); // Followers column
			var friendsCell = allCells.eq(12);   // Friends column
				
			// Check if already showing ReCalc Required message (colspan will be undefined or not '3')
			if (!hwcCell.attr('colspan') || hwcCell.attr('colspan') != '3') {
				// Remove Followers and Friends cells immediately
				if (followersCell.length > 0) followersCell.remove();
				if (friendsCell.length > 0) friendsCell.remove();
				// Update H-W-C cell to span 3 columns with ReCalc Required message
				hwcCell.attr('colspan', '3');
				hwcCell.css({'text-align': 'center', 'vertical-align': 'top', 'padding': '8px'});
				hwcCell.html('<span style="color: #d9534f; font-weight: bold; font-size:0.875em;">ReCalc Required</span>');
			}
			
			setTimeout(function() {
				$('#message').fadeOut();
			}, 5000);
		} else {
			$('#message').removeClass('bg-success').addClass('bg-warning');
			$('#message').html('Error: ' + response.message);
			$('#message').css('display', 'block');
		}
		},
		error: function(xhr, status, error) {
			$('#status').css('display', 'none');
			$('#message').removeClass('bg-success').addClass('bg-warning');
			$('#message').html('Error resetting follower statistics. Server responded with: ' + xhr.status + ' ' + xhr.statusText);
			$('#message').css('display', 'block');
		}
	});
}
</script>