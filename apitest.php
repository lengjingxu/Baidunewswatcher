<?php
set_time_limit(0);
function async_get_url($url_array,$nb, $wait_usec = 0)
{
    if (!is_array($url_array))
        return false;

    $wait_usec = intval($wait_usec);

    $data    = array();
    $handle  = array();
    $running = 0;

    $mh = curl_multi_init(); // multi curl handler

    $i = 0;
    foreach($url_array as $urlkey) {
        $ch = curl_init();
	$url='http://news.baidu.com/ns?word='.$urlkey.'&sr=0&cl=2&rn='.$nb.'&tn=news&ct=0&clk=sortbytime';

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); // return don't print
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 302 redirect
        curl_setopt($ch, CURLOPT_MAXREDIRS, 7);

        curl_multi_add_handle($mh, $ch); // 把 curl resource 放進 multi curl handler 裡

        $handle[$i++] = $ch;
    }

    /* 執行 */
    /* 此種做法會造成 CPU loading 過重 (CPU 100%)
    do {
        curl_multi_exec($mh, $running);

        if ($wait_usec > 0) // 每個 connect 要間隔多久
            usleep($wait_usec); // 250000 = 0.25 sec
    } while ($running > 0);
    */

    /* 此做法就可以避免掉 CPU loading 100% 的問題 */
    // 參考自: http://www.hengss.com/xueyuan/sort0362/php/info-36963.html
    /* 此作法可能會發生無窮迴圈
    do {
        $mrc = curl_multi_exec($mh, $active);
    } while ($mrc == CURLM_CALL_MULTI_PERFORM);

    while ($active and $mrc == CURLM_OK) {
        if (curl_multi_select($mh) != -1) {
            do {
                $mrc = curl_multi_exec($mh, $active);
            } while ($mrc == CURLM_CALL_MULTI_PERFORM);
        }
    }
    */
    /*
    // 感謝 Ren 指點的作法. (需要在測試一下)
    // curl_multi_exec的返回值是用來返回多線程處裡時的錯誤，正常來說返回值是0，也就是說只用$mrc捕捉返回值當成判斷式的迴圈只會運行一次，而真的發生錯誤時，有拿$mrc判斷的都會變死迴圈。
    // 而curl_multi_select的功能是curl發送請求後，在有回應前會一直處於等待狀態，所以不需要把它導入空迴圈，它就像是會自己做判斷&自己決定等待時間的sleep()。
    */
    do {
        curl_multi_exec($mh, $running);
        curl_multi_select($mh);
    } while ($running > 0);

    /* 讀取資料 */
    foreach($handle as $i => $ch) {
        $content  = curl_multi_getcontent($ch);
		
		$doc = new DOMDocument();
libxml_use_internal_errors(true);
//载入从百度新闻抓取的$data
$doc->loadHTML( <<<HTML_SECTION
$content
HTML_SECTION
);
libxml_clear_errors();
//抓取最近的nb条消息 
$o =  array();
$total=0;
foreach( ( new DOMXPath( $doc ) )->query('//*[@id="kw"]' ) 
				as $nkey ); 
$newskeyword=$nkey->getAttribute('value'); //获取新闻的来源和日期
$o['keyword']=$newskeyword;	// 加入关键词


for ($ii = 1; $ii<=$nb; $ii++) {
	
			foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$ii.'"]/h3[@class="c-title"]' ) 
				as $title )	;
			$newtitle=$title->textContent; //获取新闻标题

			foreach( ( new DOMXPath( $doc ) )->query( '//*[@id="'.$ii.'"]/h3/a' ) 
				 as $nurl );
			$newsurl=$nurl->getAttribute('href'); //获取新闻链接
				
			foreach( ( new DOMXPath( $doc ) )->query('//*[@id="'.$ii.'"]//p[@class="c-author"]' ) 
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
	
		$o['nb']=$total;//加入新闻数量
} 

		
		
		$result[] = $o;
		
		
		
        $data[$i] = (curl_errno($ch) == 0) ? $content : false;
		
		
    }

    /* 移除 handle*/
    foreach($handle as $ch) {
        curl_multi_remove_handle($mh, $ch);
    }

    curl_multi_close($mh);
	
	


    //return $result;
	echo json_encode( $result, JSON_UNESCAPED_UNICODE);
}


	$lists = array("法国","达达","万达","乐视","达达","法国","达达","万达","乐视","达达","法国","达达","万达","乐视","达1达",);  //测试关键词
	$nb=10;
	async_get_url($lists,$nb);
	
?>