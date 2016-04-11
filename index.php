
<?php 
// todo:
// 关键词的列表文件
//写成function 
// 标红今日新闻
// 把需要的新闻数量配置加到前端
// 改为输出json
// 前后端分离 
// 数据库——数据库 index URL栏，录入前查询URL重复
// 数据库 使用leancloud


$keyword='dada'; 
$Url='http://news.baidu.com/ns?word='.$keyword.'&bs=dada&sr=0&cl=2&rn=50&tn=news&ct=0&clk=sortbytime';
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
//抓取最近的50条消息 
for ($i = 1; $i<50; $i++) {
	echo $i ;
foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$i.'"]/h3' ) 
    as $title )	;
echo "<h2>".$title->textContent."</h2><br>";

foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$i.'"]/h3/a' ) 
     as $nurl );
$newsurl=$nurl->getAttribute('href');
echo $newsurl;
	
foreach( ( new DOMXPath( $doc ) )->query('//*[@id="'.$i.'"]/div/div[2]/p' ) 
    as $author )	;
echo	$author->textContent;
echo "<br>"; 
}