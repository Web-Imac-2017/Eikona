<?php

interface SearchControllerInterface
{
    public function profile();

	public function post();

	public function comment();

	public function tag();

	public function index();
}

class SearchController implements SearchControllerInterface
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
		$rsp = new Response();

		if(empty($_POST['query']))
        {
			$rsp->setFailure(400, "Missing query. Request aborted")
                ->send();
            
            return;
        }
        
        $res = $this->model->searchProfile($_POST['query']);

        if(count($res) == 0)
        {
            $rsp->setFailure(404, "no result found")
                ->send();
            
            return;
        }
        
        $rsp->setSuccess("result(s) found")
            ->bindValue("result", $res)
            ->send();
	}

	public function post()
	{
		$rsp = new Response();

		if(empty($_POST['query']))
        {
			$rsp->setFailure(400, "Missing query. Request aborted")
                ->send();
            
            return;
        }
        
        $res = $this->model->searchDescription($_POST['query']);

        if(count($res) == 0)
        {
            $rsp->setFailure(404, "no result found")
                ->send();
            
            return;
        }
        
        $rsp->setSuccess("result(s) found")
            ->bindValue("result", $res)
            ->send();
	}

	public function comment()
	{
		$rsp = new Response();

		if(empty($_POST['query']))
        {
			$rsp->setFailure(400, "Missing query. Request aborted")
                ->send();
            
            return;
        }
        
        $res = $this->model->searchComment($_POST['query']);
        
        if(count($res) != 0)
        {
            $rsp->setFailure(404, "no result found")
                ->send();
            
            return;
        }
		
        $rsp->setSuccess("result(s) found")
            ->bindValue("result", $res)
            ->send();
    }

	public function tag()
	{
		$resp = new Response();

		if(empty($_POST['query']))
        {
			$rsp->setFailure(400, "Missing query. Request aborted")
                ->send();
            
            return;
        }
        
        $res = $this->model->searchTag($_POST['query']);
        
        if(count($res) == 0)
        {
            $resp->setFailure(404, "no result found")
                ->send();
            
            return;
        }
        
        $rsp->setSuccess("result(s) found")
            ->bindValue("result", $res)
            ->send();
	}

	public function index()
	{
		$rsp = new Response();

		$tab = [
			"profiles" => null,
			"posts"    => null,
			"comments" => null,
			"tags"     => null
		];

		if(empty($_POST['query']))
        {
			$rsp->setFailure(400, "Missing value. Request aborted")
                ->send();
            
            return;
        }
        
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


        $rsp->setSuccess(200, "results found")
            ->bindValue("profiles", $tab['profiles'])
            ->bindValue("posts", $tab['posts'])
            ->bindValue("comments", $tab['comments'])
            ->bindValue("tags", $tab['tags'])
            ->send();
	}

}
