<?php
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
</head>
<body>
<form action="search.php" method="GET">
<input type="text" size="50" name="query" >
<input type="submit" name="submit" value="search">
<div id="advance">
<p>
sort:
<input type="radio" name="sort" value="score" checked="checked"><label>by score</label>
<input type="radio" name="sort" value="time"><label>by time</label>
<input type="radio" name="sort" value="size"><label>by size</label>
</p>
<p>
case insensitive:<input type="checkbox" name="case">
</p>
<p>
match field:<br>
<input type="checkbox" name="match[]" value="U"><label>url</label>
<input type="checkbox" name="match[]" value="T" checked="checked"><label>title</label>
<input type="checkbox" name="match[]" value="B" checked="checked"><label>body</label>
<input type="checkbox" name="match[]" value="t"><label>time</label>
</p>
<p>
limit:
<input type="radio" name="limit" value="10" checked="checked"><label>10</label>
<input type="radio" name="limit" value="20"><label>20</label>
<input type="radio" name="limit" value="40"><label>40</label>
<input type="radio" name="limit" value="80"><label>80</label>
</p>
<p>
source:
<input type="radio" name="source" value="news" checked="checked"><label>news</label>
<input type="radio" name="source" value="web"><label>web</label>
</p>
</div>
</body>