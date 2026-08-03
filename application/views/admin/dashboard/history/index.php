<style>
	table{
    	width:100%;
	}
	tr{
		font-size: 0.77em;
	}
	label {
    	display: inline-flex;
    	margin-bottom: .5rem;
    	margin-top: .5rem;
}
</style>	
<section>
	<h2>Lottery Actual Win History</h2>
	<?php if (isset($message)) : ?> <h4 class="bg-warning" id = "message" style = "margin-top: 20px; text-align:center;"><?=$message; endif; ?></h4>
	<h4 id = "status" style = "margin-top: 20px; text-align:center; display:none;"></h4>	
	<div class = "status"></div>
	<div class="table-responsive">
	<div class ="table-responsive-xl">
	<table class="table table-striped">
		<thead>
			<tr> 
				<th style = "text-align:center; white-space: nowrap;">Logo</th>
				<th style = "text-align:center; white-space: nowrap;">Lottery</th>
				<th style = "text-align:center; white-space: nowrap;">State/Prov</th>
				<th style = "text-align:center; white-space: nowrap;">Country</th>
				<th style = "text-align:center; white-space: nowrap;">At a Glance</th>
				<th style = "text-align:center; white-space: nowrap;">H-W-C Wins</th>
				<th style = "text-align:center; white-space: nowrap;">Follower Wins</th>
				<th style = "text-align:center; white-space: nowrap;">H-W-C + Follower Wins</th>
				<th style = "text-align:center; white-space: nowrap;">Reset Win Records</th>
				<th style = "text-align:center; white-space: nowrap;">Friend Wins</th>
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
		<td style = "text-align:center;"><?php echo $history->btn_glance('admin/history/glance/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_hwc('admin/history/h_w_c/'.$lottery->id, !$lottery->min_draws_met || $lottery->needs_hwc_recalc, $lottery->required_draws, $lottery->needs_hwc_recalc); ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_followers('admin/history/followers/'.$lottery->id, !$lottery->min_draws_met || $lottery->needs_followers_recalc, $lottery->required_draws, $lottery->needs_followers_recalc); ?></td>
		<td style = "text-align:center;"><?php echo $history->btn_hwc_followers('admin/history/hwc_followers/'.$lottery->id, !$lottery->min_draws_met || $lottery->needs_hwc_recalc || $lottery->needs_followers_recalc, $lottery->required_draws, $lottery->needs_hwc_recalc || $lottery->needs_followers_recalc); ?></td>
		<td style = "text-align:center;">
			<button type="button" class="btn btn-sm btn-danger" onclick="resetAllWinStats(<?php echo $lottery->id; ?>)" title="Reset all win statistics">
				<i class="fa fa-undo"></i> Reset Win Records
			</button>
		</td>
		<td style = "text-align:center;"><?php echo $history->btn_friends('admin/history/friends/'.$lottery->id, !$lottery->min_draws_met || $lottery->needs_friends_recalc, $lottery->required_draws, $lottery->needs_friends_recalc); ?></td>
	</tr>
	<?php endforeach; ?> 
	
	<?php else: ?>
		<tr>
			<td colspan="10" style = "text-align:center">No Lotteries are available.</td>
		</tr>
<?php endif; ?>
		</tbody>
	</table>
	</div>
	</div>
</section>
<script>
$(document).ready(function(){
  $('.calculate').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving Lottery Win Results. Please Wait.";
	setTimeout(fade_out, 1500);
	});
	function fade_out() {
      $("#status").fadeOut();
    }
});
function redirect(url) {
   window.location.href = url; 
}

// Reset All Win Statistics (H-W-C, Followers, H-W-C+Followers)
function resetAllWinStats(lotteryId) {
	if(confirm('WARNING: This will clear all prediction win records (H-W-C, Followers, and H-W-C + Followers) and reset the statistics.\n\nThe start date will be set when the next draw is imported.\n\nAre you sure you want to continue?')) {
		$.ajax({
			url: '<?php echo site_url("admin/history/reset_all_win_stats"); ?>',
			type: 'POST',
			dataType: 'json',
			data: { lottery_id: lotteryId },
			success: function(response) {
				if(response.success) {
					alert(response.message || 'All win statistics reset successfully');
					location.reload();
				} else {
					alert(response.message || 'Error resetting win statistics. Please try again.');
				}
			},
			error: function(xhr, status, error) {
				console.error('AJAX Error:', status, error);
				console.error('Response:', xhr.responseText);
				alert('Error resetting win statistics. Check console for details.\n\nStatus: ' + status + '\nError: ' + error);
			}
		});
	}
}
</script>