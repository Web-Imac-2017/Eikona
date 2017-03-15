<?php

class SearchController
{
	private $model;

	public function __construct()
	{
		$this->model = new SearchModel();
	}

	/*
	ON PEUT RECHERCHER :
		- USER
		- PROFILS
		- DESC POST
		- TAG
		- COMMENTAIRES
	 */
	
	public function profile()
	{
		$resp = new Response();

		if(!empty($_POST['query'])){
			$res = $this->model->searchProfile($_POST['query']);
			if(count($res) != 0){
				$resp->setSuccess("result(s) found")
					 ->bindValue("result", $res);
			}else{
				$resp->setFailure(404, "no result found");
			}
		}else{
			$resp->setFailure(400, "Missing query. Request aborted");
		}

		$resp->send();
	}

	public function post()
	{
		$resp = new Response();

		if(!empty($_POST['query'])){
			$res = $this->model->searchDescription($_POST['query']);
			if(count($res) != 0){
				$resp->setSuccess("result(s) found")
					 ->bindValue("result", $res);
			}else{
				$resp->setFailure(404, "no result found");
			}
		}else{
			$resp->setFailure(400, "Missing query. Request aborted");
		}

		$resp->send();
	}

	public function comment()
	{
		$resp = new Response();

		if(!empty($_POST['query'])){
			$res = $this->model->searchComment($_POST['query']);
			if(count($res) != 0){
				$resp->setSuccess("result(s) found")
					 ->bindValue("result", $res);
			}else{
				$resp->setFailure(404, "no result found");
			}
		}else{
			$resp->setFailure(400, "Missing query. Request aborted");
		}

		$resp->send();
	}

	public function tag()
	{
		$resp = new Response();

		if(!empty($_POST['query'])){
			$res = $this->model->searchTag($_POST['query']);
			if(count($res) != 0){
				$resp->setSuccess("result(s) found")
					 ->bindValue("result", $res);
			}else{
				$resp->setFailure(404, "no result found");
			}
		}else{
			$resp->setFailure(400, "Missing query. Request aborted");
		}

		$resp->send();
	}

	public function index()
	{
		$resp = new Response();

		$tab = [
			"profiles" => null,
			"posts"    => null,
			"comments" => null,
			"tags"     => null
		];

		if(!empty($_POST['query'])){
			$res = $this->model->searchAll($_POST['query']);
			$profiles = $res[0]['count'];
			$posts = $res[1]['count'];
			$comments = $res[2]['count'];
			$tags = $res[3]['count'];

			if($profiles != 0)
				$tab['profiles'] = $this->model->searchProfile($_POST['query']);
			
			if($posts != 0)
				$tab['posts'] = $this->model->searchDescription($_POST['query']);
			
			if($comments != 0)
				$tab['comments'] = $this->model->searchComment($_POST['query']);
			
			if($tags != 0)
				$tab['tags'] = $this->model->searchTag($_POST['query']);
			
			
			$resp->setSuccess(200, "results found")
			     ->bindValue("profiles", $tab['profiles'])
			     ->bindValue("posts", $tab['posts'])
			     ->bindValue("comments", $tab['comments'])
			     ->bindValue("tags", $tab['tags'])
			     ->send();
			return;
		}else{
			$resp->setFailure(400, "Missing value. Request aborted");
		}
	
		$resp->send();
	}

}