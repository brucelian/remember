<?php
namespace app\remember\controller;

use think\Controller;
use app\remember\model\EnglishModel;

class Shanbei extends Controller
{
	private $base_m;
	private $cookie_null = 'csrftoken=KZ6J0URp6Mw6hO0gE9cNn5GshsK1grrg; _ga=GA1.2.1449764861.1548149257; Hm_lvt_bfc6c23974fbad0bbfed25f88a973fb0=1548338557,1548338613,1548655304; __utmz=183787513.1552046688.17.5.utmcsr=localhost:2018|utmccn=(referral)|utmcmd=referral|utmcct=/BL/public/los/knowledge/detail; userid=213844873; __utma=183787513.1449764861.1548149257.1554196434.1556374747.22; __utmc=183787513; auth_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImJydWNlbGlhbiIsImRldmljZSI6MCwiaXNfc3RhZmYiOmZhbHNlLCJpZCI6MjEzODQ0ODczLCJleHAiOjE1NTcyNDA2OTJ9.5UTzrfD7nvbURu0u_2d0lZ_CSmkwywb6IaQpzRRlHyc; __utmb=183787513.36.10.1556374747';
    
    private $cookie = '_ga=GA1.2.1449764861.1548149257; Hm_lvt_bfc6c23974fbad0bbfed25f88a973fb0=1548338557,1548338613,1548655304; csrftoken=oclkKPgv6ZIonPJYWoryibkYcrFdvGSs; userid=217314892; sessionid=".eJzNVFuTmjAY_S8873pBXNE3RKu71XpZWV3aDhNChHAJSAICnf3v_aLtjp1pO33sC5fk5MzJd77vfFPOBAdIOFwgQZSRUlh1vZk-5btuvbXtbOmr1U5v-sNdOH8orV2l3Ck8xRTFDipE4MSICydOfcocF-GIMA84rpSAzFAuJDSjGYkpA_5vSnRGuc_lV0ElOJ009WuYfTLMVbkVQefVHA5zzetZL7spUFDuMHJWRkcUc3Kn5IRnKeMXpixPS8qwVP0cIOYHiMKBNCPsSjw-aFuungaPmuHU99GZLXkzWXFj9dECnA9aSa6M1DslIMijiV_kMRwLhMhG7bYIaO6dq9YJLpe2MGsniWRul9TpqW3qouHR7lK8tDzybAZh5r_u82gWcK57RndS9320NHhFEcXxImtyirKpps5mpn3aTKbWrLaCo5gbkzLqJCeriaCizxQ9YorhIu1uTwWF_1SfGPAF8mURCIN_TEUN3_MC1rMCFhDGhHNHpGAObKhdZ7a24qeAaMVLj9ruQIybD-N9_zFGB_ox2bgfqO0ki7Cvz43h_QyvtjHtLdarYK1PkhgFsZ-GVRjPqbtojDTchgMX3Z-mvhTMaMr-RTSpMgpOOhQUDdROB3qKVBcrUIkEyv8rG3BaMJHLopqfZE_ntKQxkRX__BWEY9ADe5xxlNHrKACKURwxlMidL4Wued6Xot87PsDT7XcU2cdHKEBwawve7vdZxOiy2Dm2UaX48KSFuTvUeLmxzax07Ze9uds2WjQ-mDUZb1ZNRV_UjcPVlDHtEBkiDbAp1vdjb3DwEn-xHqw3Ussfdbzd_ZzVkcgLGC-PCETjy3TezMffLvLfGfb2HlAFl_ohJyJlpOv6cPCg9sFOUUvD1KEOyN9kFqSNg_iFQkA7vyfPLZvaHfS6mj5U3-l6b79nu2adbBRGKiizBrBrckq-m8y8CdXWj1Xewil0HnVbV7rW_vIa_zjzC5GcundVb98BNhj4aw:1hLmFO:MB7Eb8uvYoBzQOzXD7mOjvyx-tc"; __utmc=183787513; __utmz=183787513.1556705632.33.6.utmcsr=open.weixin.qq.com|utmccn=(referral)|utmcmd=referral|utmcct=/connect/qrconnect; language_code=zh-CN; sajssdk_2015_cross_new_user=1; sensorsdata2015jssdkcross=%7B%22distinct_id%22%3A%22217314892%22%2C%22%24device_id%22%3A%2216a72ebf97735-091d1bf38fd9b8-36664c08-2073600-16a72ebf97869%22%2C%22props%22%3A%7B%22%24latest_utm_source%22%3A%22web_codetime%22%2C%22%24latest_utm_medium%22%3A%22shanbay_nav%22%7D%2C%22first_id%22%3A%2216a72ebf97735-091d1bf38fd9b8-36664c08-2073600-16a72ebf97869%22%7D; __utma=183787513.1449764861.1548149257.1556705632.1556708641.34; __utmt=1; _gat=1; auth_token=eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6IldlY2hhdF9iYTY3MDBkNzJhYmNmZWIyIiwiZGV2aWNlIjowLCJpc19zdGFmZiI6ZmFsc2UsImlkIjoyMTczMTQ4OTIsImV4cCI6MTU1NzU3MjgyOH0.Wm6OPmPaRuzKxlIX-cxlRpFmsaocm5CrvXScHmk3TIU; __utmb=183787513.11.10.1556708641';   // 小号

