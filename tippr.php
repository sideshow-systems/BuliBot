#!/opt/local/bin/php
<?php
/**
 * Run this script via shell to calc match results and post them
 */
require_once 'src/BuliBot.php';
require_once 'src/ScoreCalculator.php';

// Set config, zend and other stuff
$config = require_once 'config.php';
ini_set('include_path', ini_get('include_path') . ':' . $config['zend_path']);

require_once $config['zend_path'] . '/Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->setFallbackAutoloader(true);

$_SERVER['config'] = $config;


// Get options from command line
try {
	$opts = new Zend_Console_Getopt(
		array(
		'playday|p=i' => 'Playday',
		'dryrun|d' => 'Dryrun'
		)
	);
	$opts->parse();
} catch (Zend_Console_Getopt_Exception $e) {
	echo $e->getUsageMessage();
	exit;
}
//Zend_Debug::dump($opts);
$pd = $opts->getOption('p');
$dr = $opts->getOption('d');

// BuliBot instance
$buliBot = new BuliBot($config);

// Set options
$buliBot->setPlayday($pd);
if (!empty($dr)) {
	$buliBot->setDryrun($dr);
}

// Set ScoreCalculator
$buliBot->setScoreCalculator(new ScoreCalculator($config));

// Get matches by playday
$matches = $buliBot->getMatchesByPlayday($pd);

// Walk thru matches and guess result
foreach ($matches['matchdata'] as $match) {
	//Zend_Debug::dump($match);
	$data = $buliBot->guessResultOfMatchByMatchId($match['match_id']);

	// Just show results in shell
	if (!empty($data)) {
		$keys = array_keys($data);
		$resultString = '(' . $keys[0] . ') ' . $data[$keys[0]]['teamname'] . ' == ' . $data[$keys[0]]['guessgoals'] . ':';
		$resultString .= $data[$keys[1]]['guessgoals'] . ' == ' . $data[$keys[1]]['teamname'] . ' (' . $keys[1] . ')';
		echo $resultString . PHP_EOL;

		// Submit data
		$buliBot->submitData($match['match_id'], $data);

//		Zend_Debug::dump($data);
	}

//	break;
}
?>
