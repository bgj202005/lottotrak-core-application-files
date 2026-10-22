<?php
// Run with: php application\tests\hwc_extra_positions_test.php
define('BASEPATH', __DIR__);
class MY_Model {}
require dirname(__DIR__).'\models\Statistics_m.php';
require dirname(__DIR__).'\models\Predictions_m.php';

function log_message($level, $message)
{
	$GLOBALS['extra_test_logs'][] = array($level, $message);
}

function check($actual, $expected, $message)
{
	if ($actual !== $expected) {
		throw new RuntimeException($message.': '.var_export($actual, true).' !== '.var_export($expected, true));
	}
}

class ExtraTestResult
{
	private $rows;
	public function __construct($rows) { $this->rows = $rows; }
	public function result_array() { return $this->rows; }
	public function row_array() { return $this->rows ? $this->rows[0] : null; }
	public function result() { return array_map(function($row) { return (object)$row; }, $this->rows); }
}

class ExtraTestDatabase
{
	public $rows = array();
	public $tables = array();
	public $last_sql;
	public function query($sql)
	{
		$this->last_sql = $sql;
		if (strpos($sql, 'SELECT ball_drawn') === 0) return new ExtraTestResult(array());
		$rows = $this->rows;
		if (strpos($sql, 'WHERE extra <> 0') !== false) {
			$rows = array_values(array_filter($rows, function($row) { return $row['extra'] != 0; }));
		}
		preg_match('/LIMIT (\d+)/', $sql, $matches);
		return new ExtraTestResult(array_reverse(array_slice($rows, -(int)$matches[1])));
	}
	public function get_where($table, $where, $limit) { return new ExtraTestResult(array($this->tables[$table])); }
	public function escape($value) { return "'".str_replace("'", "''", $value)."'"; }
}

class ExtraTestStatistics extends Statistics_m
{
	public $db;
	public $saved = array();
	public $lotteries_m;
	public function __construct($db) { $this->db = $db; }
	public function hwc_history_save($data, $exist = FALSE) { $this->saved[] = $data; }
	public function db_row($tbl, $row = 0)
	{
		return (object)array('ball1' => 1, 'ball2' => 2, 'ball3' => 3, 'extra' => 1);
	}
}

class ExtraTestLotteries
{
	public $lottery;
	public function get($id) { return $this->lottery; }
	public function lotto_table_convert($name) { return 'draws'; }
}

class ExtraTestLoader
{
	public function model($name) {}
}

class ExtraTestPredictions extends Predictions_m
{
	public $db;
	public $load;
	public $lotteries_m;
	public $statistics_m;
	public function __construct() {}
}

function fixture($balls)
{
	$rows = array();
	foreach ($balls as $i => $ball) {
		$rows[] = array('id' => $i + 1, 'draw_date' => sprintf('2026-01-%02d', $i + 1), 'extra' => $ball);
	}
	return $rows;
}

$db = new ExtraTestDatabase();
$stats = new ExtraTestStatistics($db);
$stats->hwc_duple_extra('draws', 1, 1, 100, '2026-01-01');
check(strpos($db->last_sql, "WHERE draw_date <= '2026-01-01'") !== false, true, 'Historical date cutoff with extras and extra draws included');
check(strpos($db->last_sql, 'ORDER BY draw_date DESC, id DESC LIMIT 100') !== false, true, 'Stable draw ordering');
check(strpos($db->last_sql, 'WHERE ball_drawn <> 0') !== false, true, 'Missing extra is not a ranked ball');
check($stats->hwc_extra_ranking('3=19,2=19,6=17', 1, 7),
	array(2 => 19, 3 => 19, 6 => 17, 1 => 0, 4 => 0, 5 => 0, 7 => 0), 'Complete pool and numeric tie-break');
$db->rows = fixture(array(2, 2, 1, 3, 1, 3, 1));
$history = $stats->hwc_extra_position_history('draws', 1, 3, 3, 0);
check($history, array('position' => array(1, 2, 0), 'position_last' => array(1, 1, 1)), 'Pre-draw ranks and shifted windows');
check($stats->position_string(array(2), array(3), array(4), $history['position']),
	'H>0=2|W>0=3|C>0=4|E>0=1,1=2,2=0', 'Serialize E including zero hits');
check($stats->position_string(array(2), array(3), array(4)),
	'H>0=2|W>0=3|C>0=4', 'Main-only format unchanged');
check($stats->positions_before_last('draws', 3, 1, 1, '1=2', '2=3', '3=4',
	'H>0=2|W>0=3|C>0=4|E>0=1,1=2,2=0', 1, 3, 0),
	'H>0=1|W>0=2|C>0=3|E>0=1,1=1,2=1', 'Recalculation retains a correctly shifted E snapshot');

