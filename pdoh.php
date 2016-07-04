<?php /*by galfrano@gmail.com*/
/*the role of this class is solely to provide interactions with database adapter and statements. database agnostic.*/
abstract class pdoh{

	//TODO: create and import some constants for readability

	protected $dbi, $stmt = [];

	protected static $pdo = [];

	function __construct($dbi, $pdo){
		key_exists($dbi, self::$pdo) ?: ($pdo instanceof PDO ? self::$pdo[$dbi] = $pdo : self::kill('Second parameter must be PDO (or first parameter must be a previously assigned index).'));
		$this->dbi = $dbi;}

	protected static function kill($stmt, $tokens = []){//throws exception
		$message = $stmt instanceof PDOStatement ? $stmt->errorInfo()[2]."\n\n\t".'Query string: '.$stmt->queryString."\n\t".'Tokens: '.str_replace("\n", '', var_export($tokens, 1)) : $stmt ;
		throw new \Exception($message."\n\n");}

	function smdbg(){
		var_export(get_object_vars(self::$pdo[$this->dbi]));
		return $this;}

	function query($query, $tokens = []){//prepares query, optionaly executes it
		$this->stmt[] = self::$pdo[$this->dbi]->prepare($query);
		is_bool($tokens) ?: end($this->stmt)->execute((is_array($tokens) ? $tokens : [$tokens])) or self::kill(end($this->stmt), $tokens);
		return $this;}

	function id(){
		return self::$pdo[$this->dbi]->lastInsertId();}

	function execute($tokens = [], $stmt = -1){
		$this->valid($stmt);
		return $this->stmt[$stmt]->execute($tokens);}

	function fetchAll($callback = false, $stmt = -1, $fs = 2){
		$this->valid($stmt);
		if($c = is_callable($callback)){
			for($r = []; $line = $this->stmt[$stmt]->fetch($fs); $cr = $callback($line, $r), !is_array($cr) ?: $r += $cr);}//TODO:callback pass by reference
		return $c ? $r : $this->stmt[$stmt]->fetchAll($fs);}

	protected function valid(&$stmt){
		$stmt = $stmt < 0 ? count($this->stmt)-1 : $stmt ;
		$stmt >= 0 or die('No statements');}}
