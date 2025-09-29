<link href="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.css" rel="stylesheet">

<script src="https://unpkg.com/bootstrap-table@1.18.0/dist/bootstrap-table.min.js"></script>
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
	
	.pagination-container {
		display: flex;
		justify-content: space-between;
		align-items: center;
		margin: 20px 0;
	}
	
	.page-size-selector {
		margin-right: 15px;
	}
	
	.loading-spinner {
		text-align: center;
		padding: 20px;
		display: none;
	}
	
	.draws-container {
		min-height: 400px;
	}
	
</style>

	<h2><?php echo 'View Statistics for: '.$lottery->lottery_name; ?></h2>	
	<h5 style = "text-align:left"><?php echo anchor('admin/statistics', 'Back to Statistics Dashboard', 'title="Back to Statistics"'); ?></h5>
	
	<!-- Statistics Section (unchanged) -->
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
	
	<!-- Odds/Evens Section (unchanged) -->
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
					<th data-field="All_percent" data-halign="center" data-align="center">ALL %</th>
					<th data-field="Last_10" data-halign="center" data-align="center" data-sortable="true">LAST 10</th>
					<th data-field="10_percent" data-halign="center" data-align="center">LAST 10 %</th>
					<th data-field="Last_100" data-halign="center" data-align="center" data-sortable="true">LAST 100</th>
					<th data-field="100_percent" data-halign="center" data-align="center">LAST 100 %</th>
					<th data-field="Last_200" data-halign="center" data-align="center" data-sortable="true">LAST 200</th>
					<th data-field="200_percent" data-halign="center" data-align="center">LAST 200 %</th>
					
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
	
	<!-- Optimized Draws Section with AJAX Pagination -->
	<section>
		<div class="pagination-container">
			<div class="d-flex align-items-center">
				<div class="page-size-selector">
					<label for="pageSize" class="mr-2">Rows per page:</label>
					<select id="pageSize" class="form-control form-control-sm" style="width: auto; display: inline-block;">
						<option value="10" selected>10</option>
						<option value="25">25</option>
						<option value="50">50</option>
						<option value="100">100</option>
						<option value="200">200</option>
					</select>
				</div>
				<div class="ml-3">
					<input type="text" id="searchBox" class="form-control form-control-sm" placeholder="Search draws..." style="width: 200px;">
				</div>
			</div>
			<div id="pagination-info" class="text-muted">
				<!-- Pagination info will be loaded here -->
			</div>
		</div>
		
		<div class="loading-spinner" id="loadingSpinner">
			<div class="spinner-border" role="status">
				<span class="sr-only">Loading...</span>
			</div>
			<p>Loading draws data...</p>
		</div>
		
		<div class="draws-container">
			<div class="table-responsive">
				<table class="table table-striped table-sm" id="draws-optimized">
					<thead>
						<tr>
							<th class="datafont">Draw #</th>
							<th class="datafont">Draw Date</th>
							<th class="datafont">Ball 1</th>
							<th class="datafont">Ball 2</th>
							<th class="datafont">Ball 3</th>
							<?php if (intval($lottery->balls_drawn)>=4): ?><th class="datafont">Ball 4</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=5): ?><th class="datafont">Ball 5</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=6): ?><th class="datafont">Ball 6</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=7): ?><th class="datafont">Ball 7</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)>=8): ?><th class="datafont">Ball 8</th><?php endif; ?>
							<?php if (intval($lottery->balls_drawn)==9): ?><th class="datafont">Ball 9</th><?php endif; ?>
							<?php if (intval($lottery->extra_ball)==1): ?><th class="datafont">Extra Ball</th><?php endif; ?>
							<th class="datafont">Sum</th>
							<th class="datafont">Digits Sum</th>
							<th class="datafont">Odd</th>
							<th class="datafont">Even</th>
							<th class="datafont">Range</th>
							<th class="datafont">Max Decade</th>
							<th class="datafont">Max Last</th>
						</tr>
					</thead>
					<tbody id="draws-tbody">
						<tr>
							<td colspan="20" style="text-align: center; padding: 20px;">
								<div class="text-info">
									<i class="fas fa-spinner fa-spin"></i> 
									Loading draws data with AJAX pagination...
								</div>
								<small class="text-muted">
									This may take a moment for large datasets. If this message persists, please check the browser console for errors.
								</small>
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
		
		<!-- Pagination Controls -->
		<nav aria-label="Draws pagination">
			<ul class="pagination justify-content-center" id="pagination-controls">
				<!-- Pagination buttons will be generated here -->
			</ul>
		</nav>
	</section>

<script>
// Optimized AJAX Pagination System
class DrawsPagination {
	constructor() {
		this.currentPage = 1;
		this.pageSize = 10; // Default to 10 rows per page for 300+ draws
		this.totalPages = 1;
		this.total = 0;
		this.trend = <?= $trend; ?>;
		this.lotteryId = <?= $lottery->id; ?>;
		this.requestedRange = <?= $range; ?>; // The requested range (200, 300, etc.)
		this.searchTerm = '';
		this.isLoading = false;
		
		this.initializeEventListeners();
		this.loadDraws();
	}
	
