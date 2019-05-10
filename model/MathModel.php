<?php
namespace app\remember\model;

use think\Model;

class MathModel extends RememberModel
{
	// protected $appId = 5;

    public function __construct(){
        parent::__construct();
    }


    // 获取最近 N 日学习进度
    public function listLearLog($id)
    {
        return db('remember')
            ->join('remember_math_log log', 'remember.id=log.remember_id', 'left')
            ->where('remember_id', $id)
            ->where('type', '数学试题')
            ->order('log_id')
            ->select();
    }

    // 插入做题记录
    public function insertLearnLog($insertData)
    {
        return db('remember_math_log')
            ->insertGetId($insertData);
    }

    // 更改操作 Log
    public function updateLearnLog($id, $state){
        return db('remember_log')
            ->where('log_id', $id)
            ->update(['state'=>$state]);
    }

   





























    

    

    // 获取一个对象
    // Overwrite
    public function getObjectById($id)
    {
        return db('remember_english')
            ->join('remember', 'remember.topic_id=remember_english.topic_id', 'right')
            ->where('remember.id', $id)
            ->find();
    }
    
    // 获取还未斩的单词列表
    public function listNoZhan($page_size){

        return db('remember')
            // ->field('\'los\' as source, dtb.instance_id, los.obj_id,title,thumb, raw_img, comment,shixiao,zhongyao,shouyi,type,inputtime,\'\' as json')
            // ->join('v_sources_tags B', 'remember.id=B.obj_id AND B.app_id='.config('sys_info')['remember']['id'],   'left')     // TODO 
            // ->where(
            //     $tag_ids ? (
            //         $is_nulltag ? "(tag_ids regexp '_".implode('_|_', $tag_ids)."_' OR tag_ids IS NULL)" : "(tag_ids regexp '_".implode('_|_', $tag_ids)."_')"
            //     ) : ''
            // )
            // ->where(
            //     $no_tag_ids ? (
            //         $is_nulltag ? "(tag_ids not regexp '_".implode('_|_', $no_tag_ids)."_' OR tag_ids IS NULL)" : "(tag_ids not regexp '_".implode('_|_', $tag_ids)."_')"
            //     ) : ''
            // )
            ->where('v', '>', 5)
            ->where('type', '英语单词')
            ->order('id DESC')
            ->paginate($page_size);
    }

    // 获取已斩的单词列表
    public function listZhaned($page_size){

        return db('remember')
            ->join('v_sources_tags B', 'remember.id=B.obj_id AND B.app_id='.config('sys_info')['remember']['id'],   'left')     // TODO 
            ->where('v', 0)
            ->where('type', '英语单词')
            ->order('description DESC, id DESC')
            ->paginate($page_size);
    }

    // 获取“英语单词”列表
    public function listEnglish($limit=0)
    {
        $ret = db('remember')
            ->where('type','英语单词')
            ->limit($limit)
            ->order('id DESC')
            ->select();

        return $this->pushTags($ret);
    }

    public function listKK($limit=0)
    {
        $ret = db('remember_kk')
            // ->where('type','英语单词')
            // ->where('a','not like', '$%')
            ->limit($limit)
            // ->order('id DESC')
            ->select();

        return $this->pushTags($ret);
    }

    public function listFT($page_size = 30)
    {
        return db('data_content_264')
            // ->where('type','英语单词')
            // ->where('a','not like', '$%')
            // ->order('id DESC')
            ->paginate($page_size);
    }

    public function getFT($id)
    {
        return db('data_content_264')
            ->where('Id',$id)
            ->find();
    }

    public function listHeightKK($limit=0)
    {
        $ret = db('remember_kk')
            ->where('hits', '>', 0)
            // ->where('a','not like', '$%')
            ->limit($limit)
            ->order('hits DESC')
            ->select();

        return $this->pushTags($ret);
    }