$db->rows = fixture(array(2, 2, 1, 3, 1, 0, 1));
$with_zero = $stats->hwc_extra_position_history('draws', 1, 3, 3, 1);
check($with_zero, array('position' => array(2, 0, 0), 'position_last' => array(1, 0, 1)), 'Extra=0 consumes a slot but never a hit');
$without_zero = $stats->hwc_extra_position_history('draws', 1, 3, 3, 0);
check(array_sum($without_zero['position']), 3, 'Excluded extra=0 does not consume a slot');

$balls = array();
for ($i = 0; $i < 201; $i++) $balls[] = ($i * 5 % 7) + 1;
$db->rows = fixture($balls);
$hundred = $stats->hwc_extra_position_history('draws', 1, 7, 100, 0);
check(array_sum($hundred['position']), 100, 'Future window totals exactly 100');
check(array_sum($hundred['position_last']), 100, 'Last window totals exactly 100');
check(array_keys($hundred['position']), range(0, 6), 'All seven zero-based positions');

$db->rows = fixture(array(11, 11, 10, 12, 10, 12, 10));
check($stats->hwc_extra_position_history('draws', 10, 12, 3, 0), $history, 'Non-one pool minimum');
$db->rows = fixture(array(1, 2));
check(array_sum($stats->hwc_extra_position_history('draws', 1, 3, 3, 0)['position']), 0, 'No partial training windows');

$lotteries = new ExtraTestLotteries();
$lotteries->lottery = (object)array('id' => 15, 'lottery_name' => 'Daily Grand', 'extra_ball' => 1,
	'duplicate_extra_ball' => 1, 'minimum_extra_ball' => 1, 'maximum_extra_ball' => 3);
$stats->lotteries_m = $lotteries;
$db->rows = fixture(array(2, 2, 1, 3, 1, 3, 1));
$hwc = array('dupextra' => '3=5,2=2,1=1', 'range' => 3, 'extra_draws' => 0);
$legacy = array('position' => 'H>0=5|W>0=4|C>0=3', 'position_last' => 'H>0=4|W>0=4|C>0=3');
$repaired = $stats->refresh_hwc_extra_positions($lotteries->lottery, $hwc, $legacy);
check($repaired['position'], $legacy['position'].'|E>0=1,1=2,2=0', 'Repair persists true ranks, not ball values');
check($repaired['position_last'], $legacy['position_last'].'|E>0=1,1=1,2=1', 'Repair previous snapshot');
check(count($stats->saved), 1, 'Repair saved once');
$stats->refresh_hwc_extra_positions($lotteries->lottery, $hwc, $repaired);
check(count($stats->saved), 1, 'Unchanged history is not rewritten');
$lotteries->lottery->duplicate_extra_ball = 0;
check($stats->refresh_hwc_extra_positions($lotteries->lottery, $hwc, $legacy), $legacy, 'Shared-pool lottery unchanged');
$lotteries->lottery->duplicate_extra_ball = 1;

$db->tables = array('lottery_h_w_c' => $hwc, 'lottery_h_w_c_stats' => $legacy);
$predictions = new ExtraTestPredictions();
$predictions->db = $db;
$predictions->load = new ExtraTestLoader();
$predictions->lotteries_m = $lotteries;
$predictions->statistics_m = $stats;
check($predictions->hwc_extra(15, 2), '2,3', 'Top two rank counts map to current rows; exact distinct pool');
check($predictions->hwc_extra(15, 1), '2', 'One extra prediction');
check($predictions->hwc_extra(15, 3), '1,2,3', 'Entire pool includes zero-hit positions');
$db->tables['lottery_h_w_c']['dupextra'] = '3=5';
check($predictions->hwc_extra(15, 3), '1,2,3', 'Zero-occurrence balls remain selectable');
check($predictions->hwc_extra(15, 4), FALSE, 'Reject pool larger than legal distinct balls');
check($predictions->hwc_extra(15, 2.5), FALSE, 'Reject fractional prediction count');
check($predictions->hwc_extra(15, 0), FALSE, 'Reject zero prediction count');
$db->rows = fixture(array(1, 2));
check($predictions->hwc_extra(15, 2), FALSE, 'No occurrence fallback when training history is unavailable');
check(count($GLOBALS['extra_test_logs']), 4, 'Invalid or unavailable prediction inputs log errors');
echo "PASS: extra-ball ranking, rolling histories, 100-draw totals, repair and exact prediction pools.\n";
