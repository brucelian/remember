<?php
namespace app\remember\model;

use think\Model;

class EnglishModel extends RememberModel
{
	// protected $appId = 5;

    public function __construct(){
        parent::__construct();
    }

    /*
     * 临时功能
     */
    
    // 获取需精简 JSON 的扇贝单词
    public function listBigJson($limit=0)
    {
        return db('remember_english')
            ->where('sb_collins', 'like', '%{"msg":%')
            ->limit($limit)
            ->select();
    }




    // 合并“扇贝”、“百词斩”解释
    public function listShanbeiBCZEnglish($limit=0)
    {
        return db('remember')
            ->where('type','英语单词')
            // ->where('source="shanbei" OR source="BCZ"')
            ->where('b IS NULL')
            // ->where('c','')
            ->limit($limit)
            // ->order('a DESC')
            ->select();

        // return $this->pushTags($ret);
    }

    // 获取含有人名解释的脏数据
    public function listDirtyEnglish($limit=0)
    {
        $ret = db('remember')
            ->where('type','英语单词')
            ->where('a','not like', '$%')
            ->where('b','like', '%人名%')
            ->limit($limit)
            ->order('a DESC')
            ->select();

        return $this->pushTags($ret);
    }

    // 获取需采集扇贝ids 的单词
    public function listNoShanbeiIds($limit=0)
    {
        return db('shanbei_ids_lyz')
            ->join('remember', 'remember.a=shanbei_ids_lyz.a', 'right')
            ->where('type', '英语单词')
            ->where('ids IS NULL')
            ->limit($limit)
            ->select();
    }

    // 获取需采集扇贝 content_id 的单词
    public function listNoContentId($limit=0)
    {
        return db('shanbei_ids_lyz')
            ->where('content_id IS NULL')
            ->limit($limit)
            ->select();
    }

    // 获取已有 ids 单词
    public function listShanbeiIds($limit=0)
    {
        return db('shanbei_ids_lyz')
            ->limit($limit)
            ->order('inputtime DESC')
            ->select();
    }

    // 保存扇贝单词 ids
    public function saveShanbeiIds($insertData)
    {
        try{
            return db('shanbei_ids_lyz')
                ->insertGetId($insertData);
        }catch(\Exception $e){
            echo '<hr>'.$e->getMessage().'<br>';
            return -1;
        }
    }

    // 标记扇贝采集状态
    public function updateShanbeiIds($where, $updateData)
    {
        return db('shanbei_ids_lyz')
            ->where($where)
            ->update($updateData);
    }

    

    // 获取需补充扇贝付费内容（root、柯林斯）的单词
    public function listNoPayContentEnglish($type, $limit=0)
    {
        return db('shanbei_ids_lyz')
        //    ->join('remember', 'remember.topic_id=remember_english.topic_id', 'right')
            ->join(' remember_english','remember_english.a=shanbei_ids_lyz.a', 'right')
            ->where('sb_roots IS NULL')
            
        // return db('shanbei_ids_lyz')
        //     ->join('remember', 'remember.a=shanbei_ids_lyz.a', 'left')
        //     ->where($type, 0)
            ->limit($limit)
            ->order('id')
            ->select();
    }