	initializeEventListeners() {
		// Page size change
		$('#pageSize').on('change', () => {
			this.pageSize = parseInt($('#pageSize').val());
			this.currentPage = 1;
			this.loadDraws();
		});
		
		// Search with debounce
		let searchTimeout;
		$('#searchBox').on('input', () => {
			clearTimeout(searchTimeout);
			searchTimeout = setTimeout(() => {
				this.searchTerm = $('#searchBox').val();
				this.currentPage = 1;
				this.loadDraws();
			}, 300);
		});
		
		// Trends checkbox
		$('#trends').on('change', function() {
			// Update trend state and reload data for AJAX pagination
			drawsPagination.trend = this.checked ? 1 : 0;
			drawsPagination.currentPage = 1;
			drawsPagination.loadDraws();
		});
	}
	
	async loadDraws() {
		if (this.isLoading) return;
		
		this.isLoading = true;
		this.showLoading(true);
		
		try {
			const url = `<?= base_url('admin/statistics/ajax_load_draws/'.$lottery->id); ?>?` + 
				new URLSearchParams({
					page: this.currentPage,
					limit: this.pageSize,
					trend: this.trend,
					range: this.requestedRange,
					search: this.searchTerm
				});
			
			const response = await fetch(url);
			
			if (!response.ok) {
				throw new Error(`HTTP error! status: ${response.status}`);
			}
			
			const data = await response.json();
			
			if (data.error) {
				throw new Error(data.error);
			}
			
			this.renderDraws(data.draws, data.lottery_config);
			this.updatePagination(data.pagination);
			this.updatePaginationInfo(data.pagination);
			
		} catch (error) {
			console.error('Error loading draws:', error);
			this.showError('Failed to load draws data. Please try again.');
		} finally {
			this.isLoading = false;
			this.showLoading(false);
		}
	}
	
	renderDraws(draws, lotteryConfig) {
		const tbody = document.getElementById('draws-tbody');
		tbody.innerHTML = '';
		
		draws.forEach(draw => {
			const row = document.createElement('tr');
			
			// Helper function to get repeater icon
			const getRepeater = (ballName) => {
				return (draw.repeaters && draw.repeaters[ballName]) ? 
					'<img src="<?= base_url(); ?>images/assets/repeat-icon.png" class="repeater">' : '';
			};
			
			// Helper function to get trend arrow  
			const getTrend = (ballName) => {
				return (this.trend && draw.trends && draw.trends[ballName]) ? draw.trends[ballName] : '';
			};
			
			// Helper function to get the appropriate symbol (repeater or trend)
			// Following the logic from the synchronous version
			const getBallSymbol = (ballName, ballIndex) => {
				// For balls 1-6, check extra condition for repeaters (mimicking synchronous logic)
				if (ballIndex <= 6) {
					if (draw.repeaters && draw.repeaters[ballName] && lotteryConfig.extra_ball == 1) {
						return getRepeater(ballName);
					} else {
						return getTrend(ballName);
					}
				} else {
					// For balls 7-9, no extra condition needed for repeaters
					if (draw.repeaters && draw.repeaters[ballName]) {
						return getRepeater(ballName);
					} else {
						return getTrend(ballName);
					}
				}
			};
			
			let ballsHtml = `
				<td class="datafont">${draw.draw}</td>
				<td style="white-space: nowrap;">${draw.draw_date}</td>
				<td class="datafont">${draw.ball1}${getBallSymbol('ball1', 1)}</td>
				<td class="datafont">${draw.ball2}${getBallSymbol('ball2', 2)}</td>
				<td class="datafont">${draw.ball3}${getBallSymbol('ball3', 3)}</td>
			`;
			
			// Add additional balls based on lottery configuration
			if (lotteryConfig.balls_drawn >= 4) ballsHtml += `<td class="datafont">${draw.ball4}${getBallSymbol('ball4', 4)}</td>`;
			if (lotteryConfig.balls_drawn >= 5) ballsHtml += `<td class="datafont">${draw.ball5}${getBallSymbol('ball5', 5)}</td>`;
			if (lotteryConfig.balls_drawn >= 6) ballsHtml += `<td class="datafont">${draw.ball6}${getBallSymbol('ball6', 6)}</td>`;
			if (lotteryConfig.balls_drawn >= 7) ballsHtml += `<td class="datafont">${draw.ball7}${getBallSymbol('ball7', 7)}</td>`;
			if (lotteryConfig.balls_drawn >= 8) ballsHtml += `<td class="datafont">${draw.ball8}${getBallSymbol('ball8', 8)}</td>`;
			if (lotteryConfig.balls_drawn == 9) ballsHtml += `<td class="datafont">${draw.ball9}${getBallSymbol('ball9', 9)}</td>`;
			
			// Extra ball only shows trends, no repeaters (following synchronous logic)
			if (lotteryConfig.extra_ball == 1) ballsHtml += `<td class="datafont">${draw.extra}${getTrend('extra')}</td>`;
			
			ballsHtml += `
				<td class="datafont">${draw.sum_draw}</td>
				<td class="datafont">${draw.sum_digits}</td>
				<td class="datafont">${draw.odd}</td>
				<td class="datafont">${draw.even}</td>
				<td class="datafont">${draw.range_draw}</td>
				<td class="datafont">${draw.repeat_decade}</td>
				<td class="datafont">${draw.repeat_last}</td>
			`;
			
			row.innerHTML = ballsHtml;
			tbody.appendChild(row);
		});
	}
	