	public function __construct(){
		parent::__construct();
		
		$this->base_m = new EnglishModel();
	}




    // 临时 精简 JSON
    public function minJson()
    {
        set_time_limit(0);

        echo '<title>压缩JSON</title>';

        $list = $this->base_m->listBigJson(0);
        echo '<h1>count===>'.sizeof($list).'</h1>';

        foreach ($list as $k => $v) {
            echo $k.'. '.$v['a'];
            // print_r($v);

            $data = json_decode($v['sb_collins'], true);
            // $data = json_decode($v['sb_roots'], true);

            $definitions = $data['data'][0]['definitions'];
            // $rootArr = reset($data['data'])['roots']['roots'];
            
            // echo '<pre>';
            // print_r($definitions);
            // echo json_encode($definitions);
            // echo '</pre>';

            $this->base_m->updateEnglishDetailData(['id'=>$v['id']], ['sb_collins'=>json_encode($definitions)]);
            // $this->base_m->updateEnglishDetailData(['id'=>$v['id']], ['sb_roots'=>json_encode($rootArr)]);
            echo '<br>';

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }







	public function mylearn()
	{
		echo '<title>将扇贝单词入库 Remember</title>';

		$page = 1;
		$total = 0;
		$data = array('list'=>[]);
		do{
			$url = 'https://www.shanbay.com/api/v1/bdc/library/familiar/?ipp=50&page='.$page;
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'x-requested-with: XMLHttpRequest',
				'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36'
			));
			curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			echo $t = curl_exec($ch);
			$total = json_decode($t, true)['data']['total'];

			$data['list'] = array_merge($data['list'], json_decode($t, true)['data']['objects']);
			curl_close($ch);

			$page++;
		} while ($page <= ($total/50+1));


		print_r($data);
		// $l=0;
		// foreach ($data['list'] as $k => $v) {

		// 	$insertData = [
		// 		'md5' => $v['conent_id'],
		// 		'a' => $v['content'],
		// 		'b2' => $v['definition'],
		// 		'c' => $v['pronunciation'],
		// 		'us_audio' => $v['us_audio'],
		// 		'audio_addresses' => $v['audio_addresses']['us'][0],
		// 		'audio_addresses2' => $v['audio_addresses']['us'][1],
		// 		'en_definition' => (isset($v['en_definition']['pos']) ? $v['en_definition']['pos'].' '.$v['en_definition']['defn'] : ''),
		// 		'type' => $v['content_type'] == 'vocabulary' ? '英语单词' : $v['content_type'],
		// 		// 'v' => $v['num_sense'],
		// 		'source' => 'shanbei'
		// 	];

