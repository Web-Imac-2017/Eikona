<?php

class BotController extends DBInterface
{   
    private $currUserID;
    private $currProfileID;
    
    private $action;
    
    public function __construct()
    {
        parent::__construct();   
    }
    
    
    public function __call($method, $args)
    {
        $this->index($method);
    }


    public function index($do = null)
    {
        Cookie::delete("stayConnected");

        $actions = ["addUser", "addProfile", "addPost", "addComment", "addLike", "viewPost"];
        
        if(in_array($do, $actions))
        {
            $this->$do();
        }
        else
        {
            $rand = mt_rand(0, 100);

            if($rand < 3)
                $this->addUser();
            else if($rand < 7)
                $this->addProfile();
            else if($rand < 30)
                $this->addPost();
            else if($rand < 70)
                $this->viewPost();
            else if($rand < 90)
                $this->addLike();
            else
                $this->addComment();

        }
            header('Content-Type: text/html');
            http_response_code(200);
            echo '
            <html>
                <head>
                    <title>BotController - Hang Tight</title>
                    <meta http-equiv="refresh" content="2">
                </head>
                <body>
                    <h1>Hang Tight</h1>
                    <h3>'.$rand.' - '.$this->action.'</h3>
                </body>
            </html>
            ';
        
        //$this->viewPost();
    }
    
    
    
    private function logAction($action, $userID = 0, $profileID = 0, $postID = 0, $commentID = 0)
    {
        $fp = fopen('controllers/botRessources/botLogs.csv', 'a');

        $this->action = $action;

        fputcsv($fp, [date("Y/M/D H:m:s", time()), $userID, $profileID, $postID, $commentID, $action]);

        fclose($fp);
    }
    
    
    
    
    
    private function addUser()
    {
        $uName = substr(md5(time()), 0, 16);
        $uMail = $uName."@bot.com";
        $uPasswd = $uName;
                        
        $_POST['user_name'] = $uName;
        $_POST['user_email'] = $uMail;
        $_POST['user_passwd'] = $uPasswd;
        $_POST['user_passwd_confirm'] = $uPasswd;
        
        $uID = Response::read("auth", "register")['data']['userID'];
        $this->logAction("User created", $uID);
        
        $this->addProfile();
    }
    
    private function addProfile($userID = 0)
    {
        $uID = $this->login($userID);
            
        $profileName = "profile".substr(md5(time()), 0, 16);
        
        $_POST['profileName'] = $profileName;
        
        $response = Response::read("profile", "create");

        if($response['code'] != 201 && $response['code'] != 409)
            print_r($response);

        if($response['code'] == 409)
        {
            sleep(1);
            return $this->addProfile();
        }


        $profileID = $response['data']['profileID'];
        $this->logAction("Profile created", $uID, $profileID);
        
        return $profileID;
    }
    
    private function addPost()
    {
        $pID = $this->getProfile();
        
        if($pID == null)
            return;
        
        // initialise the curl request
        $request = curl_init('localhost/eikona/do/post/create/');

        $cfile = new CURLFile('controllers/botRessources/face.jpg');
        
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
        $rep = json_decode(curl_exec($request), true);
        
        //print_r($rep);
        // close the session
        curl_close($request);
        
        $post = new PostModel();
        $post->publish($rep['data']['postID']);

        $this->logAction("Post added", 0, $pID, $rep['data']['postID']);
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
        
        $this->viewPost($postID);
        
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
            
            $this->viewPost($postID);
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
    
    
    
    
    
    
    
    
    
    
    
    private function login($userID = 0)
    {
        if($userID == 0)
        {
            $stmt = $this->cnx->prepare("SELECT user_id, user_name, user_email FROM users ORDER BY RAND() LIMIT 1");
            $stmt->execute();
        }
        else
        {
            $stmt = $this->cnx->prepare("SELECT user_id, user_name, user_email FROM users WHERE user_id = :uID");
            $stmt->execute([":uID" => $userID]);
        }
        
        $user = $stmt->fetch();
        
        $_POST['user_email'] = $user['user_email'];
        $_POST['user_passwd'] = $user['user_name'];
        
        Response::read("auth", "signIn");
        
        return $user['user_id'];
    }
    
    private function getprofile()
    {
        $uID = $this->login();
        
        $stmt = $this->cnx->prepare("SELECT profile_id FROM profiles WHERE user_id = :uID ORDER BY RAND() LIMIT 1");
        $stmt->execute([":uID" => $uID]);
        
        $profileID = $stmt->fetchColumn();

        if($profileID === false)
        {
            $profileID = $this->addProfile();
        }
        
        Session::write("profileID", $profileID);

        return $profileID;
    }
}
