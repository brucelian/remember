<?php
namespace app\remember\controller;

use app\remember\model\EnglishModel;

class English extends Base
{
	protected $base_m;
	// protected $appNickName = '素材库';
	// protected $appName = 'fms';
	// protected $appId = 5;
    protected $circleId = 6;		// 英语

	public function __construct(){
		parent::__construct();

		$this->base_m = new EnglishModel();
	}

    // 测试百词斩无效解析
    public function testBCZAnalysis()
    {
        set_time_limit(0);

        $listNoB = $this->base_m->listHaveBczBlob(0);
        echo '<h1>count===>'.sizeof($listNoB).'</h1>';

        foreach ($listNoB as $k => $v) {
            echo $v['id'].' ';

            $Baicizhan = new \Baicizhan($v['bcz']);

            // echo '<pre>';
            // print_r($d);
            // echo '</pre>';
            
            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            // echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }

    // （临时）去除解释中的人名解释
    public function removeNameDesctipt()
    {
        $d = $this->base_m->listNameDesctipt();
        // print_r($d);
        foreach ($d as $key => $v) {
            // print_r($v);

            preg_match_all("/n(.*?)人名(.*?)\r\n/", $v['b'], $matches);

            if($matches[0]){
                if ($key < 40){
                    
                    // echo '<br>';
                    
                    // echo $v['b'];
                    // echo '<br>';
                    $new = str_replace($matches[0][0], '', $v['b']);
                    $updateData = ['b'=>$new];
                    // echo $matches[0][0];
                    // print_r();
                    echo $this->base_m->updateData(['id'=>$v['id']], $updateData)?'1':'0';
                    // echo ' ';
                    // echo $v['id'];
                    // echo ' ';
                    // echo $new;
                    // // echo '<br>';
                    // echo '<br>';
                } else {
                    echo '<br>';
                    echo $v['id'];
                    
                    echo $matches[0][0];
                    
                }
            }
        }
        // return json($data);
    }

    // 远程音频转为本地
    // TODO:使用 Java 写多线程
    public function localAudio()
    {
        set_time_limit(0);

        // 1、读取数据库记录
        $list = $this->base_m->listNoLocalAudioEnglish();
        echo '<h1>count===>'.sizeof($list).'</h1>';

        // 2、抓取图片
        foreach ($list as $k => $v) {
            echo 'ok '.$v['id'].'===>';
            
            // TODO:XXXXX
            $file_name = str_replace('?','',$v['a']).'.mp3';
            $new_uri =  dirname(dirname(dirname(dirname(__FILE__)))).'/public/static/mp3/English/'.$file_name;

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $v['audio_addresses']);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            // curl_setopt($ch, CURLOPT_HEADER, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $data = curl_exec($ch);
            curl_close($ch);

            // if(strlen($data) < 400 && $v['audio_addresses2']){
            //     $data = file_get_contents($v['audio_addresses2']);
            // }

            if(strlen($data) < 400 && $v['us_audio']){
                $data = file_get_contents($v['us_audio']);
            }
            
            // 图片有效，并保存成功
            // if(strpos($data, 'HTTP') === false && strpos($data, '<!doctype') === false && strpos($data, 'Moved') === false && strlen($data) > 100 ){
            if(strlen($data) > 400 ){
                echo strlen($data).'<br>';
                
                file_put_contents($new_uri, $data);

                // 3、标记图片已下载
                $updateData = array(
                    'local_audio' => $file_name,
                );

                $this->base_m->updateData(['id'=>$v['id']], $updateData);
            } else {
                echo '<b style="color:red;">error! '.$v['id'].'===>'.strlen($data).'===></b><br>';
            }

            if(strlen($data) == 169){
                $this->base_m->updateData(['id'=>$v['id']], ['local_audio'=>'']);
            }

            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器
        }
    }

    // 自动补充有道释义
    public function autoPatchYoudao()
    {
        set_time_limit(0);

        // 为了快捷删除解释中的人名   
        // 为了给扇贝/百词斩单词添加有道解释
        $listNoB = $this->base_m->listShanbeiBCZEnglish();
        echo '<h1>count===>'.sizeof($listNoB).'</h1>';

        foreach ($listNoB as $k => $v) {
            echo $v['id'];

            $xml = simplexml_load_file('http://dict.youdao.com/fsearch?xmlVersion=3.3&q='.$v['a'], null, LIBXML_NOCDATA);
            $d = json_decode(json_encode($xml),true);
            // echo '<pre>';
            // print_r($d);
            // echo '</pre>';
            
            $where = ['id'=>$v['id']];
            if(isset($d['us-phonetic-symbol']) && $d['us-phonetic-symbol']){
                $updateData['c'] = $d['us-phonetic-symbol'];
            } 

            if (isset($d['custom-translation'])) {

                $content = '';
                if (sizeof($d['custom-translation']['translation']) > 1) {
                    foreach($d['custom-translation']['translation'] as $k2 => $v2){
                        if(!is_array($v2['content'])){
                            $content = $content.$v2['content']."\r\n";
                        }
                    }
                } else {
                    $content = $d['custom-translation']['translation']['content'];
                }

                $updateData['b'] = $content;
            }

            print_r($updateData);
            echo '===>'.$this->base_m->updateData($where, $updateData);



            // 即时输出数据到浏览器
            ob_flush(); //将数据从php的buffer中释放出来
            flush(); //将释放出来的数据发送给浏览器

            // if (isset($d['custom-translation'])) {

            //     $content = '';
            //     if (sizeof($d['custom-translation']['translation']) > 1) {
            //         foreach($d['custom-translation']['translation'] as $k2 => $v2){
            //             if(!is_array($v2['content'])){
            //                 $content = $content.$v2['content']."\r\n";
            //             }
            //         }
            //     } else {
            //         $content = $d['custom-translation']['translation']['content'];
            //     }

            //     $where = ['id'=>$v['id']];
            //     $updateData = [
            //         'c' => $d['us-phonetic-symbol'],
            //         'b' => $content
            //     ];

            //     echo '===>'.$this->base_m->updateData($where, $updateData);


            //     // 即时输出数据到浏览器
            //     ob_flush(); //将数据从php的buffer中释放出来
            //     flush(); //将释放出来的数据发送给浏览器
            // }
            echo '<br>';
        }
        echo '<br>================ finish! ======================';
    }






    // k.k. 音标发音
    public function KK()
    {
        $this->assign('height_symbol', $this->base_m->listHeightKK());
        $this->assign('symbol', $this->base_m->listKK());
        return $this->fetch('/kk');
    }

    // FT 双语阅读 列表页
    public function FT()
    {
        $listData = $this->base_m->listFT(30);
        $this->assign('listData', $listData);
        $this->assign('paginate', $listData->render());

        return $this->fetch('ft_list');
    }

    // FT 双语阅读 详情页
    public function FT_detail()
    {
        $id = input('id');

        $data = $this->base_m->getFT($id);
        $this->assign('detailata', $data);

        return $this->fetch('FT_detail');
    }

    // 修改 FT 双语阅读 备注
    public function updateFTComment()
    {
        $id = input('id');
        $description = input('description');

        return $this->base_m->updateFTComment(['id'=>$id], ['comment' => $description]);
    }

    // FT 双语阅读 打卡
    public function FTclockIn()
    {
        $id = input('id');
        $duration = input('duration');

        return $this->base_m->FTclockIn($id, [
            'article_id'=>$id,
            'duration'=>$duration,
            'type'=>'FT'
        ]);
    }

    /*
     * 切页版
     */

    // 随机练习
    // TODO：应该智能选择版本
    public function randLearn()
    {
        $this->assign('all_words', []);
        $this->assign('tag_ids', input('tag_ids'));
        $this->assign('bookId', null);

        $is_m = input('m');
        if ($is_m){
            return $this->fetch('/shuffle_m');
        } else {
            return $this->fetch('/shuffle');
        }
    }

    // 随机练习（句子、图片、音频）
    public function randLearn2()
    {
        $this->assign('all_words', []);
        $this->assign('tag_ids', input('tag_ids'));
        $this->assign('bookId', null);

        $is_m = input('m');
        if ($is_m){
            return $this->fetch('/shuffle_mxxx');
        } else {
            return $this->fetch('/shuffle2');
        }
    }

    // 百词斩 Plus
    public function BCZplush()
    {
        $this->assign('all_words', []);
        $this->assign('tag_ids', input('tag_ids'));
        $this->assign('bookId', null);
        $this->assign('fake_list', $this->base_m->listBczSource('img'));

        $is_m = input('m');
        if ($is_m){
            // return $this->fetch('/shuffle_mxxx');
        } else {
            return $this->fetch('/shuffle_bcz');
        }
    }

    // 顺序练习
    public function orderLearn()
    {
        $this->assign('all_words', []);
        $this->assign('bookId', input('bookId'));
        $this->assign('tag_ids', null);

        $is_m = input('m');
        if ($is_m){
            return $this->fetch('/shuffle_m');
        } else {
            return $this->fetch('/shuffle');
        }
    }


    // 待斩列表页
    public function zhan()
    {
        $list_data = $this->base_m->listNoZhan(50);

        $this->assign('title', '斩！');
        $this->assign('list_data', $list_data);
        $this->assign('paginate', $list_data->render());

        return $this->fetch('list');
    }


    // 已斩列表页
    public function zhaned()
    {
        $list_data = $this->base_m->listZhaned(50);
        $list_data2 = $this->pushTags($list_data);

        $this->assign('title', '已斩！');
        $this->assign('list_data', $list_data2);
        $this->assign('paginate', $list_data->render());

        return $this->fetch('list_zhan');
    }


    // 脏数据处理页
    public function advance()
    {
        // 为了快捷删除解释中的人名   
        // 为了给扇贝/百词斩单词添加有道解释
        $this->assign('listDirtyEnglish', array_merge($this->base_m->listShanbeiBCZEnglish(), $this->base_m->listDirtyEnglish(999100)));

        return $this->fetch('advance');
    }

	// 详情页
    public function detail()
    {
        $id = input('id');
        $kw = trim(input('kw'));

        if ($id) {
            $detailata = $this->base_m->getObjectById($id);
        } else if($kw){
            $detailata = $this->base_m->searchByKeywordPre($kw);
        }


    	if($detailata){

	    	$this->assign('detailata', $detailata);
	    	// $this->assign('appId', $this->appId);
	    	// $this->assign('circleId', $this->circleId);
	    	// $this->assign('readCount', $this->base_m->getReadCount($id));
	    	$this->assign('quicktag', $this->tag_m->listTagsByCircle($this->circleId));
    		$this->assign('tags', $this->tag_m->listObjTags(6, $detailata['id']));
    		// $this->assign('relate', $this->base_m->listRelate($id));
    		
            $is_m = input('m');
            if ($is_m){
                return $this->fetch('detail_m');
            } else {
        		return $this->fetch('detail');
            }
    	}else{
    		echo '<h1 style="color:red;">';
    		echo '无此资源！';
    		echo '</h1>';
    		return;
    	}
    }

    // “定制单词簿”排序页
    public function orderManage()
    {
        $bookId = input('bookId');

        $listData = $this->base_m->listBookWord($bookId);
        $this->assign('title', '定制单词簿['.$bookId.']排序');
        $this->assign('bookId', $bookId);
        $this->assign('hottags', $listData);

        return $this->fetch('/orderManage');
    }    


    // 获取百词斩 fake 候选项图片
    public function listFackOptionJS()
    {
        header("Content-type: text/javascript");
        $interval = 3600*24*30; // 一个月
        if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
            // HTTP_IF_MODIFIED_SINCE即下面的: Last-Modified,文档缓存时间.
            // 缓存时间+时长.
            $c_time = strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE'])+$interval;
            // 当大于当前时间时, 表示还在缓存中... 释放304
            if($c_time > time()){
                header('HTTP/1.1 304 Not Modified');
                exit();
            }
        }
        header("Expires: " . gmdate("D, d M Y H:i:s",time()+$interval)." GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header('Cache-Control:max-age='.$interval);


        $list = $this->base_m->listBczSource('img');
        echo 'var fake_list = [';
        foreach ($list as $k => $v) {
            echo "'".$v['img']."',";
        }
        echo ']';

        exit();
    }

    // 随机获取 N 个单词短语（加入艾宾浩斯、错题）
    public function listRand()
    {
        // $d = $this->base_m->todayStatus(3792);
        // print_r($d);

        $limit = 100;
        $tag_ids = input('tag_ids/a');
        $no_tag_ids = input('no_tag_ids/a');
        $is_nulltag = 1;

        // $mistakes = $this->base_m->listMistakes();

        $data = $this->base_m->listRand($limit, $tag_ids, $no_tag_ids, $is_nulltag);

        return json($data);
    }

    // 获取艾宾浩斯复习题
    public function listEbbinghaus()
    {
        $data = $this->base_m->listEbbinghaus();
        return json($data);
    }

    // 获取某“定制单词簿”
    public function listBookWord()
    {
        $bookId = input('bookId');

        $data = $this->base_m->listBookWord($bookId);

        return json($data);
    }

    // 根据大数据重新评估权重
    public function scoreByLog()
    {
        /*
         * 简单级（v=0）
         */
        // 每次都正确（至少 5 次），反应都没超时，且最后一次距离第一次有 1 个月
        $where = [];
        echo $this->base_m->updateData($where, $updateData);

        // 当天第一次就“双过”，则当天不在提示
    }


    /* 一次性更改所有 raw 标签排序
     * par [Array]
     * return [JSON]0
     */
    public function sortAllTag()
    {
        $bookId = input('bookId');
        $data = input('data/a');

        $j = 0;
        foreach ($data as $k => $v){
            $updateData = array(
                'listorder' => $v['listorder']
            );

            $whereArr = [
                'obj_id' => $v['tag_id'],
                'book_id' => $bookId
            ];

            $j += $this->base_m->updateBookWordOrder($whereArr, $updateData);
        }
        echo '（'.$j.'）ok!';
    }

    // 修改熟悉度（带 Log）
    public function updateV()
    {
        $id = input('id');
        $v = input('v');

        
        $insertData = [
            'obj_id'    => $id,
            'v'    => $v,
        ];

        // 更新 v
        $f = $this->base_m->updateData(['id'=>$id], ['v' => $v]);

        // 插入 Log
        if($f){
            $this->base_m->insertLearnLog($insertData);
        }
        
        return $f;
    }

    // 标记学习情况
    public function flagEnglish()
    {
        $id = input('id');
        $state = input('state');
        $raw_v = input('raw_v');
        $duration = input('duration');
        $mistake = input('mistake');

        $insertData = [
            'obj_id'    => $id,
            'state'    => $state,
            'duration'    => $duration
        ];

        // 如果答对
        if ($state==0) {

            // 如果今天第一次就答对
            if(!$this->base_m->todayStatus($id)) {
                // 艾宾浩斯周期 +1
                $this->base_m->upgradeEbbinghaus($id);

                // 抽奖概率 v-1
                $f = $this->base_m->updateData(['id'=> $id],
                    [
                        'blockdate'=>date('Y-m-d'),
                        'v'=> db()->raw('v-1')
                    ]
                );

                // 如果 v == 5，则重新设置艾宾浩斯周期 = 3
                $obj = $this->base_m->getObjectById($id);
                if($obj['v'] == 5) {
                    $this->base_m->updateData(['id'=> $id], ['ebbinghaus' => 3]);
                }
            }

        // 如果答错（排除“待学”）
        } else if($state != 9){
            $this->base_m->updateData(['id'=> $id],
                [
                    'blockdate'=>date('Y-m-d'),
                    'v'=> db()->raw('v+1')
                ]
            );

            $insertData['v'] = $raw_v + 1;

            // 记录错误选项
            if($mistake){
                $insertMistakeData = [
                    'obj_id' => $id,
                    'option' => $mistake
                ];
                $this->base_m->insertMistakeLog($insertMistakeData);
            }
        }
        // echo 'Log:';

        return $this->base_m->insertLearnLog($insertData);
    }

    // 撤销更改标记学习情况
    public function changeFlagEnglish()
    {
        $logId = input('log_id');
        $objId = input('obj_id');
        
        $operation = input('operation');

        if($operation == 'isNoRight') {
            $this->base_m->updateData([
                'id'=>$objId], ['blockdate'=>null,
                'v'=> db()->raw('v+1')
            ]);
            $this->base_m->updateLearnLog($objId, 5);
        }

        return $this->base_m->cancelLearnLog($logId);
    }
}
