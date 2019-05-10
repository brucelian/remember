<?php
namespace app\remember\model;

use think\Model;

class RememberModel extends Model
{
    
    // 看今天是否第一次就记住了
    public function todayStatus($id)
    {
        return db('remember_log')
            // ->field('obj_id, COUNT(obj_id) AS C')
            ->join('remember', 'remember_log.obj_id=remember.id', 'left')
            ->where('obj_id', $id)
            // ->where('DATE_FORMAT(inputtime, "%Y-%m-%d") ="'.date('Y-m-d').'"')
            ->where('(
                (ebbinghaus = 0 AND DATE_FORMAT(remember_log.inputtime, "%Y-%m-%d") ="'.date('Y-m-d').'")
                OR (ebbinghaus = 1 AND TIMESTAMPDIFF(HOUR, lasttime, current_timestamp()) < 6)
                OR (ebbinghaus = 2 AND DATEDIFF(current_timestamp(), lasttime) < 1)
                OR (ebbinghaus = 3 AND DATEDIFF(current_timestamp(), lasttime) < 2)
                OR (ebbinghaus = 4 AND DATEDIFF(current_timestamp(), lasttime) < 4)
                OR (ebbinghaus = 5 AND DATEDIFF(current_timestamp(), lasttime) < 7)
                OR (ebbinghaus > 5 AND DATEDIFF(current_timestamp(), lasttime) < 15)
            )')
            ->count();
    }



    // 获取一个对象
    public function getObjectById($id)
    {
        return db('remember')
            ->where('id', $id)
            ->find();
    }

	public function searchByKeyword($kw)
    {
        return db('remember')
            // ->field('\'remember\' as source, id, a AS title, b AS thumb, v, type, \'\' AS tags, inputtime, c AS json')
            ->where('a', 'like', '%'.$kw.'%')
            ->whereOr('b', 'like', '%'.$kw.'%')
            ->order('a')
            ->select();
    }

    public function searchByKeywordPre($kw)
    {
        return db('remember')
            ->where('a', $kw)
            ->find();
    }

    public function listLast($limit=10)
    {
        return db('remember')
            // ->where('type','英语单词')
            ->limit($limit)
            ->order('id DESC')
            ->select();
    }

    public function listToday($n)
    {
        $ret = db('remember')
            ->order('id DESC')
            ->limit($n)
            ->select();

        return $this->pushTags($ret);
    }

    public function listRand($limit, $tag_ids=null, $no_tag_ids=null, $is_nulltag=1)
    {
        // remember
        $res = db('remember')
            ->field('\'remember\' as source, id, a AS title, b AS thumb, v, type, local_audio, \'\' AS tags, inputtime, blockdate, c AS json')
            ->join('v_sources_tags B', 'remember.id=B.obj_id AND B.app_id='.config('sys_info')['remember']['id'],   'left')
            ->where('(blockdate < DATE_FORMAT(DATE_SUB("'.date('Y-m-d').'", INTERVAL 5 DAY), "%Y-%m-%d") OR blockdate IS NULL)')
            // 排除带标签“待学”
            ->where('B.tag_ids','NOT LIKE', '%\_721\_%')
            ->select();

        $size = sizeof($res);

        $arr = [];
        if($size>$limit){
            // 加权（ 时效*10+1 ）
            $range_arr = array();
            for($j=0; $j<$size; $j++){

                // v = 0     1     2     3     4     5     6     7     8     9     10
                // % = 1     1x    1.4x  2x    2.8   x4x   5.6x  8x    11.3x 16x   22.6x
                if($res[$j]['v']==0){
                    $range_arr[] = $j;
                } else {
                    for($k=0; $k < (int)(10*pow(2, ($res[$j]['v']-1)/2)); $k++){
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

        return $this->pushTags($arr);
    }

    // 获取记录数
    public function getCount(){
        return db('remember')
            ->where('v', '>', 0)
            ->count();
    }

    // 插入记录
    public function insertData($insertData){
        return db('remember')
            ->insertGetId($insertData);
    }

    // 更新记录
    public function updateData($where, $updateData){
        return db('remember')
            ->where($where)
            ->update($updateData);
    }

    // 删除记录
    public function removeData($id){
        db('remember_english')
            ->where('id', $id)
            ->delete();

        return db('remember')
            ->where('id', $id)
            ->delete();
    }

    // +----------------------------------------------------------------------
    // | 内部功能函数
    // +----------------------------------------------------------------------

    // 给记录附上拥有的 Tags
    protected function pushTags($result_array)
    {
        $arr = array();
        foreach($result_array as $v){
            $res = db('tag')
                ->alias('A')
                ->field('A.tag_id,A.tagname')
                ->join('tag_data B', 'A.tag_id=B.tag_id', 'left')
                ->where('app_id', 6)                                             // TODO 自动获取
                ->where('obj_id',$v['id'])
                ->order('listorder')
                ->select();

            $v['tags'] = array();
            foreach ($res as $v2) {
                $v['tags'][] = array('tag_id'=>$v2['tag_id'], 'tagname'=>$v2['tagname']);
            }
            $arr[] = $v;
        }
        return $arr;
    }

    
}
