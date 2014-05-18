<?php

namespace Home\Controller;

/**
 * 内容内页
 */
class ArticleController extends \Home\Controller\HomeController {

	//内容内页
	public function index() {
		$article = sp_sql_post(I('get.id'), '');
		$termid = $article['term_id'];
		$term_obj = new \Admin\Model\TermsModel();
		$term = $term_obj->where("term_id='$termid'")->find();
		$smeta = json_decode($article[smeta], true);
		$this->assign($article);
		$this->assign("smeta", $smeta);
		$this->assign("term", $term);
		$tplname = empty($term["one_tpl"]) ? "default" : $term["one_tpl"];
		$this->display("article:$tplname");
	}

}
