<?php

class ProfilesController
{
	private $model;
	private $view;

	public function __construct()
	{
		$this->model = new PostsModel();
	}

	public function create($postId)
	{
		$type = $_POST['postType'];
		$desc = isset($_POST['postDescription']) ? $_POST['postDescription'] : "";
		$time = $_POST['postTime'];

		/*gestion de l'image*/

		if(is_uploaded_file($_FILES['img']['tmp_name']))
		{
			$source = $_FILES['img']['tmp_name'];
			$format = getimagesize($source);
			$tab;
			
			if(preg_match('#(png|gif|jpeg)$#i', $format['mime'], $tab))
			{
				$imSource = 'imagecreatefrom'.$tab[1]($source);
				if($tab[1] == "jpeg")
					$tab[1] = "jpg";
				$extension = $tab[1];
				/*$_SESSION['extension_img'] = $extension;*/
			}

			if($format['mime'] == "image/png")
			{
				/*transformer png en jpg*/
				/* ... */
				$extension = 'jpg';
			}
			
			/*$imdest = imagecreatetruecolor(120, 120);
			imagecopyresampled($imdest, $imSource, 0, 0, 0, 0, 120, 120, $format[0], $format[1]);*/
			
			'image'.$tab[1]($source, '../medias/img/'.$postId.'.'.$format);

		}


		/*appel du model*/
		$this->model->create($type, $extension, $desc, $time);
	}

	public function delete(){
		$this->model->delete();
	}







}
