    // 随机获取 Object 列表
    public function listRand($limit, $tag_ids=null, $no_tag_ids=null, $is_nulltag=1){

        $res = db('remember')
            // ->field('\'los\' as source, dtb.instance_id, los.obj_id,title,thumb, raw_img, comment,shixiao,zhongyao,shouyi,type,inputtime,\'\' as json')
            ->join('v_sources_tags B', 'remember.id=B.obj_id AND B.app_id='.config('sys_info')['remember']['id'],   'left')     // TODO 
            ->where('app_id', config('sys_info')['remember']['id'])
            ->where(
                $tag_ids ? (
                    $is_nulltag ? "(tag_ids regexp '_".implode('_|_', $tag_ids)."_' OR tag_ids IS NULL)" : "(tag_ids regexp '_".implode('_|_', $tag_ids)."_')"
                ) : ''
            )
            ->where('tag_ids', 'like', '%\_744\_%')
            // ->where(
            //     $no_tag_ids ? (
            //         $is_nulltag ? "(tag_ids not regexp '_".implode('_|_', $no_tag_ids)."_' OR tag_ids IS NULL)" : "(tag_ids not regexp '_".implode('_|_', $tag_ids)."_')"
            //     ) : ''
            // )
            ->where('type', '英语单词')
            ->where('(blockdate < DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL 5 DAY), "%Y-%m-%d") OR blockdate IS NULL)')
            // 排除带标签“待学”
            ->where('B.tag_ids','NOT LIKE', '%\_721\_%')
            ->select();
        
        // echo db()->getLastSql();

        $size = sizeof($res);

        $arr = [];
        if($size>$limit){
            
            // ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★
            //  该算法非常关键
            // ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★ ☆ ★
           
            // 加权（ 时效*10+1 ）
            $range_arr = array();
            for($j=0; $j<$size; $j++){

                // v = 0  1   2    3  4   5   6    7    8     9    10
                // % = 1  1x  1.4x 2x 2.8 x4x 5.6x 8x   11.3x 16x  22.6x
                
                // % = 1  2x  4x   8x 16x 32x 64x  128x 256x  512x 1024x
                if($res[$j]['v']==0){
                    $range_arr[] = $j;
                } else {
                    for($k=0; $k < (int)(2*pow(2, ($res[$j]['v']-1))); $k++){
                        $range_arr[] = $j;
                    }
                }
            }

            $cache_arr = [];
            $k=0;
            while ($k < $limit) {
                $item = $res[$range_arr[mt_rand(0, sizeof($range_arr)-1)]];
                if (!in_array($item['id'], $cache_arr)) {
                    $arr[] = $item;
                    $cache_arr[] = $item['id'];
                    $k++;
                }
            }
                
        }else{
            $arr = $res;
        }


        // 捆绑文章标签
        return $this->pushBcz($this->pushTags($arr));
    }

	// 获取没有音频的记录
    public function listNoAudioEnglish()
    {
        return db('remember')
            ->where('type','英语单词')
            // ->where('a','not like', '$%')
            ->where('id > 5368')
            ->where('us_audio IS NULL')
            // ->where('us_audio IS NOT NULL')
            // ->where('en_definition IS NULL')
            ->order('id DESC')
            ->select();
    }

    // 获取没有本地化的音频记录
    public function listNoLocalAudioEnglish()
    {
        return db('remember')
            ->where('type','英语单词')
            // ->where('a','not like', '$%')
            ->where('local_audio IS NULL')
            // ->where('us_audio IS NOT NULL')
            ->where('audio_addresses IS NOT NULL')
            ->where('audio_addresses', '<>', '')
            ->order('id DESC')
            ->select();
    }

    // 获取解释中含人名解释的记录
    public function listNameDesctipt()
    {
        return db('remember')
            ->where('b REGEXP "n\\\\. \\\\((.*)人名"')
            ->where('b', '>', 'a')
            ->order('id DESC')
            ->limit(80)
            ->select();
    }

    // 获取某“定制单词簿”中的单词
    public function listBookWord($bookId)
    {
        $ret = db('remember')
            // ->field('count(*) C')
            ->join('v_sources_tags t', 'remember.id=t.obj_id', 'left')
            ->join('remember_data d', 'remember.id=d.obj_id', 'left')
            ->where('app_id', config('sys_info')['remember']['id'])
            ->where('book_id', $bookId)
            ->where('type','英语单词')
            ->where('v > 0')
            ->where('(blockdate < DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL 5 DAY), "%Y-%m-%d") OR blockdate IS NULL)')
            // ->where('a','not like', '$%')
            // ->where('tag_ids', 'like', '%\_801\_%')
            ->order('listorder')
            ->select();

        // 捆绑文章标签
        return $this->pushTags($ret);
    }

    // 获取所有考研需要背的单词数
    public function listAllKYEnglishCount()
    {
        return db('remember')
            // ->field('count(*) C')
            ->join('v_sources_tags t', 'remember.id=t.obj_id', 'left')
            ->where('app_id', config('sys_info')['remember']['id'])
            // ->where('v > 0')
            ->where('type','英语单词')
            // ->where('a','not like', '$%')
            ->where('tag_ids', 'like', '%\_801\_%')
            // ->order('id DESC')
            ->count();
    }

