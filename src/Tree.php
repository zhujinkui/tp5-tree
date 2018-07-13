<?php
// 类库名称：无限分类
// +----------------------------------------------------------------------
// | PHP version 5.6+
// +----------------------------------------------------------------------
// | Copyright (c) 2012-2014 http://www.myzy.com.cn, All rights reserved.
// +----------------------------------------------------------------------
// | Author: 阶级娃儿 <262877348@qq.com> 群：304104682
// +----------------------------------------------------------------------

namespace think;
use think\Db;

class Tree
{
	//分类的数据表模型
	private $table;
	//初始化提示
	private $init_option;
	//分类的数据表模型
	private $rawList    = [];
	//原始的分类数据
	private $formatList = [];
	//格式化的字符
	private $icon       = ['├&nbsp;&nbsp;'];
	//格式化的字符间隔
	private $spac       = ['1'=>'2', '2'=>'3', '3'=>'6'];
	//字段映射，分类id，上级分类fid,分类名称name,格式化后分类名称fullname
	private $fields     = [];
	//最大层级
	private $level      = 3;

	/**
	 * 初始化对象
	 * @param unknown $config
	 */
	public function __construct($table = '', $fields = [])
	{
		if (empty($table)) {
			$this->error = "参数有误！";
			exit;
		}

		$this->table       = $table;
		$this->fields      = $fields;
	}

	/**
	 * [getSelectList 获取分类]
	 * @param  integer $pid    [父级ID]
	 * @param  array   &$child [引用]
	 * @return array
	 */
	public function getSelectList($pid = 0, &$child = [])
	{
		$result = $this->_findAllCat($pid, true);

		foreach ($result as $key => $value) {
			if ($value['pid'] == 0) {
				$value['name'] = str_repeat('&emsp;', 0) . $this->icon['0'] . $value['name'];
			} else {
				$value['name'] = str_repeat('&emsp;', $this->spac[$value['level']]) . $this->icon['0'] . $value['name'];
			}

			$child[] = $value;
			$this->getSelectList($value['id'], $child);
		}

		return $child;
	}

	/**
	 * [viewList 视图渲染]
	 * @param  integer $pid          [父级ID]
	 * @param  string  $selected     [初始化选中]
	 * @param  string  $str          [初始化字符串]
	 * @param  string  $selected_str [初始化选中字符串]
	 * @return string
	 */
	public function viewList($name = 'cate', $pid = 0, $selected = 0, $init_option = '作为顶级栏目', $style_class = '', $nullable = 'false', $star = 'true', $alt_error = '请选择店铺类型', $str = '')
	{
		//获取所有信息
		$result = $this->getSelectList($pid);

		$str .= "<select id='".$name."' name='".$name."' class='".$style_class."' nullable='".$nullable."', star='".$star."', alt_error='".$alt_error."'>";
		$str .= "<option value='0-1'>&nbsp;≡ ".$init_option." ≡</option>";
		foreach ($result as $key => $value) {
			$level = $value['level'] + 1;
			if ($value['level'] == $this->level) {
				$str .= "<option disabled='disabled' class='disabled' value='".$value['id'].'-'.$level."'>{$value['name']}</option>";
			} else {
				//return $selected;
				if ($value['id'] == $selected) {
					$str .= "<option selected='selected' value='".$value['id'].'-'.$level."'>{$value['name']}</option>";
				} else {
					$str .= "<option value='".$value['id'].'-'.$level."'>{$value['name']}</option>";
				}
			}
		}

		return $str .= "</select>";
	}

	/**
	 * [_findAllCat 获取分类信息数据]
	 * @param  [array,string] 	$condition 	[查询条件]
	 * @param  [string] 		$orderby   	[排序]
	 */
	public function _findAllCat($pid = 0, $is_where = false)
	{
		if ($is_where) {
			$result = Db::name($this->table)->where(['pid'=>$pid], true)->field($this->fields)->select();
		} else {
			$result = Db::name($this->table)->field($this->fields)->select();
		}
		return $result;
	}

	/**
	 * [_findAllCat 获取分类信息数据]
	 * @param  [array,string] 	$condition 	[查询条件]
	 * @param  [string] 		$orderby   	[排序]
	 */
	public function _getTypeCat($id = 0)
	{
		if ($id) {
			return Db::name($this->table)->where(['id'=>$id])->field($this->fields)->find();
		}
		return $id;
	}
}
?>