		// 	if ($this->base_m->saveEnglish($insertData) > 0) {
		// 		$l++;
		// 		echo '<span style="color:green;">'.$k.'===>'.$v['id'].' '.$v['content'].'</span><br>';
		// 	} else {
		// 		echo '<span style="color:red;">'.$k.'===>'.$v['id'].' '.$v['content'].'</span><br>';
		// 	}
			

		// }
		// echo 'total add ====> '.$l;

		// $this->assign('json', json_encode($data));

		// return $this->fetch('/douyin/douyin_work');
	}

	// 添加单词到扇贝“单词库”
	public function addLearn()
	{
		set_time_limit(0);

		echo '<title>添加单词</title>';

		$list = $this->base_m->listNoShanbeiIds(0);
		echo '<h1>count===>'.sizeof($list).'</h1>';

		// print_r($list);

		$buffer = [];
		foreach ($list as $k => $v) {

			echo $k.'. '.$v['a'];

			$buffer[] = $v['a'];

            if(sizeof($buffer) == 10) {
            	$url = 'https://www.shanbay.com/bdc/vocabulary/add/batch/?words='.implode('%0A', $buffer);

            	echo '<br>'.$url;

            	$ch = curl_init();
            	curl_setopt($ch, CURLOPT_URL, $url);
            	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
					'Content-Type: application/json',
					'x-requested-with: XMLHttpRequest',
					'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36'
				));
            	curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            	$jsonStr = curl_exec($ch);
            	// $data = json_decode($jsonStr, true);
            	curl_close($ch);
            	
            	// echo '<pre>';
            	// print_r($jsonStr);
            	// echo '</pre>';

            	$buffer = [];
            }

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }

        echo '<br>================ finish! ======================';
	}

	// 清空扇贝“单词库”（到太简单）
	public function clearLearn()
	{
		set_time_limit(0);

		echo '<title>清空扇贝单词库</title>';

		$list = $this->base_m->listShanbeiIds(5000);
		echo '<h1>count===>'.sizeof($list).'</h1>';

		// print_r($list);

		foreach ($list as $k => $v) {

        	echo $k.'. '.$v['a'];

        	$url = 'https://www.shanbay.com/api/v1/bdc/learning/'.$v['ids'].'/';

        	echo '<br>'.$url;

        	$ch = curl_init();
        	curl_setopt($ch, CURLOPT_URL, $url);
        	curl_setopt($ch, CURLOPT_TIMEOUT, 15);
        	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
        	curl_setopt($ch, CURLOPT_POSTFIELDS, '{"pass":1, "force":1}');
        	curl_setopt($ch, CURLOPT_HTTPHEADER, array(
				'Content-Type: application/json',
				'x-requested-with: XMLHttpRequest',
				'user-agent: Mozilla/5.0 (Linux; Android 6.0; Nexus 5 Build/MRA58N) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/69.0.3497.100 Mobile Safari/537.36'
			));
        	curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        	// curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        	$jsonStr = curl_exec($ch);
        	// $data = json_decode($jsonStr, true);
        	curl_close($ch);
        	
        	// echo '<pre>';
        	// print_r($jsonStr);
        	// echo '</pre>';

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }

        echo '<br>================ finish! ======================';
	}

	// 采集 web 扇贝单词列表信息（ids）
    public function patchShanbeiIds()
    {
        set_time_limit(0);

        echo '<title>采集网页版扇贝单词列表信息（ids）</title>';

        $pn = input('pn');
        $library = input('library');

        echo '<h1>'.$library.'</h1>';

        for($i=1; $i <= $pn; $i++) {
            echo '【'.$i.'】<br>';

            $ch = curl_init();
            $url = 'https://www.shanbay.com/api/v1/bdc/library/'.$library.'/?page='.$i;
            curl_setopt($ch, CURLOPT_URL, $url);
            // curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/bdc/library/fresh/?page='.$i);	// 新的单词
            // curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/bdc/library/familiar/?page='.$i);	// 正在学习
            // curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/bdc/library/master/?page='.$i);	// 掌握单词
            curl_setopt($ch, CURLOPT_TIMEOUT, 3);
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $raw_data = curl_exec($ch);
            $data = json_decode($raw_data);
            curl_close($ch);
            
            // echo '<pre>';
            // print_r($data);
            // echo '</pre>';

            if($data->status_code==0){
                foreach (json_decode(json_encode($data->data->objects), true) as $k => $v) {
                    echo $v['id'].' '.$v['content'].' ';
                    if ((is_numeric($v['learning_id']))&&(strpos($v['learning_id'],'.')))
                    {
                        echo $v['learning_id'] = rtrim(rtrim(number_format($v['learning_id'],12,',',''),'0'),',').' ';
                    }

                    $this->base_m->saveShanbeiIds([
                        'ids'    => $v['learning_id'],
                        'a'=> $v['content'],
                    ]);
                    echo '<br>';
                }
            }

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }

    // 采集扇贝单词 conten_id
    public function patchContentId()
    {
        set_time_limit(0);

        echo '<title>采集扇贝单词 content_id</title>';

        $list = $this->base_m->listNoContentId();
        echo '<h1>count===>'.sizeof($list).'</h1>';

        foreach ($list as $k => $v) {
            echo $k.'. ';
            
            // $data = file_get_contents('https://api.shanbay.com/bdc/search/?word='.$v['a']);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/bdc/learning/?basic=0&ids='.$v['ids']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $jsonStr = curl_exec($ch);
            $jsonArr = json_decode($jsonStr, true);
            curl_close($ch);

            // print_r($jsonArr);
            if ($jsonArr['status_code']==0){
                echo $content_id = $jsonArr['data'][0]['content_id'];
                // echo $v['ids'];
                echo ' --> '.$this->base_m->updateShanbeiIds(['ids' => $v['ids']], ['content_id' => $content_id]);
            }
            echo '<br><br>';

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器
        }
        echo '================ finish! ======================';
    }

    // 采集扇贝付费内容： roots
    public function patchShanbeiPayContent()
    {
        set_time_limit(0);

        echo '<title>采集扇贝付费内容： roots </title>';

        $listNoPC = $this->base_m->listNoPayContentEnglish('is_local_root');
        echo '<h1>count===>'.sizeof($listNoPC).'</h1>';

        foreach ($listNoPC as $k => $v) {
            echo $k.'. '.$v['a'];
            // print_r($v);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/market/applet/?ids='.$v['ids']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $jsonStr = curl_exec($ch);
            $data = json_decode($jsonStr, true);
            curl_close($ch);
            
            // echo '<pre>';
            // // print_r($data);
            // echo '</pre>';

            if($data['status_code']==0){
                echo ' ----> '.$ret = $this->base_m->saveShanbeiPC(['id'=>$v['id']], ['sb_roots'=> reset($data['data'])['roots']['roots']]);
                if($ret > 0){
                    $this->base_m->updateShanbeiIds(['ids'=>$v['ids']], ['is_local_root'=>1]);
                }
            } else {
            	echo '<br>error!<br>';
            	// 标记采集数据异常
            	$this->base_m->updateShanbeiIds(['ids'=>$v['ids']], ['is_local_root'=>-1]);
            }
            echo '<br>';

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }

    // 采集扇贝付费内容（派生联想）
    public function patchShanbeiPayContent2()
    {
        set_time_limit(0);

        echo '<title>采集扇贝付费内容： 派生联想 </title>';

        $listNoPC = $this->base_m->listNoPayContentEnglish('is_local_paycontent');
        echo '<h1>count===>'.sizeof($listNoPC).'</h1>';

        foreach ($listNoPC as $k => $v) {
            echo $k.'. '.$v['a'];
            // print_r($v);

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/market/applet/?ids='.$v['ids']);
            // curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/bdc/vocabulary/?version=2&ids='.$v['content_id']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $jsonStr = curl_exec($ch);
            $data = json_decode($jsonStr, true);
            curl_close($ch);
            
            // echo '<pre>';
            // print_r(reset($data['data'])['affixes']['affix']);
            // echo '</pre>';

            if($data['status_code']==0){
                echo ' ----> '.$ret = $this->base_m->saveShanbeiPC(['id'=>$v['id']], ['sb_affix'=> json_encode(reset($data['data'])['affixes']['affix'])]);
                if($ret > 0){
                    $this->base_m->updateShanbeiIds(['ids'=>$v['ids']], ['is_local_paycontent'=>1]);
                }
            } else {
                echo '<br>error!<br>';
                // 标记采集数据异常
                $this->base_m->updateShanbeiIds(['ids'=>$v['ids']], ['is_local_paycontent'=>-1]);
            }
            echo '<br>';

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }

    // 采集扇贝付费内容（柯林斯）
    public function patchShanbeiPayContent3()
    {
        set_time_limit(0);

        echo '<title>采集扇贝付费内容： 柯林斯 </title>';

        $listNoPC = $this->base_m->listNoPayContentEnglish('is_local_collins');
        echo '<h1>count===>'.sizeof($listNoPC).'</h1>';

        foreach ($listNoPC as $k => $v) {
            echo $k.'. '.$v['a'];
            // print_r($v);

            $ch = curl_init();
            // curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/market/applet/?ids='.$v['ids']);
            curl_setopt($ch, CURLOPT_URL, 'https://www.shanbay.com/api/v1/bdc/vocabulary/?version=2&ids='.$v['content_id']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 15);
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $jsonStr = curl_exec($ch);
            $data = json_decode($jsonStr, true);
            curl_close($ch);
            
            // echo '<pre>';
            // print_r(reset($data['data']));
            // echo '</pre>';

            if($data['status_code']==0){
                echo ' ----> '.$ret = $this->base_m->saveShanbeiPC(['id'=>$v['id']], ['sb_collins'=> $data['data'][0]['definitions']]);
                if($ret > 0){
                    $this->base_m->updateShanbeiIds(['ids'=>$v['ids']], ['is_local_collins'=>1]);
                }
            } else {
                echo '<br>error!<br>';
                // 标记采集数据异常
                $this->base_m->updateShanbeiIds(['ids'=>$v['ids']], ['is_local_collins'=>-1]);
            }
            echo '<br>';

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }


    // 采集音频URI（扇贝接口）
    public function patchAudio()
    {
        set_time_limit(0);
        $list = $this->base_m->listNoAudioEnglish();
        echo '<h1>count===>'.sizeof($list).'</h1>';

        foreach ($list as $k => $v) {
            if ($k<800){
                echo $k.'. ';
                $data = file_get_contents('https://api.shanbay.com/bdc/search/?word='.$v['a']);
                $jsonArr = json_decode($data, true);
                // print_r($jsonArr);
                if ($jsonArr['status_code']==0){
                    echo $us_audio = $jsonArr['data']['us_audio'];
                    echo '<br>';
                    echo $audio_addresses = $jsonArr['data']['audio_addresses']['us'][0];
                    echo '<br>';
                    echo $audio_addresses2 = $jsonArr['data']['audio_addresses']['us'][1];
                    echo '<br>';
                    $en_definition = $jsonArr['data']['en_definition'];
                    
                    echo $this->base_m->updateData(['id'=>$v['id']], [
                        'us_audio'=> $us_audio,
                        'audio_addresses'=> $audio_addresses,
                        'audio_addresses2'=> $audio_addresses2,
                        'en_definition'=> ($en_definition ? ($en_definition['pos'].' '.$en_definition['defn']) : null)
                    ]);
                }
                echo '<br><br>';

                // 即时输出数据到浏览器
                ob_flush(); //将数据从php的buffer中释放出来
                flush(); //将释放出来的数据发送给浏览器
            }
        }
        echo '================ finish! ======================';
    }
}