    // 获取记录分布状态
    // TODO: 改进。还需要补充 read_num 等
    public function sumStatus()
    {
        return db('remember')
            ->field('v, count(*) C')
            ->join('v_sources_tags t', 'remember.id=t.obj_id', 'left')
            ->where('app_id', config('sys_info')['remember']['id'])
            ->where('type','英语单词')
            ->where('tag_ids', 'like', '%\_801\_%')
            ->group('v')
            ->order('v DESC')
            ->select();
    }

    

    // 获取最近 N 日（每日）学习情况
    public function learnEachDayStatus($day)
    {
        return db('remember_log')
            ->field('DATE_FORMAT(inputtime, "%Y-%c-%e") d, count(DISTINCT remember_log.obj_id) C')
            ->join('v_sources_tags t', 'remember_log.obj_id=t.obj_id', 'left')
            ->where('app_id', config('sys_info')['remember']['id'])
            ->where('tag_ids', 'like', '%\_801\_%')
            ->where('v', 0)
            // ->where('inputtime > DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL '.$day.' DAY), "%Y-%m-%d")')
            ->group('d')
            ->order('d DESC')
            ->limit($day)
            ->select();
    }

    

    // 获取没有“百词斩”数据的记录
    public function listNoBczEnglish($limit=0)
    {
        return db('remember_english')
            ->where('bcz IS NULL')
            // ->where('a','not like', '$%')
            ->limit($limit)
            ->order('id')
            ->select();
    }

    // 获取已采集“百词斩”数据的记录
    public function listHaveBczBlob($limit=0)
    {
        return db('remember_english')
            // ->join('remember_local_bcz blob', 'remember_english.topic_id=blob.topic_id', 'left')
            ->where('bcz IS NOT NULL')
            // ->where('id < 5153')
            ->limit($limit)
            ->order('id DESC')
            ->select();
    }

    // 获取“百词斩”资源 URI
    public function listBczSourceURI($type)
    {
        if($type=='audio'){
            $type1 = 'is_local_audio';
            $type2 = 'audio';
        } else if($type=='img'){
            $type1 = 'is_local_img';
            $type2 = 'img';
        }
        return db('remember_local_bcz')
            // ->join('remember_local_bcz blob', 'remember_english.topic_id=blob.topic_id', 'left')
            ->where($type1, 0)
            ->where($type2.'<>""')
            // ->where('id < 5153')
            ->limit(0)
            ->order('source_id DESC')
            ->select();
    }

    // 获取本地“百词斩”资源列表
    public function listBczSource($type)
    {
        return db('remember_local_bcz')
            // ->join('remember_local_bcz blob', 'remember_english.topic_id=blob.topic_id', 'left')
            ->where($type.'<>""')
            ->group($type)
            // ->where('id < 5153')
            ->select();
    }



    // 增加待下载记录
    public function saveEnglish($insertData)
    {
        try{
            return db('remember')
                ->insertGetId($insertData);
        }catch(\Exception $e){
            echo '<hr>'.$e->getMessage().'<br>';
            return -1;
        }
    }

    // 增加资源记录
    public function insertURI($insertData)
    {
        try{
            return db('remember_local_bcz')
                ->insertGetId($insertData);
        }catch(\Exception $e){
            echo '<hr>'.$e->getMessage().'<br>';
            return -1;
        }
    }


    // 更新详情
    public function updateEnglishDetailData($where, $updateData){
        return db('remember_english')
            ->where($where)
            ->update($updateData);
    }

    // 标记为已下载
    public function updateSourceStatus($where, $updateData){
        return db('remember_local_bcz')
            ->where($where)
            ->update($updateData);
    }

    // 更新定制单词簿中单词的排序
    public function updateBookWordOrder($where, $updateData){
        return db('remember_data')
            ->where($where)
            ->update($updateData);
    }





    // +----------------------------------------------------------------------
    // | 内部功能函数
    // +----------------------------------------------------------------------

    // 给记录附上百词斩二进制数据
    protected function pushBcz($result_array)
    {
        // echo sizeof($result_array);
        $arr = [];
        foreach($result_array as $v){
            $res = db('remember_english')
            ->where('topic_id', $v['topic_id'])
            ->find();
            $Baicizhan = new \Baicizhan($res['bcz']);
            $graphicExamples = [];
            foreach ($Baicizhan->listGraphicExample() as $k2 => $v2) {
                $graphicExamples[] = [
                    $v2[0]['data'],
                    $v2[1]['data'],
                    $v2[2]['data'],
                    $v2[3]['data'],
                    $v2[4]['data']
                ];
            }
            $v['bcz'] = [
                'root'=> $Baicizhan->getRoot(),
                'englishDefinitions'=> $Baicizhan->listEnglishDefinitions(),
                'graphicExamples'=> $graphicExamples
            ];
            $arr[] = $v;
        }
        return $arr;
    }
    
}
