<?php

class BotController extends DBInterface
{   
    private $currUserID;
    private $currProfileID;
    
    private $action;
    private $toPrint;
    
    public function __construct()
    {
        parent::__construct();   
    }

    public function index()
    {
        
        $actions = ["addPost", "addComment", "addLike", "viewPost", "follow"];

        $rand = mt_rand(0, 100);

        if($rand < 20)
            $action = "addPost";
        else if($rand < 55)
            $action = "addComment";
        else if($rand < 80)
            $action = "addLike";
        else if($rand < 95)
            $action = "viewPost";
        else
            $action = "follow";

        $this->$action();

        header('Content-Type: text/html');
        http_response_code(200);

        echo '
        <!doctype html>
        <html>
            <head>
                <title>BotController - Hang Tight</title>
                <meta http-equiv="refresh" content="2">
            </head>
            <body>
                <h1>Hang Tight</h1>
                <h3>'.$rand.' - '.$action.' - '.$this->action.'</h3>
                <br><br>
                <pre>
                    '.$this->toPrint.'
                </pre>
            </body>
        </html>
        ';
        
        //$this->viewPost();
    }
    
    
    
    private function logAction($action, $userID = 0, $profileID = 0, $postID = 0, $commentID = 0)
    {
        $fp = fopen('controllers/botRessources/botLogs.csv', 'a+');

        $this->action = $action;

        fputcsv($fp, [date("Y/M/D H:m:s", time()), $userID, $profileID, $postID, $commentID, $action]);

        fclose($fp);
    }

