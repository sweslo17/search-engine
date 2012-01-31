<?php
	$cmd="./srgrep -o output -s ";
	$case_i=false;
	if($_GET['source'] == "web")
	{
		$input_file="/data/tyl100/record.test.web";
	}
	else if($_GET['source'] == "news")
	{
		$input_file="/data/tyl100/record.test.news";
	}
	if($_GET['sort'] == "score")
	{
		$cmd .= "score";
		$sort[score]=true;
	}
	else if($_GET['sort'] == "size")
	{
		$cmd .= "size";
		$sort[size]=true;
	}
	else if($_GET['sort'] == "time")
	{
		$cmd .= "time";
		$sort[time]=true;
	}
	if($_GET['case'])
	{
		$cmd .= " -i";
		$case_i=true;
	}
	//print_r($_GET['match']);
 	if(!empty($_GET['match']))
	{
		$cmd .= " -k \"";
		if(in_array('T',$_GET['match']))
		{
			$cmd .= "@T:,";
			$match[T]=true;
		}
		if(in_array('B',$_GET['match']))
		{
			$cmd .= "@B:,";
			$match[B]=true;
		}
		if(in_array('U',$_GET['match']))
		{
			$cmd .= "@U:,";
			$match[s]=true;
		}
		if(in_array('t',$_GET['match']))
		{
			$cmd .= "@t:,";
			$match[t]=true;
		}
		$cmd .= "\"";
	} 
	$limit=$_GET['limit'];
	$pattern=$_GET['query'];
	$cmd .= " ".$pattern;
	$cmd .= " ".$input_file;
	$output=shell_exec(escapeshellcmd($cmd));
	$handler=fopen("output","r");
	$file_count=0;
	
	while($line=fgets($handler))
	{
		if($line[0]=='@')
		{
			if(strlen($line)==2)
			{
				$file_count++;
			}
			else if($line[2]==':')
			{
				switch($line[1])
				{
					case 'T':
						$title[$file_count]=substr($line,3);
					break;
					case 'U':
						$url[$file_count]=substr($line,3);
					break;
					case 'B':
						$body[$file_count]=substr($line,3);
					break;
					case 't':
						$time[$file_count]=substr($line,3);
					break;
				}
			}
		}
		else
		{
			$body[$file_count] .= $line;
		}
	}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=big5">
<link rel="stylesheet" href="./jquery-ui/css/custom-theme/jquery-ui-1.8.16.custom.css" type="text/css" media="all" />
			<script src="./jquery-ui/js/jquery-1.6.2.min.js" type="text/javascript"></script>
			<script src="./jquery-ui/js/jquery-ui-1.8.16.custom.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function(){      
		$( "#result" ).tabs();	
});
</script>
</head>
<body size="50">
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

<div id="result" style="width: 1000px;">
<?php echo $file_count; ?>results:
<ul>
<?php
for($i=1;$i<=$file_count;$i++)
{
	if($i%$limit==1)
	{?>
		<li><a href="#result-<?php echo (int)($i/$limit)+1;?>"><?php echo (int)($i/$limit)+1;?></a></li>
	<?php
	}
}
?>
</ul>
<?php
for($i=1;$i<=$file_count;$i++)
{
	if($i%$limit==1)
	{?>
		<div  id="result-<?php echo (int)($i/$limit)+1;?>">
	<?php
	}
	$k=0;
	?>
	<p>
	<a href="<?php echo $url[$i];?>" > <?php echo $title[$i]?></a><br>
	<?php
	if($case_i)
	{
		for($j=0;$j<strlen($body[$i]);$j++)
		{
			if(strcasecmp(substr($body[$i],$j,strlen($pattern)),$pattern)==0)
			{
				echo "<font color=\"RED\">";
				$k=$j;
				for(;$j<$k+strlen($pattern);$j++)
				{
					echo $body[$i][$j];
				}
				echo "</font>";
			}
			else
			{
				echo $body[$i][$j];
			}
		}
	}
	else
	{
		for($j=0;$j<strlen($body[$i]);$j++)
		{
			if(strcmp(substr($body[$i],$j,strlen($pattern)),$pattern)==0)
			{
				echo "<font color=\"RED\">";
				$k=$j;
				for(;$j<$k+strlen($pattern);$j++)
				{
					echo $body[$i][$j];
				}
				echo "</font>";
			}
			else
			{
				echo $body[$i][$j];
			}
		}
	}
	?>
	</p>
	<?php
	if($i%$limit==0)
	{?>
		</div>
	<?php
	}
}

?> 

</div>
</body>
