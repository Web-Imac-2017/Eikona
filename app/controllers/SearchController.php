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
					var_dump($res);
					die();
					break;

				case "tag":
					$res = $this->model->searchTag($query);
					break;

				default:
					$res = $this->model->searchAll($query);
					break;
			}
			$resp->setSuccess(200, "request done");
		}else{
			$resp->setFailure(400, "Missing value. Request aborted");
		}
		

		$resp->send();
	}

}