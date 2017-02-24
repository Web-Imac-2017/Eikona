<?php

class LikeController{

	private $model;


	public function __construct()
	{
		$this->model = new LikeModel();
	}

}