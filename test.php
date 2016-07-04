<!doctype html>
<html>
<head>
<meta charset="utf8" />
<style>
td, th, table, pre{border:1px solid;}
</style>
</head>
<body>
<pre>
<?php
include __DIR__.DIRECTORY_SEPARATOR.'pdoh.php';

//extend abstract
class a extends pdoh{
	static function i($dbi, $pdo = false){
		return new self($dbi, $pdo);}}

$db = a::i('someIndex', new PDO('mysql:dbname=jml_vm;host=localhost;', 'root', ''));

//print and collect table names
$tables = $db->query('show tables')->fetchAll(function($line, $r){
	print(current($line)."\n");
	return [count($r)=>current($line)];}); ?>
</pre>
<?php
//print all tables
for($heads = [];
		list($k, $table) = each($tables);
			print '<table>'."\n".'<tr><th colspan="100%"><h3>'.$table.'</h3></th></tr>'."\n",
			$db->query('SELECT * FROM '.$table.' LIMIT 0, 10')->fetchAll(function($line, $r) use ($heads, $table){
				$heads[$table] = key_exists($table, $heads) ?: (print '<tr><th>'.implode('</th><th>', array_keys($line)).'</th></tr>') || true ;
				print '<tr><td>'.implode('</td><td>', $line).'</td></tr>'."\n";}),
			print'</table>'."\n"); ?>
</body>
</html>
