<script type="text/javascript" src="https://www.gstatic.com/charts/loader.js"></script>

<div class="container">
  <div class="row">
    
    <div class="col">
        <div id="piechart_3d" style="width: 500px; height: 300px;"></div>
    </div>
    <div class="col">
      <div class="card bg-light mb-3 mt-2 w-100">
      <div class="card-header"><h3>Latest Lottery News Articles</h3></div>
      <div class="card-body">
            <?php if(count($recent_articles)) : ?>
          <u class="list-group">
          <?php foreach($recent_articles as $article): ?>
            <li class="list-group-item"><?php echo anchor('admin/article/edit/'.$article->id, e($article->title)); ?> - <?php echo date('Y-m-d', strtotime(e($article->modified))); ?></li>
            <?php endforeach; ?>
          </ul>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
    <div class="w-100"></div>   
  <div class="row">  
    <div class="col">
          <div id="chart_div" style="width: 500px; height: 300px;"></div>
    </div>
      <div class="col">
      <div class="card bg-light mb-3 mt-2 w-100">
        <div class="card-header"><h3>Recent Pages Included</h3></div>
        <div class="card-body">
              <?php if(count($recent_pages)) : ?>
            <ul class="list-group">
            <?php foreach($recent_pages as $page): ?>
              <li class="list-group-item"><?php echo anchor('admin/page/edit/'.$page->id, e($page->title));?> - Visible on Menu: <?=($page->menu_item ? 'Yes' : 'Hidden'); ?></li>
              <?php endforeach; ?>
            </ul>
            <?php endif; ?>
            </div>
          </div>
        </div>  
    </div>   
  </div>

<script type="text/javascript">
      google.charts.load("current", {packages:["corechart"]});
      google.charts.setOnLoadCallback(drawChart);
      function drawChart() {
        var data = google.visualization.arrayToDataTable([
          ['Task', 'Visitors per Day'],
          ['New Visitors',     11],
          ['Signups',      2],
          ['Members',  2],
          ['Subscriptions', 2],
          ['Removed',    7]
        ]);

        var options = {
          title: 'My Daily Signups',
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('piechart_3d'));
        chart.draw(data, options);
      }
      
      google.charts.load('current', {'packages':['corechart']});
      google.charts.setOnLoadCallback(drawVisualization);

      function drawVisualization() {
        // Some raw data (not necessarily accurate)
        var data = google.visualization.arrayToDataTable([
          ['Month', 'USA', 'Canada', 'United Kingdon', 'Australia', 'Russia', 'Average'],
          ['January',  165,      938,         522,             998,           450,      614.6],
          ['February',  135,      1120,        599,             1268,          288,      682],
          ['March',  157,      1167,        587,             807,           397,      623],
          ['April',  139,      1110,        615,             968,           215,      609.4],
          ['June',  136,      691,         629,             1026,          366,      569.6]
        ]);

        var options = {
          title : 'Top Country Monthly Sales Report',
          vAxis: {title: 'Sales'},
          hAxis: {title: 'Month'},
          seriesType: 'bars',
          series: {5: {type: 'line'}}        };

        var chart = new google.visualization.ComboChart(document.getElementById('chart_div'));
        chart.draw(data, options);
      }
    </script>  
   
