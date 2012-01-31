<?php
	require_once('config.php');
	$page = $_GET['page'];
	$query = $_GET['query'];
	
	//$page = -1;
	//$query = "bird dog";
	//$USERNAME = "rabbit";
	$query = explode(" ", $query);
	
	if($page == -1)
	{
		//$query = "hack";
		//echo $query;
		$log_file = fopen($PATH."data/".$USERNAME.".log", "a");
		$log_file_total = fopen($PATH."data/total.log", "a");
		$cmd=$PATH."cshell -q '";
		foreach($query as &$val)
		{
			fwrite($log_file,$val."\n");
			fwrite($log_file_total,$val."\n");
			$cmd .= $val."&";
		}
		$cmd = trim($cmd,"&");
		$cmd .= "' > ".$PATH."query_result.tmp";
		//echo $cmd."<br>";
		// $result=shell_exec("/.amd_mnt/gais4/host/home/UserHome/tyl100/search/idb/bin/nSearch -P 81605 -N 50 -459 -5 hack");
		$result=shell_exec($cmd);	
		$page = 0;
	}
	$filename = $PATH."query_result.tmp";
	//echo $filename;
	$handle = fopen($filename, "r");
	$result = fread($handle, filesize($filename));
	
	//$page = 1;
	
	$object=json_decode($result,true);
	if($object!=NULL)
		$flag=1;
	//var_dump($object);
	echo sizeof($object)."||";
	$flag=0;
	if(!isset($object[($page)*20]['thumb']))
	{
		$flag=1;
	}
	for($k=($page)*20;$k<($page+1)*20;$k++)
	{
		if($flag==1)
		{
			$p = xml_parser_create();
			$ch = curl_init($object[$k]['id']); 
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$output = curl_exec($ch);
			xml_parse_into_struct($p, $output, $vals);
			//echo "Vals array\n";
			//print_r($vals);
			for($i=0;$i<sizeof($vals);$i++)
			{
				if(strcmp($vals[$i][tag],"MEDIA:THUMBNAIL")==0)
				{
					break;
				}
			}
			if($vals[$i+1][attributes][URL])
			{
				$object[$k]['thumb'] = $vals[$i+1][attributes][URL];
				//echo $vals[$i+1][attributes][URL]."\n";
			}
			else
			{
				$object[$k]['thumb'] = "img/thumb.jpg\n";
			}
			curl_close($ch);
			xml_parser_free($p);
		}
		$out_object[$k-(($page)*20)] = $object[$k];
		//array_push($output,$object[$k]);
	}
	
	$filename = $PATH."query_result.tmp";
	$handle = fopen($filename, "w");
	$result = fwrite($handle,json_encode($object));
	echo json_encode($out_object);
?>