	updatePagination(pagination) {
		this.total = pagination.total;
		this.totalPages = pagination.last_page;
		this.currentPage = pagination.current_page;
		
		const paginationContainer = document.getElementById('pagination-controls');
		paginationContainer.innerHTML = '';
		
		// Previous button
		const prevButton = this.createPaginationButton('Previous', this.currentPage - 1, this.currentPage === 1);
		paginationContainer.appendChild(prevButton);
		
		// Calculate visible page range
		const maxVisible = 5;
		let startPage = Math.max(1, this.currentPage - Math.floor(maxVisible / 2));
		let endPage = Math.min(this.totalPages, startPage + maxVisible - 1);
		
		if (endPage - startPage + 1 < maxVisible) {
			startPage = Math.max(1, endPage - maxVisible + 1);
		}
		
		// First page
		if (startPage > 1) {
			paginationContainer.appendChild(this.createPaginationButton('1', 1));
			if (startPage > 2) {
				const ellipsis = document.createElement('li');
				ellipsis.className = 'page-item disabled';
				ellipsis.innerHTML = '<span class="page-link">...</span>';
				paginationContainer.appendChild(ellipsis);
			}
		}
		
		// Visible pages
		for (let i = startPage; i <= endPage; i++) {
			paginationContainer.appendChild(this.createPaginationButton(i, i, false, i === this.currentPage));
		}
		
		// Last page
		if (endPage < this.totalPages) {
			if (endPage < this.totalPages - 1) {
				const ellipsis = document.createElement('li');
				ellipsis.className = 'page-item disabled';
				ellipsis.innerHTML = '<span class="page-link">...</span>';
				paginationContainer.appendChild(ellipsis);
			}
			paginationContainer.appendChild(this.createPaginationButton(this.totalPages, this.totalPages));
		}
		
		// Next button
		const nextButton = this.createPaginationButton('Next', this.currentPage + 1, this.currentPage === this.totalPages);
		paginationContainer.appendChild(nextButton);
	}
	
	createPaginationButton(text, page, disabled = false, active = false) {
		const li = document.createElement('li');
		li.className = `page-item ${disabled ? 'disabled' : ''} ${active ? 'active' : ''}`;
		
		const link = document.createElement('a');
		link.className = 'page-link';
		link.href = '#';
		link.textContent = text;
		
		if (!disabled && !active) {
			link.addEventListener('click', (e) => {
				e.preventDefault();
				this.goToPage(page);
			});
		}
		
		li.appendChild(link);
		return li;
	}
	
	goToPage(page) {
		if (page >= 1 && page <= this.totalPages && page !== this.currentPage) {
			this.currentPage = page;
			this.loadDraws();
		}
	}
	
	updatePaginationInfo(pagination) {
		const start = ((pagination.current_page - 1) * pagination.per_page) + 1;
		const end = Math.min(pagination.current_page * pagination.per_page, pagination.total);
		
		document.getElementById('pagination-info').innerHTML = 
			`Showing ${start} to ${end} of ${pagination.total} entries`;
	}
	
	showLoading(show) {
		document.getElementById('loadingSpinner').style.display = show ? 'block' : 'none';
	}
	
	showError(message) {
		console.error('DrawsPagination Error:', message);
		// Display error in the draws container
		document.getElementById('draws-tbody').innerHTML = 
			`<tr><td colspan="20" class="text-center text-danger">Error: ${message}</td></tr>`;
		// Also show alert for now
		alert(`Error loading draws: ${message}`);
	}
}

// Initialize when DOM is ready
$(document).ready(function() {
	// Initialize other bootstrap tables first (without problematic extensions)
	try {
		$('#history').bootstrapTable({
			filterControl: false,
			reorderableRows: false
		});
		$('#evenodds').bootstrapTable({
			filterControl: false, 
			reorderableRows: false
		});
	} catch (tableError) {
		console.error('Error initializing bootstrap tables:', tableError);
		// Continue with the main pagination system even if tables fail
	}
	
	try {
		// Initialize the optimized draws pagination (make it global for trend checkbox access)
		window.drawsPagination = new DrawsPagination();
	} catch (error) {
		console.error('Failed to initialize DrawsPagination:', error);
		
		// Show an error message to the user
		$('#draws-tbody').html(`
			<tr>
				<td colspan="20" style="text-align: center; color: red; padding: 20px;">
					<strong>Error loading draws:</strong><br>
					${error.message}<br>
					<small>Please check the browser console for more details.</small>
				</td>
			</tr>
		`);
	}
});
</script>