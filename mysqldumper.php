<?php /*galfrano at gmail dot com*/
/*simple dumper. not binary safe! only simple quote escape. one table in memory at a time*/
class MysqlDump{

	private $pdo, $fileName;

	function __construct($d, $u, $p, $fileName){
		list($this->pdo, $this->fileName) = [new PDO('mysql:host=localhost;dbname='.$d, $u, $p), $fileName];
		file_put_contents($this->fileName, '');
		$this->dump();}

	private function createTable($table){
		$create = $this->pdo->prepare('show create table '.$table);
		$create->execute();
		$create = current($create->fetchAll(2));
		return $create['Create Table'].";\n";}

	private function tableData($table){
		$select = $this->pdo->prepare('select * from '.$table);
		$insert = $lines = 'INSERT INTO '.$table." VALUES \n";
		for($select->execute(), $l = 0; $line = $select->fetch(2); $lines .= "('".implode("', '", array_map(function($v){return str_replace("'", "\'", $v);}, $line))."')".(++$l%100?",\n":";\n$insert"));
		return $l == 0 ? '' : substr($lines, 0, -2).";\n\n";}

	private function dump(){
		foreach($this->pdo->query('show tables') as $row){
			$table = current($row);
			file_put_contents($this->fileName, $this->createTable($table).$this->tableData($table), FILE_APPEND);}}}

list($d, $u, $p, $f) = ['dbName', 'root', '', 'testdump.sql'];

new MysqlDump($d, $u, $p, $f);