    private function addPost()
    {
        $pID = $this->getProfile();
        
        if($pID == null)
            return;
        
        // initialise the curl request
        $request = curl_init('localhost/eikona/do/post/create/');

        $rand = mt_rand(1, 4);

        $cfile = new CURLFile('controllers/botRessources/'.$rand.'.jpg');
        
        $strCookie = 'PHPSESSID=' . $_COOKIE['PHPSESSID'] . '; path=/';
        
        session_write_close();
        
        // send a file
        curl_setopt($request, CURLOPT_POST, true);
        curl_setopt($request, CURLOPT_AUTOREFERER,         true); 
        curl_setopt($request, CURLOPT_COOKIESESSION,         true); 
        curl_setopt($request, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($request, CURLOPT_COOKIE, $strCookie ); 
        curl_setopt($request, CURLOPT_FAILONERROR,         false); 
        curl_setopt($request, CURLOPT_FOLLOWLOCATION,        false); 
        curl_setopt($request, CURLOPT_USERAGENT, $_SERVER['HTTP_USER_AGENT']);
        curl_setopt(
            $request,
            CURLOPT_POSTFIELDS,
            array(
              'img' => $cfile
            ));

        // output the response
        curl_setopt($request, CURLOPT_RETURNTRANSFER, true);
        $rep = curl_exec($request);

        curl_close($request);
        
        //var_dump($rep);

        $rep = json_decode($rep, true);

        $postID = $rep['data']['postID'];

        $postController = new PostController();

        ob_start();

        $postController->setFilter($postID, FiltR::$availableFilters[mt_rand(0, count(FiltR::$availableFilters) - 1)]);
        $postController->publish($postID);

        /*$this->toPrint .= */ob_get_clean();


        $this->logAction("Post added", 0, $pID, $postID);
    }
    
    private function addComment()
    {
        $pID = $this->getProfile();
        
        if($pID == null)
            return;
        
        $stmt = $this->cnx->prepare("SELECT post_id FROM posts ORDER BY RAND() LIMIT 1");
        $stmt->execute();
        
        $postID = $stmt->fetchColumn();
        
        $msg = "comment---".substr(md5(time()), 0, 16);
        
        $commentModel = new CommentModel();
        $commentModel->create($pID, $postID, $msg);
        
        $this->logAction("Comment added", 0, $pID, $postID);
        
        //$this->viewPost($postID);
        
    }
    
    private function addLike()
    {
        $pID = $this->getProfile();
        
        if($pID == null)
            return;
            
        $stmt = $this->cnx->prepare("SELECT posts.post_id FROM posts ORDER BY RAND() LIMIT 3");
        $stmt->execute([":pID" => $pID]);
        
        
        while($postID = $stmt->fetchColumn())
        {
            $LikeModel = new LikeModel();
            $LikeModel->like($postID, $pID);
            
            $this->logAction("Post liked", 0, $pID, $postID);
            
            //$this->viewPost($postID);
        }
        
    }
    
    private function viewPost($postID = 0)
    {
        $pID = $this->getProfile();
        
        if($pID == null)
            return;
            
        if($postID != 0)
        {
            $stmt = $this->cnx->prepare("INSERT INTO post_views(profile_id, post_id, view_time) VALUES (:profile, :post, :time)");
            $stmt->execute([":profile" => $pID,
        	                ":post" => $postID,
                            ":time" => time()]);
        
            $this->logAction("Post viewed", 0, $pID, $postID);  
        
            return;
        }
        
        $stmt = $this->cnx->prepare("SELECT post_id FROM posts ORDER BY RAND() LIMIT 10");
        $stmt->execute();

        while($postID = $stmt->fetchColumn())
        {
            //ob_start();
            $postViewModel = new PostViewModel();
            $postViewModel->view($pID, $postID);
            //ob_end_clean();
            $this->logAction("Post viewed", 0, $pID, $postID);  
        }
    }
    
    private function follow()
    {
        $pID = $this->getProfile();

        $stmt = $this->cnx->prepare("SELECT profile_id FROM profiles WHERE profile_id <> :profile ORDER BY RAND() LIMIT 1");
        $stmt->execute([":profile" => $pID]);

        $toFollow = $stmt->fetchColumn();

        if($toFollow == NULL)
            return;

        $followModel = new FollowModel();
        $followModel->follow($toFollow, false);

        $this->logAction("Profile Followed", 0, $pID);
    }

    
    
    
    
    
    
    
    
    
    




    private function getprofile()
    {
        //Get a user and log in
        $rand = mt_rand(1, 5);

        if($rand == 1)
        {
            //create a new user
            $user = $this->addUser();
        }
        else
        {
            //Get an existing one
            $stmt = $this->cnx->prepare("SELECT user_id, user_name, user_email FROM users ORDER BY RAND() LIMIT 1");
            $stmt->execute();
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if(!isset($user["user_id"]))
            {
                //No user in DB, create one
                $user = $this->addUser();
            }
        }

        //Log in with user
        Cookie::delete("stayConnected");
        Session::write("userID", $user['user_id']);

        $profileModel = new ProfileModel();

        //No profiles, create one
        if(!$profileModel->hasProfiles($user['user_id']))
        {
            $profileID = $this->addProfile($user['user_id']);
        }
        else
        {
            $rand = mt_rand(1, 5);

            if($rand == 1 && !$profileModel->tooMuchProfiles($user['user_id']))
            {
                $profileID = $this->addProfile($user['user_id']);
            }
            else
            {
                $stmt = $this->cnx->prepare("SELECT profile_id FROM profiles WHERE user_id = :uID ORDER BY RAND() LIMIT 1");
                $stmt->execute([":uID" => $user['user_id']]);
                $profileID = $stmt->fetchColumn();
            }
        }

        //set current profile
        Session::write("profileID", $profileID);

        return $profileID;
    }

    //Create a new User and return its login credentials
    private function addUser()
    {
        $authModel = new AuthModel();
        
        $uName = substr(md5(time()), 0, 16);
        $uMail = $uName."@bot.com";
        $uPasswd = $uName;
        
        if(!$authModel->isUnique($uMail))
        {
            sleep(1);
            $uName = substr(md5(time()), 0, 16);
            $uMail = $uName."@bot.com";
            $uPasswd = $uName;
        }
        
        $_POST['user_name'] = $uName;
        $_POST['user_email'] = $uMail;
        $_POST['user_passwd'] = $uPasswd;
        $_POST['user_passwd_confirm'] = $uPasswd;
        
        $uID = $authModel->addUser($uName, $uMail, $uPasswd, time());

        $this->logAction("User created", $uID);

        return ["user_id" => $uID, "user_name" => $uName, "user_email" => $uMail];
    }
    
    private function addProfile($userID)
    {
        $profileName = "profile".substr(md5(time()), 0, 16);
        
        $_POST['profileName'] = $profileName;

        $profileModel = new ProfileModel();
        $response = $profileModel->create($userID, $profileName, "", false);
        
        $profileKey = $profileModel->getKey();

        $root = $_SERVER['DOCUMENT_ROOT']."/Eikona/app/medias/img/";
        mkdir($root."/".$profileKey);

        if($response === "userNameAlreadyExists")
        {
            sleep(1);
            return $this->addProfile($userID);
        }
        
        $profileID = $response;

        $this->logAction("Profile created", $userID, $profileID);

        return $profileID;
    }
}
