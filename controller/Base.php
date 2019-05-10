<?php
namespace app\Remember\controller;

use think\Controller;
use app\remember\model\RememberModel;
use app\ltag\model\TagModel;

class Base extends Controller
{
	private $base_m;
	protected $ltag_access_key = '1679091c5a880faf6fb5e6087eb1b2dc';

	public function __construct(){
		parent::__construct();

		$this->base_m = new RememberModel();
		$this->tag_m = new TagModel();
	}

	// Remember 首页
	public function index()
	{
		// $this->assign('all_words', $this->base_m->listEnglish(999100));
		$this->assign('all_words', []);

		return $this->fetch('/index');
	}

	// 根据字母查单词 JSON
	public function query()
	{
		$kw = trim(input('query'));

		if ($kw) {
			return json($this->base_m->searchByKeyword($kw));
		}
	}

	// 最近入库
	public function last()
	{
		$n = input('n');

		return json($this->base_m->listLast($n));
	}

	// 插入新记录
	public function insert()
	{
		$a = trim(input('a'));
		$b = input('b');
		$c = input('c');
		$description = input('description');
		$type = input('type');
		$source = input('source');

		if ($a) {
			$insertData = [
				'a' => $a,
				'b' => $b,
				'c' => $c,
				'description' => $description,
				'type' => $type,
				'source' => $source,
			];

			return $this->base_m->insertData($insertData);
		}
	}

	// 更新记录
	public function update()
	{
		$id = input('id');
		$a = input('a');
		$b = input('b');
		$b2 = input('b2');
		$b3 = input('b3');
		$c = input('c');
		$description = input('description');
		$type = input('type');
		$source = input('source');

		if ($a && ($b || $b2 || $c || $description)) {
			$where = ['id'=>$id];
			$updateData = [
				'a' => $a,
				'b' => $b,
				'b2' => $b2,
				'b3' => $b3,
				'c' => $c,
				'description' => $description,
				'type' => $type,
				'source' => $source,
			];

			return $this->base_m->updateData($where, $updateData);
		}
	}

	// 修改熟悉度
	public function updateV()
	{
		$id = input('id');
		$v = input('v');

		return $this->base_m->updateData(['id'=>$id], ['v' => $v]);
		// return $this->base_m->updateData(['id'=>$id], ['v' => db()->raw('v-1')]);
	}

	// 删除记录
	public function remove()
	{
		$id = input('id');

		if ($id) {
			return $this->base_m->removeData($id);
		}
	}

	public function youdao_api()
	{
		$q = input('q');

		$xml = simplexml_load_file('http://dict.youdao.com/fsearch?xmlVersion=3.3&q='.$q, null, LIBXML_NOCDATA);

		return json( $json = json_decode(json_encode($xml),true) );
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
	            ->field('A.tag_id, A.tagname')
	            ->join('tag_data B', 'A.tag_id=B.tag_id', 'left')
	            ->where('app_id', 6)                                             // TODO 自动获取
	            ->where('obj_id', $v['obj_id'])
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
