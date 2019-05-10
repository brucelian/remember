<?php
namespace app\remember\controller;

use app\remember\model\MathModel;

class Math extends Base
{
	protected $base_m;
    protected $circleId = 9;		// 数学

	public function __construct(){
		parent::__construct();

		$this->base_m = new MathModel();
	}


    // 详情页
    public function detail()
    {
        $id = input('id');

        $detailata = $this->base_m->getObjectById($id);

        if($detailata){

            $this->assign('detailata', $detailata);
            // $this->assign('appId', $this->appId);
            // $this->assign('circleId', $this->circleId);
            // $this->assign('readCount', $this->base_m->getReadCount($id));
            $this->assign('quicktag', $this->tag_m->listTagsByCircle($this->circleId));
            $this->assign('tags', $this->tag_m->listObjTags(6, $id));
            
            $this->assign('learLogs', $this->base_m->listLearLog($id));
            
            $is_m = input('m');
            if ($is_m){
                // return $this->fetch('detail_m');
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

    // 插入新记录
    public function insertLearnLog()
    {
        $id = input('id');
        $score = input('score');
        $duration = input('duration');
        $inputtime = input('inputtime');
        $refer = input('refer');
        $description = input('description');

        if ($id) {
            $insertData = [
                'remember_id'   => $id,
                'score'         => $score,
                'duration'      => $duration,
                'inputtime'     => $inputtime,
                'refer'         => $refer,
                'description'   => $description
            ];

            return $this->base_m->insertLearnLog($insertData);
        }
    }





















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

	// 详情页

    // 随机获取 N 个单词短语
    public function listRand()
    {
        // $d = $this->base_m->todayStatus(3792);
        // print_r($d);

        $limit = 10;
        $tag_ids = input('tag_ids/a');
        $no_tag_ids = input('no_tag_ids/a');
        $is_nulltag = 1;

        $data = $this->base_m->listRand($limit, $tag_ids, $no_tag_ids, $is_nulltag);

        try {
            return json($data);
        } catch (Exception $e) {
            print_r($data);
        }
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


    
}
