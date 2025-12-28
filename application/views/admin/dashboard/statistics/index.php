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
.reset-statistics {
	padding: 4px 8px;
	font-size: 11px;
	background-color: #ffc107;
	border-color: #ffc107;
	color: #212529;
}
.reset-statistics:hover {
	background-color: #e0a800;
	border-color: #d39e00;
	color: #212529;
}
.reset-statistics:disabled {
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
		<td style = "text-align:center;"><?php echo $statistics->btn_stat('admin/statistics/view_draws/'.$lottery->id); ?></td>
		<td style = "text-align:center;">
			<?php echo $statistics->btn_hwc('admin/statistics/h_w_c/'.$lottery->id); ?>
			<?php if(isset($lottery->needs_hwc_recalc) && $lottery->needs_hwc_recalc): ?>
				<br><small style="color: #d9534f; font-weight: bold; font-size: 12px;">ReCalc Required</small>
			<?php endif; ?>
		</td>
		<td style = "text-align:center;">
			<?php echo $statistics->btn_followers('admin/statistics/followers/'.$lottery->id); ?>
			<?php if($lottery->needs_recalc): ?>
				<br><small style="color: #d9534f; font-weight: bold; font-size: 12px;">ReCalc Required</small>
			<?php endif; ?>
		</td>
		<td style = "text-align:center;"><?php echo $statistics->btn_friends('admin/statistics/friends/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><?php echo $statistics->btn_calculate('admin/statistics/calculate/'.$lottery->id); ?></td>
		<td style = "text-align:center;"><input type="checkbox" name="recalc" value="<?=$lottery->id;?>" class="recalc<?=$lottery->id;?>" id="recalc" <?=($lottery->last_draw!='NA' ? '' : 'disabled');?> >
		<td style = "text-align:center;">
			<button type="button" class="btn btn-sm btn-warning reset-statistics" 
					data-lottery-id="<?=$lottery->id;?>"
					data-lottery-name="<?=htmlspecialchars($lottery->lottery_name);?>"
					title="Reset H-W-C and Follower statistics to start from scratch"
					<?=($lottery->last_draw!='NA' ? '' : 'disabled');?>>
				<i class="fa fa-undo fa-lg" aria-hidden="true"></i> Reset
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
	$('.followers').click(function(e){
		var followersBtn = $(this);
		var href = followersBtn.attr('href');
		
		// Extract lottery ID from the href (admin/statistics/followers/ID)
		var lotteryId = href.split('/').pop();
		var recalcCheckbox = $('.recalc' + lotteryId);
		
		// Check if this lottery needs recalc (has "ReCalc Required" message)
		var needsRecalc = followersBtn.closest('td').find('small:contains("ReCalc Required")').length > 0;
		
		if (needsRecalc && !recalcCheckbox.is(':checked')) {
			e.preventDefault();
			$('#message').removeClass('bg-success').addClass('bg-warning');
			$('#message').html('Click the ReCalc checkbox first before viewing followers data.');
			$('#message').css('display', 'block');
			return false;
		}
		
		$('#status').css('display', 'block');
		$('#message').css('display', 'none'); 
		document.getElementById("status").innerHTML = "Retrieving the Followers and History for the next draw. Please Wait.";
		
		// Allow normal navigation if recalc check passes
		return true;
	});
	$('.friends').click(function(){
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Retrieving the Friends of numbers and History for the next draw. Please Wait.";
	});
	
	// Handle reset statistics button clicks (resets both H-W-C and Followers)
	$('.reset-statistics').click(function(e) {
		e.preventDefault();
		var lotteryId = $(this).data('lottery-id');
		var lotteryName = $(this).data('lottery-name');
		
		if (confirm('Are you sure you want to reset H-W-C and Follower statistics for ' + lotteryName + '?\n\nThis will clear all calculated data and require a full recalculation.')) {
			resetAllStatistics(lotteryId, lotteryName);
		}
	});
	
	function fade_out() {
      $("#status").fadeOut();
    }
});

function redirect(url) {
   window.location.href = url; 
}

function resetAllStatistics(lotteryId, lotteryName) {
	// Show status message
	$('#status').css('display', 'block');
	$('#message').css('display', 'none'); 
	document.getElementById("status").innerHTML = "Resetting statistics for " + lotteryName + ". Please wait...";
	
	// Reset both followers and H-W-C in parallel
	var resetFollowers = $.ajax({
		url: '<?php echo site_url("admin/statistics/reset_followers"); ?>',
		type: 'POST',
		data: { lottery_id: lotteryId },
		dataType: 'json'
	});
	
	var resetHWC = $.ajax({
		url: '<?php echo site_url("admin/statistics/reset_hwc"); ?>',
		type: 'POST',
		data: { lottery_id: lotteryId },
		dataType: 'json'
	});
	
	// Wait for both to complete
	$.when(resetFollowers, resetHWC).done(function(followersResult, hwcResult) {
		$('#status').css('display', 'none');
		var followersResponse = followersResult[0];
		var hwcResponse = hwcResult[0];
		
		if (followersResponse.success && hwcResponse.success) {
			$('#message').removeClass('bg-warning').addClass('bg-success');
			$('#message').html('Statistics reset successfully for ' + lotteryName + '. Click ReCalc to recalculate from scratch.');
			$('#message').css('display', 'block');
			
			// Reload the page to show "ReCalc Required" messages
			setTimeout(function() {
				location.reload();
			}, 2000);
		} else {
			var errorMsg = '';
			if (!followersResponse.success) errorMsg += 'Followers: ' + followersResponse.message + ' ';
			if (!hwcResponse.success) errorMsg += 'H-W-C: ' + hwcResponse.message;
			
			$('#message').removeClass('bg-success').addClass('bg-warning');
			$('#message').html('Error: ' + errorMsg);
			$('#message').css('display', 'block');
		}
	}).fail(function(xhr, status, error) {
		$('#status').css('display', 'none');
		$('#message').removeClass('bg-success').addClass('bg-warning');
		$('#message').html('Error resetting statistics. Server responded with: ' + xhr.status + ' ' + xhr.statusText);
		$('#message').css('display', 'block');
	});
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
				
				// Update UI to show ReCalc Required status
				var followersCell = $('button[data-lottery-id="' + lotteryId + '"]').closest('tr').find('td').eq(11); // Followers column
				var existingMessage = followersCell.find('small');
				if (existingMessage.length === 0) {
					followersCell.append('<br><small style="color: #d9534f; font-weight: bold;">ReCalc Required</small>');
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