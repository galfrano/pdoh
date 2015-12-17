<?php

abstract class pdoh{

	protected $dbn, $stmt = [];

	protected static $pdo = [];

	protected function __construct($dbn){//only sets database name
		$this->dbn = $dbn;}

	protected static function pdo($dbn){//calls adapter, checks initialization
		if(!key_exists($dbn, self::$pdo)){
			$dbc = config::i()->dbc;
			property_exists($dbc, $dbn) or die('No such database'.var_export(config::i(), 1));
			self::$pdo[$dbn] = new \PDO('mysql:host='.$dbc->$dbn->h.';dbname='.$dbn, $dbc->$dbn->u, $dbc->$dbn->p);}//TODO: vendor detection
		return self::$pdo[$dbn];}

	function query($query, $tokens = []){//prepares query, optionaly executes it
		$this->stmt[] = self::pdo($this->dbn)->prepare($query);
		is_bool($tokens) ?: end($this->stmt)->execute((is_array($tokens) ? $tokens : [$tokens])) or die(@end(end($this->stmt)->errorinfo()).var_export($tokens, 1).$query);
		return $this;}

	function id(){
		return self::pdo($this->dbn)->lastInsertId();}

	function execute($tokens = [], $stmt = -1){
		$this->valid($stmt);
		return $this->stmt[$stmt]->execute($tokens);}

	function fetchAll($callback = false, $stmt = -1, $fs = 2){//returns callback should always return a properly indexed array (or a number)
		$this->valid($stmt);
		if($c = is_callable($callback)){
			for($r = []; $line = $this->stmt[$stmt]->fetch($fs); $r += $callback($line, $r));}//TODO:callback pass by reference
		return $c ? $r : $this->stmt[$stmt]->fetchAll($fs);}

	protected function valid(&$stmt){
		$stmt = $stmt < 0 ? count($this->stmt)-1 : $stmt ;
		$stmt >= 0 or die('No statements');}}
