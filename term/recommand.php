<?php
	require_once('config.php');
	//$_GET['gen']=1;
	//$USERNAME = "sweslo17";
	if($_GET['gen']==1)
	{
		$cmd=$PATH."cshell -r ";
		if(file_exists($PATH."data/".$USERNAME.".log"))
		{
			$cmd .= $USERNAME;
		}
		else//??????
		{
			$cmd .= "all";
		}
		//$cmd .= " > ".$PATH."recommand_result.tmp";
		//echo $cmd;
		$result=shell_exec($cmd);
		//echo $result;
	
		//$page = 1;
		
		$object=json_decode($result,true);
		if($object!=NULL)
			$flag=1;
		//var_dump($object);
		for($k=0;$k<sizeof($object);$k++)
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
		$filename = $PATH."data/".$USERNAME."_recommand_result.tmp";
		$handle = fopen($filename, "w");
		$result = fwrite($handle,json_encode($object));
	}
	else
	{
		if(file_exists($PATH."data/".$USERNAME."_recommand_result.tmp"))
		{
			$filename = $PATH."data/".$USERNAME."_recommand_result.tmp";
			$handle = fopen($filename, "r");
			$result = fread($handle, filesize($filename));
			echo $result;
		}
		else
		{
			$cmd=$PATH."cshell -r all";
			$result=shell_exec($cmd);
			$object=json_decode($result,true);
			if($object!=NULL)
				$flag=1;
			//var_dump($object);
			for($k=0;$k<sizeof($object);$k++)
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
			/*$filename = $PATH."data/total_recommand_result.tmp";
			$handle = fopen($filename, "w");
			$result = fwrite($handle,json_encode($object));*/
			echo json_encode($object);
		}
	}
?>
