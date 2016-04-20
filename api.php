<?php 
set_time_limit(0);

	$lists = array("法国","达达","万达","乐视","达达","法国","达达","万达","乐视","达达","法国","达达","万达","乐视","达1达",);  //测试关键词

class Objet
{ };
foreach ($lists as $keyword) {
$nb=10;
$Url='http://news.baidu.com/ns?word='.$keyword.'&sr=0&cl=2&rn='.$nb.'&tn=news&ct=0&clk=sortbytime';
$curl = curl_init();  
curl_setopt($curl, CURLOPT_URL, $Url );  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
curl_setopt($curl, CURLOPT_AUTOREFERER, true);
curl_setopt($curl, CURLOPT_HEADER, true );  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );  
$data = curl_exec($curl);  
//初始化dom
$doc = new DOMDocument();
libxml_use_internal_errors(true);
//载入从百度新闻抓取的$data
$doc->loadHTML( <<<HTML_SECTION
$data
HTML_SECTION
);
libxml_clear_errors();
//抓取最近的nb条消息 
$o =  array();
$total=0;
for ($i = 1; $i<=$nb; $i++) {
	
			foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$i.'"]/h3[@class="c-title"]' ) 
				as $title )	;
			$newtitle=$title->textContent; //获取新闻标题

			foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$i.'"]/h3/a' ) 
				 as $nurl );
			$newsurl=$nurl->getAttribute('href'); //获取新闻链接
				
			foreach( ( new DOMXPath( $doc ) )->query('//*[@id="'.$i.'"]//p[@class="c-author"]' ) 
				as $author ); 
			$newsauthor=$author->textContent; //获取新闻的来源和日期
	 
	 // 离线测试数据
	 // $newtitle="title".$i;
	 // $newsurl="url".$i;
	 // $newsauthor="author".$i;

			$datelen=mb_strlen(strrchr($newsauthor,chr(0xC2) . chr(0xA0)));//从新闻来源和日期中截取日期
			 
			 if  ($datelen<15) // 日期长度少于15的表示是今天的新闻
			{
				//$result['newslist'][]=array("title"=> "$newtitle","url"=>"$newsurl","date"=>"$newsauthor" );
				$o['newslist'][]=array("title"=> "$newtitle","url"=>"$newsurl","date"=>"$newsauthor" );
				$total=$total+1;
			}//在newslist中加入多条新闻信息。
	
} 

		$o['keyword']=$keyword;	// 加入关键词
		$o['nb']=$total;//加入新闻数量
		
$result[] = $o;


}
curl_close($curl);  

echo json_encode($result, JSON_UNESCAPED_UNICODE);// 转成json 并且不对中文转码

// 大概需要生成的json结构
/* 	
	newslist：
		title："xxxx",url:"xxxxxx",date="xxxx"
		title："xxxx",url:"xxxxxx",date="xxxx"
		title："xxxx",url:"xxxxxx",date="xxxx"
	keyword: "xxxx"
	nb：10 
	
	newslist：
		title："xxxx",url:"xxxxxx",date="xxxx"
		title："xxxx",url:"xxxxxx",date="xxxx"
		title："xxxx",url:"xxxxxx",date="xxxx"
	keyword: "xxxx"
	nb：10 
	
	newslist：
		title："xxxx",url:"xxxxxx",date="xxxx"
		title："xxxx",url:"xxxxxx",date="xxxx"
		title："xxxx",url:"xxxxxx",date="xxxx"
	keyword: "xxxx"
	nb：10 
	 */
	
	

?>