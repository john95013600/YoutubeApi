<?php

if(isset($_GET['url'])){

	$url = $_GET['url'];

	if(strpos($url,'?') !== false){ 

		$sp_url = explode('?',$url); 
		parse_str($sp_url[1],$sp_urlquery);
		$id = $sp_urlquery['v'];

		if($id=="")die("請輸入正確的 youtube url");
		else getsource($id);
	}

}

function getsource($id){
	
	parse_str(file_get_contents('https://www.youtube.com/get_video_info?video_id='.$id),$info); 
	$streams = explode(',',$info['url_encoded_fmt_stream_map']); 

	$jsonstr = "[";

	foreach($streams as $stream){

		parse_str($stream,$real_stream); 

		$stype = $real_stream['type']; 
		$quality = $real_stream['quality'];
		$url = $real_stream['url'];
		$sig = $real_stream['sig'];
		$title =$info['title'];
		if(strpos($real_stream['type'],';') !== false){
			$tmp = explode(';',$real_stream['type']); 
			$stype = $tmp[0]; 
			unset($tmp); 
		} 

		$source  = $url .'&signature='.$sig;

		$jsonstr .= "{\"type\":\"$stype\",\"quality\":\"$quality\",\"source\":\"$source\",\"title\":\"$title\"},";

	}
	if($info["url_encoded_fmt_stream_map"]){
		$jsonstr = substr($jsonstr,0,-1);
		$jsonstr .= "]";
		echo $jsonstr;
	}
	else{
		$jsonstr ="[{\"reason\":\"$info[reason]\"}]";
		echo $jsonstr;
	}
}


?>