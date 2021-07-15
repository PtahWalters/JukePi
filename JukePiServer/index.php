<?php
include "configuration.php";

use Configuration\DotEnv;
(new DotEnv(__DIR__ . '/.env'))->load();

class Jukebox {
    function __construct($beemusername, $beempassword, $dbpass, $dbuser, $dbhost, $admin_contact, $dbname) {
        $this->username = $beemusername; 
        $this->password = $beempassword;
        $this->dbusername = $dbuser; 
        $this->dbpassword = $dbpass;
        $this->dbhost = $dbhost;
        $this->admin = $admin_contact;
        $this->database = $dbname;
      }

    function db(){
        // Create database connection
        $conn = new mysqli($this->dbhost, $this->dbusername, $this->dbpassword, $this->database);
        // Check connection
        if ($conn->connect_error):
            die("Connection failed: " . $conn->connect_error);
        else:
            return $conn;
        endif;
    }

    function checkBalance(){
        $Url ='https://apisms.beem.africa/public/v1/vendors/balance';

        $ch = curl_init($Url);
        error_reporting(E_ALL);
        ini_set('display_errors', 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_HTTPGET => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("$this->username:$this->password"),
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($ch);

        if($response === FALSE){
            //TODO: Log and notify administrator
            echo $response;
            die(curl_error($ch));
        }
        
        $response_arr = json_decode($response);
        
        return (int)$response_arr->data->credit_balance;
    }

    function sendMessage($message, $contact){
        if($this->checkBalance() <= 5 ){
            //TODO: Log and notify administrator
           die("Sorry, not enough credits to perfom transaction");
           
        }

        $postData = array(
            'source_addr' => 'INFO',
            'encoding'=>0,
            'schedule_time' => '',
            'message' => $message,
            'recipients' => [array('recipient_id' => '1','dest_addr'=>$contact)]
        );
        
        $Url ='https://apisms.beem.africa/v1/send';
        
        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt_array($ch, array(
            CURLOPT_POST => TRUE,
            CURLOPT_RETURNTRANSFER => TRUE,
            CURLOPT_HTTPHEADER => array(
                'Authorization:Basic ' . base64_encode("$this->username:$this->password"),
                'Content-Type: application/json'
            ),
            CURLOPT_POSTFIELDS => json_encode($postData)
        ));
        
        $response = curl_exec($ch);
        
        if($response === FALSE){
            //TODO: Log and notify administrator
            echo $response;
            die(curl_error($ch));
        }
    }

    function AcceptRequest(){
        //Get SMS
        //REMOVE BEEM TEXT
        //FORMAT SEARCH

        $search_term = "alaine%20victory";

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => "https://shazam.p.rapidapi.com/search?term=".$search_term."&locale=en-US&offset=0&limit=1",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => [
                "x-rapidapi-host: shazam.p.rapidapi.com",
                "x-rapidapi-key: 5iGbfTYfRImshR9MjyDvox16Dr1Zp1ej889jsnvitviN8VzK0y"
            ],
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            echo "cURL Error #:" . $err;
        } else {
            $resArr = json_decode($response);
            $song = $resArr->tracks->hits[0]->track->hub->actions[1]->uri;

            if (filter_var($song, FILTER_VALIDATE_URL) === FALSE) {
                //song not found
                $this->RespondErr("255715190934");
            }else{
                //song found, add to queue
                $db =& $this->db();
                $query = "INSERT INTO jukebox_queue VALUES(NULL, '$song')";

                if ($db->query($query) === TRUE) {
                    $this->RespondSuccess("255715190934");
                }else{
                    //TODO: Log and notify administrator
                    echo  "something went wrong with the request";
                    echo $db->error;
                }
            }
            $db->close();
        }
    }

    function RespondSuccess($phone_number){
        $message = "We have received your requsest and added it in the queue, get your dancing shoes on and get ready to break a leg :)";
        $this->sendMessage($message, $phone_number);
    }

    function RespondErr($phone_number){
        $message = "Sorry :(, we could not find your request in our massive music library, please try another request";
        $this->sendMessage($message, $phone_number);
    }

    function FetchSong(){
        $db =& $this->db();
        $query = "SELECT id,uri FROM jukebox_queue ORDER BY id ASC LIMIT 1";
        $result = $db->query($query);

        if ($result->num_rows > 0) {
            // output data of each row
            while($row = $result->fetch_assoc()) {
                echo $row['uri'];
                //Update queue
                $delID = $row['id'];
            }
        } else {
            echo "0";
        }
        
        if ($db->query("DELETE FROM jukebox_queue WHERE id = ".$delID) === TRUE):
            $db->close();
        endif;    
    }

}

$jukebox = new Jukebox(getenv('BEEM_API_KEY'), getenv('BEEM_API_PASSWORD'), getenv('DATABASE_PASSWORD'), getenv('DATABASE_USER'), getenv('DATABASE_HOST'), getenv('ADMIN_CONTACTS'), getenv('DATABASE'));

if ( $_GET['task'] != "" ): 
    $method = trim($_GET['task']);
    if ( method_exists($jukebox, $method) ):
        $jukebox->{$method}();
    else:
        die("Error: could not process your request.");
    endif;
else:
    $jukebox->AcceptRequest();
endif;
