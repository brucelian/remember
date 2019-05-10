<?php
namespace app\remember\controller;

use think\Controller;
use app\remember\model\EnglishModel;

class Baicizhan extends Controller
{
	private $base_m;

	public function __construct(){
		parent::__construct();
		
		$this->base_m = new EnglishModel();
	}

	// 采集百词斩二进制数据
	public function mylearn()
	{
		echo '<title>采集百词斩数据 Remember</title>';

		set_time_limit(0);

		$list = $this->base_m->listNoBczEnglish();

		$count_scuess = $count_error = 0;
		foreach ($list as $k => $v) {
			
			$topic_id = $v['topic_id'];

			$colsA     = ($topic_id > 63 ? ($topic_id*2 % 128 + 128) : ($topic_id*2 % 128));
			$quotient1 = ((int)($topic_id / 64) > 255 ? (int)($topic_id / 64) % 128 + 128 : (int)($topic_id / 64));
			$quotient2 = floor($topic_id / 8192);
			$quotient3 = floor($topic_id / 1048576);

			echo '<h1>'.$topic_id.' '.$v['a'].'</h1><br>';

			// echo strtoupper(
			// 	dechex($colsA).' '
			// 	.dechex($quotient1).' '
			// 	.dechex($quotient2).' '
			// 	.dechex($quotient3)
			// );
			// echo '<br><br>';
			// echo strtoupper(
			// 	($colsA).' '
			// 	.($quotient1).' '
			// 	.($quotient2).' '
			// 	.($quotient3)
			// );

			$url = "http://resource.baicizhan.com/rpc/resource_api/get_topic_resource_v2/1553509889104";
			$s1 = "\x82\x21\x01\x15get_topic_resource_v2\x1C\x15"
			.chr($colsA)
			.($quotient1 ? chr($quotient1) : '')
			.($quotient2 ? chr($quotient2) : '')
			.($quotient3 ? chr($quotient3) : '')
			."\x15\x00\x15\x00\x00\x15\x02\x12\x12\x11\x11\x11\x00";
			$s = "\x00\x00\x00".chr(strlen($s1)).$s1;

			// $url = "http://resource.baicizhan.org/rpc/resource_api/get_dict_wiki_by_word/1551588812967";
			// $s = "\x00\x00\x00".chr(0x20)."\x82\x21\x01\x15get_dict_wiki_by_word\x18".chr(0x04).$kw."\x00";

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLINFO_HEADER_OUT, TRUE);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
			curl_setopt($ch, CURLOPT_TIMEOUT, 2);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $s);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
								'Content-Type: application/x-thrift',
								'Accept: application/x-thrift',
								'User-Agent: Java/THttpClient bcz_dict_app_android/1001304',
								'Compress-Type: plain',
								'Host: resource.baicizhan.com',
								'Connection: Keep-Alive',
								'Content-Length: '.strlen($s),
								// '',
								// $s
							)
						);
			curl_setopt($ch, CURLOPT_COOKIE, 'access_token=7386d796287a750f9dfbbc696397152e; device_name=android/Redmi 5 Plus - Xiaomi; serial=5364c9062325181236; 
				device_id=5364cab237790623; 
				version=7.1.2; 
				channel=null; 
				app_name=1001304');
			echo $data = curl_exec($ch);


			// echo '<pre>'.curl_getinfo($ch, CURLINFO_HEADER_OUT).'</pre>';
			// echo '<pre>'.$data.'</pre>';

			curl_close($ch);


			// // print_r($data);
			// $l=0;
			// foreach ($data['list'] as $k => $v) {

			$insertData = [
				'bcz' => $data
			];

			if(strpos($data, $v['a'])){
				if ($this->base_m->updateEnglishDetailData(['topic_id'=>$v['topic_id']], $insertData) > 0) {
					echo '<span style="color:green;">'.$v['topic_id'].'===> '.$v['a'].'</span><br>';
					$count_scuess++;
				} else {
					echo '<span style="color:red;">'.$v['topic_id'].'===> '.$v['a'].'</span><br>';
					echo '<pre>'.$data.'</pre>';
					$count_error++;
				}
			}

			// 即时输出数据到浏览器
			ob_flush(); //将数据从php的buffer中释放出来
			flush(); //将释放出来的数据发送给浏览器
		}

		echo 'success ====> '.$count_scuess;
		echo 'error ====> '.$count_error;
	}

	// 百词斩数据提取资源链接
	public function getSourceURI()
	{
		set_time_limit(0);

		// 1、读取数据库记录
		$list = $this->base_m->listHaveBczBlob(0);
		echo '<h1>count===>'.sizeof($list).'</h1>';

		$count_scuess = $count_error = 0;

		// 2、解析二进制数据
		foreach ($list as $k1 => $v1) {
			echo '【【'.$v1['id'].'】】 topic_id-->'.$v1['topic_id'].'<br>';
			$d = $v1['bcz'];

			$boundary_t = strpos($d, "\x0c\x00\x3c\x1c\x1c");
			$boundary_t2 = substr($d, $boundary_t + 5);
			$end_pos = strpos($boundary_t2, "\x18");
			if(substr($boundary_t2, strpos($boundary_t2, "\x18") + 1, 1)=="\x18"){
				$end_pos++;
			}
			$boundary = substr($boundary_t2, 0, $end_pos);


			// 分块集
			$s1 = [];
			$s1_t = explode("\x00\x19", $d);
			// print_r($s1_t);
			// echo 'size==>'.sizeof($s1_t)."\r\n";
			foreach ($s1_t as $k7=>$v7) {
				// echo '%';
				// echo $v7;
				$s1_t2 = explode("\x19\x0c", "\x19".$v7);
				if($s1_t2){
					foreach ($s1_t2 as $k8 => $v8) {
						$s1[] = $v8;
					}
				} else {
					$s1[] = $v7;
				}
			}
			// print_r($s1);
			
			$index_len = strpos($s1[1], $boundary) - strpos($s1[1], "\x16");
			// echo 'index_len===>'.$index_len;
			
			$indexArr_t = explode($boundary, $s1[1]);
			$indexArr = [];
			// print_r($indexArr_t);
			for($i = 0; $i < sizeof($indexArr_t)-1; $i++){
				if($i==0){
					$indexArr[] = substr($indexArr_t[$i], strpos($indexArr_t[$i], "\x16"));
				} else {
					$indexArr[] = substr($indexArr_t[$i], strpos($indexArr_t[$i], "\x00\x16") + 1);
				}
			}
			// print_r($indexArr);

			// 图文集（第一项的头比较特殊，不过都可以使用“.*0x18”过滤）
			// echo (substr($f1[7], 0, 1));
			$s3 = explode($boundary, $s1[3]);
			// print_r($s3);
		
			// 新算法
			foreach ($s3 as $k => $v) {
				if($k==0){
					continue;
				}
				// echo '有效起始位置->'.strpos($v, "\x18", 4)."\r\n";
				// echo '有效字符串->'.substr($v, strpos($v, "\x18", 4)+1)."\r\n";
				// echo '$flag->['.substr($v, strpos($v, "\x18", 4)+1, 1)."]"."\r\n\r\n";
				
				// 去除 index
				$w = '';
				foreach ($indexArr as $k3 => $v3) {
					// echo $v3;
					// echo '$v==>'.$v.' - '.$v3;
					if(substr($v, 0, strlen($v3)) == $v3){
						$w = substr($v, strlen($v3) + 1);
					}
				}
				// echo $w.'^';
				$ret = $this->l_unpack($w);
				// print_r($ret);

				// if (strpos($v, '.jpg') != false)
				// $ret[0]['data'];
				// $ret[1]['data'];
				echo '<br>';
				echo 'index['.$k.']';
				// echo ' (('.$ret[2]['data'].'))<br>';

				// echo $ret[3]['data'];
				// echo $ret[4]['data'];

				$insertData = [
					'topic_id' => $v1['topic_id'],
					'a'		=> $v1['a'],
					'index'	=> $k,
					'audio' => $ret[4]['data'],
					'img'	=> $ret[3]['data']
				];

				if ($this->base_m->insertURI($insertData) > 0) {
					echo '<span style="color:green;">'.$v1['topic_id'].'===> '.$v1['a'].'</span><br>';
					$count_scuess++;
				} else {
					echo '<span style="color:red;">'.$v1['topic_id'].'===> '.$v1['a'].'</span><br>';
					$count_error++;
				}

				// 即时输出数据到浏览器
				ob_flush(); //将数据从php的buffer中释放出来
				flush(); //将释放出来的数据发送给浏览器
			}
		}

		echo 'success ====> '.$count_scuess;
		echo ' error ====> '.$count_error;
	}

	// 百词斩图片音频资源本地化
	public function localSource()
	{
		set_time_limit(0);

		$type = input('type');

		echo '<title>采集百词斩数据 Remember</title>';

		// 1、读取数据库记录
		$list = $this->base_m->listBczSourceURI($type);
		echo '<h1>count===>'.sizeof($list).'</h1>';

		// 2、抓取文件
		$count_scuess = $count_error = 0;
		foreach ($list as $k => $v) {
			// print_r($v);
			if($type=='audio'){
				$updateData = ['is_local_audio'=>1];
				$filename = $v['audio'];
			} else if($type=='img'){
			    $updateData = ['is_local_img'=>1];
			    $filename = $v['img'];
			}
			
			// TODO:XXXXX
			$new_uri =  dirname(dirname(dirname(dirname(__FILE__)))).'\\public\\static\\bcz'.$filename;

			// echo $v['raw_img'].'<br>';
			echo $uri = "http://ali.bczcdn.com".$filename;

			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $uri);
			curl_setopt($ch, CURLOPT_TIMEOUT, 10);
			// curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$data = curl_exec($ch);
			curl_close($ch);
			
			// 图片有效，并保存成功
			if(strpos($data, 'Moved') === false && strlen($data) > 100 ){
				$dir = str_replace("/", "\\", dirname($new_uri));
				if(!file_exists($dir)){
					echo $dir;
					mkdir($dir, null, true);
				}

				file_put_contents($new_uri, $data);
				
				echo '#'.$v['source_id'].'===>'.strlen($data).'<br>';

				// 3、标记图片已下载
				$this->base_m->updateSourceStatus(['source_id'=>$v['source_id']], $updateData);

				$count_scuess++;
			} else {
				echo '<b style="color:red;">error! '.$v['source_id'].'===>'.strlen($data).'===>'.$data.'</b><br>';

				$count_error++;
			}

			// 即时输出数据到浏览器
			ob_flush(); //将数据从php的buffer中释放出来
			flush(); //将释放出来的数据发送给浏览器
		}

		echo 'success ====> '.$count_scuess;
		echo 'error ====> '.$count_error;
	}
}