    // 更新扇贝单词二进制数据
    public function saveShanbeiPC($where, $insertData)
    {
        $count = db('remember_english')
            ->where($where)
            ->count();

        if ($count > 0) {
            return db('remember_english')
                ->where($where)
                ->update($insertData);
        } else {
            return db('remember_english')
                ->insert( array_merge($insertData, $where));
        }
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

    public function searchByKeywordPre($kw)
    {
        return db('remember_english')
            ->join('remember', 'remember.topic_id=remember_english.topic_id', 'right')
            ->where('remember.a', $kw)
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
        return db('book_ft')
            // ->where('type','英语单词')
            // ->where('a','not like', '$%')
            ->orderRaw('DATE_FORMAT(publictime, "%Y-%m-%d") DESC')
            ->paginate($page_size);
    }

    public function getFT($id)
    {
        return db('book_ft')
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

        $ret = db('remember')
            ->field('\'random\' tag, id, topic_id, audio_addresses, a, b, b2, c, description, en_definition, local_audio, v')
            ->join('v_sources_tags B', 'remember.id=B.obj_id', 'left')     // TODO 
            ->where('app_id', config('sys_info')['remember']['id'])
            ->where(
                $tag_ids ? (
                    $is_nulltag ? "(tag_ids regexp '_".implode('_|_', $tag_ids)."_' OR tag_ids IS NULL)" : "(tag_ids regexp '_".implode('_|_', $tag_ids)."_')"
                ) : ''
            )
            // 801大纲 659【考研】 744核心 800-phrase-core 564-Foundation
            ->where('tag_ids', ['like', '%\_744\_%'], ['like', '%\_564\_%'], ['like', '%\_800\_%'], 'OR')
            // ->where(
            //     $no_tag_ids ? (
            //         $is_nulltag ? "(tag_ids not regexp '_".implode('_|_', $no_tag_ids)."_' OR tag_ids IS NULL)" : "(tag_ids not regexp '_".implode('_|_', $tag_ids)."_')"
            //     ) : ''
            // )
            ->where('type', '英语单词')
            // ->where('(blockdate < DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL 5 DAY), "%Y-%m-%d") OR blockdate IS NULL)')
            // 排除带标签“待学”
            ->where('B.tag_ids','NOT LIKE', '%\_721\_%')
            ->select();
        // echo db()->getLastSql();

        $size = sizeof($ret);

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
                if($ret[$j]['v']==0){
                    $range_arr[] = $j;
                } else {
                    for($k=0; $k < (int)(2*pow(2, ($ret[$j]['v']-1))); $k++){
                        $range_arr[] = $j;
                    }
                }
            }

            $cache_arr = [];
            $k=0;
            while ($k < $limit) {
                $item = $ret[$range_arr[mt_rand(0, sizeof($range_arr)-1)]];
                if (!in_array($item['id'], $cache_arr)) {
                    $arr[] = $item;
                    $cache_arr[] = $item['id'];
                    $k++;
                }
            }
                
        }else{
            $arr = $ret;
        }

        // 错题
        $ret2 = db('remember_log')
            ->alias('log')
            ->distinct(true)
            ->field('\'mistake\' tag, id, topic_id, audio_addresses, a, b, b2, c, description, en_definition, local_audio, r.v')
            ->join('remember r', 'log.obj_id=r.id', 'left')
            ->where('log.inputtime > DATE_SUB(curdate(),INTERVAL 1 DAY)')
            ->where('state > 0')
            ->select();

        shuffle($ret2);
        // 捆绑常选错项
        $arr2 = [];
        foreach ($ret2 as $k => $v) {
            $ret3 = db('remember_mistake')
                ->distinct(true)
                ->field('option')
                ->where('obj_id', $v['id'])
                ->select();

            $mistakes = [];
            foreach ($ret3 as $k2 => $v2) {
                $mistakes[] = $v2['option'];
            }

            $v['mistakes'] = $mistakes;
            $arr2[] = $v;
        }
        $arr = array_merge($arr2, $arr);

        // $arr = array_merge($ret2, $arr);

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
            // ->where('(blockdate < DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL 5 DAY), "%Y-%m-%d") OR blockdate IS NULL)')
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

    // 获取最近 N 日学习进度
    public function learnStatus($day)
    {
        return db('remember_log')
            ->field('v, count(DISTINCT remember_log.obj_id) C')
            ->join('v_sources_tags t', 'remember_log.obj_id=t.obj_id', 'left')
            ->where('app_id', config('sys_info')['remember']['id'])
            ->where('tag_ids', 'like', '%\_801\_%')
            ->where('state', 0)
            ->where('inputtime > DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL '.$day.' DAY), "%Y-%m-%d")')
            ->group('v')
            ->order('v DESC')
            ->select();
    }

    // 获取今日错题
    // TODO
    public function listMistakes()
    {
        // return db('remember_log')
        //     ->field('v, count(DISTINCT remember_log.obj_id) C')
        //     ->join('v_sources_tags t', 'remember_log.obj_id=t.obj_id', 'left')
        //     ->where('app_id', config('sys_info')['remember']['id'])
        //     ->where('tag_ids', 'like', '%\_801\_%')
        //     ->where('state IS NULL')
        //     ->where('inputtime > DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL '.$day.' DAY), "%Y-%m-%d")')
        //     ->group('v')
        //     ->order('v DESC')
        //     ->select();
    }

