<?php

namespace Common\Lib\Util;

/**
 * CX系统标签库
 * @author Anyon <cxphp@qq.com>
 */
class Tag extends \Think\Template\TagLib {

	// 标签定义
	protected $tags = array(
		'list' => array(
			'attr'	 => 'id,pk,class,action,color,bgcolor,actionlist,show,datasource,checkbox,length',
			'close'	 => 0
		),
	);

	/**
	 * list标签解析
	 * 格式： <html:list datasource="" show="" />
	 * @access public
	 * @param string $tag 标签属性
	 * @return string
	 */
	public function _list($tag) {
		$id = $tag['id']; //表格ID
		$datasource = $tag['datasource']; //列表显示的数据源VoList名称
		$pk = empty($tag['pk']) ? 'id' : $tag['pk']; //主键名,默认为id
		$style = $tag['class']; //样式名
		$name = !empty($tag['name']) ? $tag['name'] : 'vo';   //Vo对象名
		$attrList = empty($tag['attrlist']) ? false : explode(',', $tag['attrlist']); //添加额外的属性值
		$color = empty($tag['color']) ? false : $tag['color']; //设置行的字体颜色
		$bgColor = empty($tag['bgcolor']) ? false : $tag['bgcolor']; //设置行的背景颜色
		$sort = (isset($tag['sort']) && $tag['sort']) == 'false' ? false : true;
		$checkBox = isset($tag['checkbox']) ? ($tag['checkbox'] === 'false' ? false : $tag['checkbox']) : 'key';
		$length = isset($tag['length']) ? $tag['length'] : false; // 输出列表长度

		/* 功能列表分离 */
		isset($tag['actionlist']) && $actionlist = explode(',', trim($tag['actionlist']));

		/* 列表显示字段列表 */
		if ($tag['show'][0] == '$') {
			$show = $this->tpl->get(substr($tag['show'], 1));
		} elseif ($tag['show'][0] == '#') {
			$show = val(array_pop(explode('#', $tag['show'])));
		} else {
			$show = $tag['show'];
		}
		$show = explode(',', $show);

		/* 计算表格的列数 */
		$colNum = count($show);
		empty($checkBox) or $colNum++;
		empty($actionlist) or $colNum++;


		/* 拼接模板表格 */
		$parseStr = '<table id="' . $id . '" class="' . $style . '" cellpadding=0 cellspacing=0 ><thead>';
		$parseStr .= '<tr class="row" >';

		/* 处理表格列头checkbox */
		empty($checkBox) or $parseStr .='<th width="8"><input type="checkbox" id="check" onclick="EU.list.checkAll(\'' . $id . '\',undefined,\'' . $checkBox . '\')"></th>';


		/* 处理表格数据列表 */
		$fields = array();
		foreach ($show as $val) {
			$fields[] = explode(':', $val);
		}
		/* 显示指定的字段表头 */
		foreach ($fields as $field) {
			$property = explode('|', $field[0]);
			$showname = explode('|', $field[1]);
			if (isset($showname[1])) {
				$parseStr .= '<th width="' . $showname[1] . '" data-field="' . $property[0] . '">';
			} else {
				$parseStr .= '<th data-field="' . $property[0] . '">';
			}
			$showname[2] = isset($showname[2]) ? $showname[2] : $showname[0];
			if ($sort) {
				$parseStr .= '<a href="javascript:$.list.sortBy(\'' . $property[0] . '\',\'{$sort}\',\'' . ACTION_NAME . '\')" title="按照' . $showname[2] . '{$sortType} ">' . $showname[0] . '<eq name="order" value="' . $property[0] . '" ><img src="__PUBLIC__/img/{$sortImg}.gif" width="12" height="17" border="0" align="absmiddle"></eq></a></th>';
			} else {
				$parseStr .= $showname[0] . '</th>';
			}
		}
		empty($actionlist) or $parseStr .= '<th >' . L('common_table_action') . '</th>';
		$parseStr .= '</tr></thead><tbody>';

		/* 表格内容规则 */
		!!$length && $length = ' offset = "0" length = "' . $length . '"'; // 支持输出部分数据
		$parseStr .= '<volist name="' . $datasource . '" id="' . $name . '"' . $length . '><tr class="row"';
		if (!empty($color) || !empty($bgColor)) {
			$cssStyle = '';
			if (!empty($color)) {
				/* 设置行的文字颜色 */
				$cssStyle.=' color:{$' . $name . '.' . $color . '}; ';
				empty($bgColor) OR $cssStyle.=' background-color:{$' . $name . '.' . $bgColor . '}; ';
			} else {
				/* 设置行的背景 */
				empty($bgColor) OR $cssStyle.=' background-color:{$' . $name . '.' . $bgColor . '}; ';
			}
			$parseStr.=' style="' . $cssStyle . '"';
			unset($cssStyle);
		}

		/* 设置表格第一行的TR属性值 */
		if (!empty($attrList)) {
			foreach ($attrList as $v) {
				$field = array_shift(explode('|', $v));
				$parseStr .= ' data-' . $field . '="{$' . $name . '.' . $v . '}"';
			}
		}
		$parseStr .= '>';

		/* 如果需要显示checkbox 则在每行开头显示checkbox */
		empty($checkBox) OR $parseStr .= '<td ><input type="checkbox" name="' . $checkBox . '"	value="{$' . $name . '.' . $pk . '}"></td>';


		foreach ($fields as $field) {
			/* 显示定义的列表字段位置 */
			$lastAttribute = $field[count($field) - 1];
			$attr = empty($lastAttribute) ? array() : explode('align_', $lastAttribute, 2);
			if (count($attr) > 1 && in_array($attr[1], array('left', 'right', 'center'))) {
				$parseStr .= "<td style='text-align:{$attr[1]}'>";
				array_pop($field);
				$lastAttribute = $field[count($field) - 1];
			} else {
				$parseStr .= "<td style='text-align:center;'>";
			}
			$tag_a_subffix = '';
			if (count($field) > 2) {
				$auth = explode('#', $lastAttribute);
				if (count($auth) === 1 OR checkAuth($auth)) {
					$parseStr .= '<a href="javascript:void(0);" onclick="' . $field[1] . '(\'{$' . $name . '.' . $pk . '|addslashes}\')">';
					$tag_a_subffix = '</a>';
				}
				$property = explode('|', $field[0]);
				if (count($property) > 1) {
					$parseStr .= '{$' . $name . '.' . $property[0] . '|' . $property[1] . '}';
				} else {
					$parseStr .= '{$' . $name . '.' . $field[0] . '}';
				}
			} else {
				$parseStr .= '{$' . $name . '.' . $field[0] . '}';
			}
			$parseStr .= "{$tag_a_subffix}</td>";
		}

		if (!empty($actionlist[0])) {//显示指定的功能项
			$parseStr .= '<td>';
			foreach ($actionlist as $val) {
				if (strpos($val, ':')) {
					$a = explode(':', $val);
					$b = explode('#', $a[count($a) - 1]);
					switch (count($a)) {
						case 2:
							if (count($b) === 1 or checkAuth($b[1]) === true) {
								$parseStr .= '<a href="javascript:void(0);" onclick="' . $a[0] . '(\'{$' . $name . '.' . $pk . '}\')">' . $b[0] . '</a>&nbsp;';
							}
							break;
						default :
							$ptmp = array();
							foreach ($a as $k => $p) {
								if ($k >= 1 && $k < count($a) - 1) {
									$ptmp[] = '{$' . $name . '.' . $p . '}';
								}
							}

							if (count($b) === 1 or checkAuth($b[1]) === true) {
								$parseStr .= '<a href="javascript:void(0);" onclick="' . $a[0] . '(\'' . join('\',\'', $ptmp) . '\')">' . $b[0] . '</a>&nbsp;';
							}
					}
				} else {
					$array = explode('|', $val);
					$b = explode('#', $array[count($array) - 1]);
					if (count($b) === 1 or checkAuth($b[1]) === true) {
						if (count($array) > 2) {
							$parseStr .= ' <a href="javascript:void(0);" onclick="' . $array[1] . '(\'{$' . $name . '.' . $array[0] . '}\')">' . $b[0] . '</a>&nbsp;';
						} else {
							$parseStr .= ' {$' . $name . '.' . array_shift(explode('#', $val)) . '}&nbsp;';
						}
					}
				}
			}
			$parseStr .= '</td>';
		}
		$parseStr .= '</tr></volist></tbody></table>';
		return $parseStr;
	}

}
