<?php

class TagController
{
	public function __construct()
	{
		$this->model = new TagModel();
	}

	/*
	 * Add a comment
	 *
	 */
	public function add()
	{
		$rsp = new Response();

		$profileID = Session::read("profileID");

		//Vérifier avec la vérif créé par Florian

		$rsp->send;
	}

	/*
	 * Delete a comment
	 *
	 */
	public function delete()
	{
		//Vérifier que l'utilisateur a le droit de faire ça
	}

	public function countTag()
	{

	}
}
