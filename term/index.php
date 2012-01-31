<?php
    session_start();
    if($_SESSION['admin']==0)
	header('location:./login.php');
    echo "<br>";
    
?>
<html> 
<head> 
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"> 
  <link href="main.css" rel="stylesheet" type="text/css" />
</head>
<body>
  <script type="text/javascript" src="./jquery-1.7.1.min.js"></script>
  <script>
  $(document).ready(function(){
	do_recommand(0);
   $('#submit').click(function(event){
	do_query($('#query').val(),$('#page').val());
	do_recommand(1);
   });
   $('#pnum').live('click',function(){
	var url=$(this).prop("href");
	do_query($('#query').val(),url.split('#')[1]);	
   });
   $('#result').live('click',function(){
	var url=$(this).find("A").prop("href");
	pop_up(url,650,450);
   });
 });
    function do_recommand(type){
    	jQuery.ajax({
	    url: 'recommand.php',
	    dataType: 'html',
	    cache: true,
	    type: 'GET',
	    data: {gen:type},
	    error:function(response){
		alert("error:"+response);
	    },
	    success: function(response){
		if(type==0){
		    obj=eval('('+response+')');
		    con_gen(obj);
		}
	    }
	})
    }
    function do_query(query,page){
	var div=document.createElement("div");
	var obj_num;
	var obj;
	div.id="response";
	div.innerHTML="Querying...";
	document.getElementById("request").appendChild(div);
	$('#content').empty();
	jQuery.ajax({
	    url: 'query.php',
	    dataType: 'html',
	    cache: true,
	    type: 'GET',
	    data: {query:query,page:page},
	    error:function(response){
		alert("error:"+response);
		document.getElementById("request").removeChild(div);
	    },
	    success: function(response){
		obj_num=response.split("||")[0];
		obj=eval('('+response.split("||")[1]+')');
		add_page(obj_num);
		con_gen(obj);
		add_page(obj_num);
		document.getElementById("request").removeChild(div);
	    }
	})
    }
    function add_page(length)
    {
	var i=0;
	var pager=document.createElement("div");
	pager.id="pager";
	while(length>0)
	{
	    pager.innerHTML+="<a href=\"#"+i+"\" id=\"pnum\" >"+(i+1)+"</a> ";
	    length-=20
	    i++;
	}
	pager.innerHTML+="<br>";	
	document.getElementById("content").appendChild(pager);
    }
    function con_gen(obj)
    {
	var i;
	var size;
	if(obj.length>=20)
	    size=20;
	else
	    size=obj.length;
	for(i=0;i<size;i++)
	{
	    var div_handle=document.createElement("div");
	    div_handle.id="result";
	    div_handle.className="content";
	    div_handle.innerHTML="<p>"+obj[i].title+"</p>";
	    div_handle.innerHTML+="<a href="+obj[i].src+" onclick=\"return false\"><img src="+obj[i].thumb+" width=150 height=100/></a><br>";
	    div_handle.innerHTML+="<p>author:"+obj[i].author+"</p>";
	    document.getElementById("content").appendChild(div_handle);
	}
    }
    function pop_up(url,w,h)
    {
	var titleheight = "22px";
	var bordercolor = "#666699";
	var titlecolor = "#FFFFFF";
	var titlebgcolor = "#f7d56e";
	var bgcolor = "#FFFFFF";
	var iWidth = document.documentElement.clientWidth;
        var iHeight = document.documentElement.clientHeight;
	var iframe=document.createElement("div");
	var msgObj=document.createElement("iframe");
	    iframe.style.cssText = "position:absolute;left:0px;top:0px;width:"+iWidth+"px;height:"+Math.max(document.body.clientHeight, iHeight)+"px;filter:Alpha(Opacity=30)    ;opacity:0.3;background-color:#000000;z-index:9998;";
	    msgObj.style.cssText = "position:absolute;font:11px '細明體';top:"+(iHeight-h)/2+"px;left:"+(iWidth-w)/2+"px;width:"+w+"px;height:"+h+"px;text-align:center;bor    der:1px solid "+bordercolor+";background-color:"+bgcolor+";padding:1px;line-height:22px;z-index:9999;";
	iframe.id="iframe";
        msgObj.id="msgObj";
	msgObj.setAttribute("src",url);
    	/*jQuery.ajax({
	    url: url,
	    dataType: 'html',
	    cache: true,
	    error:function(response){
		alert("error:"+response);
	    },
	    success: function(response){
		iframe.innerHTML=response;
	    }
	});*/
	
	document.body.appendChild(iframe);
	document.body.appendChild(msgObj);
	iframe.onclick=function(){
		document.body.removeChild(iframe);
		document.body.removeChild(msgObj);
	}

    }
  </script>
        <div id="top" align="left">
            hi! <?php echo $_SESSION['username'];?>,
            <a href="./logout.php">登出</a><br>
        </div>
	<div id="request" align=center>
	    <img src="./mark.png"><br>
	    <input type="text" name="query" id="query" size="68">
	    <input type="submit" name="submit" id="submit">
	    <input type="hidden" name="page" id="page" value=-1>
	    <br><br>
	</div>
	<div id="content">
	</div>
    

</body>
</html>