    // 插入 错题选项记录
    public function insertMistakeLog($insertData){
        return db('remember_mistake')
            ->insertGetId($insertData);
    }

    // 获取艾宾浩斯复习题
    public function listEbbinghaus()
    {
        $ret = db('remember')
            ->field('*, \'Ebbinghaus\' tag')
            ->where('(
                (ebbinghaus = 1 AND TIMESTAMPDIFF(HOUR, lasttime, current_timestamp()) > 6)
                OR (ebbinghaus = 2 AND DATEDIFF(current_timestamp(), lasttime) > 0)
                OR (ebbinghaus = 3 AND DATEDIFF(current_timestamp(), lasttime) > 1)
                OR (ebbinghaus = 4 AND DATEDIFF(current_timestamp(), lasttime) > 3)
                OR (ebbinghaus = 5 AND DATEDIFF(current_timestamp(), lasttime) > 6)
                OR (ebbinghaus = 6 AND DATEDIFF(current_timestamp(), lasttime) > 14)
                OR (ebbinghaus > 6 AND DATEDIFF(current_timestamp(), lasttime) > 30)
            )')
            ->where('ebbinghaus > 0')
            ->where('v > 0')
            ->order('ebbinghaus DESC')
            ->select();
        shuffle($ret);

        // 捆绑常选错项
        $arr = [];
        foreach ($ret as $k => $v) {
            $ret3 = db('remember_mistake')
                ->distinct(true)
                ->field('option')
                ->where('obj_id', $v['id'])
                ->select();

            $mistakes = [];
            foreach ($ret3 as $k2 => $v2) {
                $mistakes[] = $v2['option'];
            }

            $v['mistakes'] = $mistakes;
            $arr[] = $v;
        }

        // 捆绑文章标签
        return $this->pushBcz($this->pushTags($arr));
    }

    // 升级艾宾浩斯
    public function upgradeEbbinghaus($id)
    {
        return db('remember')
            ->where('id', $id)
            ->where('(
                ebbinghaus = 0
                OR (ebbinghaus = 1 AND TIMESTAMPDIFF(HOUR, lasttime, current_timestamp()) > 6)
                OR (ebbinghaus = 2 AND DATEDIFF(current_timestamp(), lasttime) > 0)
                OR (ebbinghaus = 3 AND DATEDIFF(current_timestamp(), lasttime) > 1)
                OR (ebbinghaus = 4 AND DATEDIFF(current_timestamp(), lasttime) > 3)
                OR (ebbinghaus = 5 AND DATEDIFF(current_timestamp(), lasttime) > 6)
                OR (ebbinghaus = 6 AND DATEDIFF(current_timestamp(), lasttime) > 14)
                OR (ebbinghaus > 6 AND DATEDIFF(current_timestamp(), lasttime) > 30)
            )')
            ->update([
                'lasttime' => db()->raw('CURRENT_TIMESTAMP'),
                'ebbinghaus' => db()->raw('ebbinghaus+1')
            ]);
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


    // 插入学习记录
    public function insertLearnLog($insertData){
        return db('remember_log')
            ->insertGetId($insertData);
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

    // 更改操作 Log
    public function updateLearnLog($id, $state){
        return db('remember_log')
            ->where('log_id', $id)
            ->update(['state'=>$state]);
    }

    // 删除操作 Log
    public function cancelLearnLog($id){
        return db('remember_log')
            ->where('log_id', $id)
            ->delete();
    }

    // 修改 FT 双语阅读 备注
    public function updateFTComment($where, $updateData){
        return db('book_ft')
            ->where($where)
            ->update($updateData);
    }

    // FT 双语阅读 打卡
    public function FTclockIn($id, $insertData){

        $ret = db('book_ft')
            ->where('id', $id)
            ->update(['read_count'=>db()->raw('read_count+1')]);

        return db('book_read_log')
            ->insertGetId($insertData);
    }





    // +----------------------------------------------------------------------
    // | 内部功能函数
    // +----------------------------------------------------------------------

    // 给记录附上百词斩二进制数据
    // 暂时不能废弃！！！！（因为解析出错会导致接口异常，建议直接把数据解析到各字段），但是前端解析需要 图片例句
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
