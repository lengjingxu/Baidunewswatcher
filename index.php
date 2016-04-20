<style>.red {color:red;}</style>
<?php 
// todo:
// 关键词的列表文件
//
// 
// 改为输出json
// 前后端分离 
// 数据库——数据库 index URL栏，录入前查询URL重复
// 数据库 使用leancloud


function  CurlNews ($keyword,$nb)
	{ 
$Url='http://news.baidu.com/ns?word='.$keyword.'&sr=0&cl=2&rn='.$nb.'&tn=news&ct=0&clk=sortbytime';
$curl = curl_init();  
curl_setopt($curl, CURLOPT_URL, $Url );  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);  
curl_setopt($curl, CURLOPT_AUTOREFERER, true);
curl_setopt($curl, CURLOPT_HEADER, true );  
curl_setopt($curl, CURLOPT_RETURNTRANSFER, true );  
$data = curl_exec($curl);  
curl_close($curl);  
//初始化dom
$doc = new DOMDocument();
libxml_use_internal_errors(true);
//载入抓取的$data
$doc->loadHTML( <<<HTML_SECTION
$data
HTML_SECTION
);
libxml_clear_errors();
//抓取最近的nb条消息 
for ($i = 1; $i<=$nb; $i++) {
foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$i.'"]/h3[@class="c-title"]' ) 
    as $title )	;
$newtitle=$title->textContent;
//echo "<div class='newstitle'>".$newtitle."</div><br>";

foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$i.'"]/h3/a' ) 
     as $nurl );
$newsurl=$nurl->getAttribute('href');
	
foreach( ( new DOMXPath( $doc ) )->query('//*[@id="'.$i.'"]//p[@class="c-author"]' ) 
    as $author ); 
$newsauthor=$author->textContent; 


$datelen=mb_strlen(strrchr($newsauthor,chr(0xC2) . chr(0xA0)));
if (empty($TodayNews)) $TodayNews = 0;

if  ($datelen<15) 
	{
		
echo "<div class='no'>".$i."</div>" ;
echo "<div class='newstitle red'><a href=".$newsurl.">".$newtitle."</a></div><br>";
 //echo "<div class='newsurl'><a href=".$newsurl.">".$newsurl."</a></div>";
 echo "<div class='newsauther'>".$newsauthor."</div>";
	$TodayNews=$TodayNews+1;
	
	} 
	// else{
	// echo "<div class='newstitle'>".$newtitle."</div><br>";
	// }

// echo "<br>"; 

} 
echo "<h2>今天有".$TodayNews."条新闻</h2><hr>";
$TodayNews=0; 
}


$lists = array("法国","达达","雅典","中国","36kr"); 

foreach ($lists as $value) {
  echo "$value <br>";
  CurlNews( $value,'10');

}




?>