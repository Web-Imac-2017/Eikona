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

	public function index()
	{
		$resp = new Response();

		$query = !empty($_POST['query']) ? $_POST['query'] : null;
		$field = !empty($_POST['field']) ? $_POST['field'] : false;

		if($query != null){
			switch($field){

				/*---------- PROFILE ----------*/
				case "profile":
					$res = $this->model->searchProfile($query);
					break;

				case "description":
					$res = $this->model->searchDescription($query);
					break;

				case "comment":
					$res = $this->model->searchComment($query);
					break;

				case "tag":
					$res = $this->model->searchTag($query);
					break;

				default:
					$res = $this->model->searchAll($query);
					$profiles = $res[0]['count'];
					$posts = $res[1]['count'];
					$comments = $res[2]['count'];
					$tags = $res[3]['count'];
					
					$tab = [
						"profiles" => null,
						"posts"    => null,
						"comments" => null,
						"tags"     => null
					];

					if($profiles != 0){
						$tab['profiles'] = $this->model->searchProfile($query);
					}
					if($posts != 0){
						$tab['posts'] = $this->model->searchDescription($query);
					}
					if($comments != 0){
						$tab['comments'] = $this->model->searchComment($query);
					}
					if($tags != 0){
						$tab['tags'] = $this->model->searchTag($query);
					}
					$resp->setSuccess(200, "results found")
					     ->bindValue("profiles", $tab['profiles'])
					     ->bindValue("posts", $tab['posts'])
					     ->bindValue("comments", $tab['comments'])
					     ->bindValue("tags", $tab['tags'])
					     ->send();
					return;
			}
			if(count($res) != 0){
				$resp->setSuccess("result(s) found")
					 ->bindValue("result", $res);
			}else{
				$resp->setFailure(404, "no result found");
			}
		}else{
			$resp->setFailure(400, "Missing value. Request aborted");
		}
		

		$resp->send();
	}

}