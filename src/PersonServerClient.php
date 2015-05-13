<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Jithin
 * Date: 8/4/14
 * Time: 2:10 PM
 * To change this template use File | Settings | File Templates.
 */

class PersonServerHandler {
    private $handler = false;
    private $error   = true;
    private $error_message = "";



    function __construct(){


        try{
            $this->handler = socket_create(AF_INET, SOCK_STREAM, 0);
            if(!$this->handler){
                $this->error = false;
                $this->error_message = socket_strerror(socket_last_error());
            }
        }
        catch(Exception $e){
            $this->error = false;
            $this->error = $e->getMessage();
        }
    }

    function openConnection(dbConfig $config){
        try{
            if($this->error){
                if(!socket_connect($this->handler, $config->PERSON_IP,$config->PERSON_SERVER_PORT)){
                    $this->error = false;
                }
                else{
                    return 1;
                }

            }
            else{
                $this->error = false;
                return 0;
            }
        }
        catch(Exception $e){
            $this->error = false;
            return 0;
        }
        return 0;
    }

    function __destruct(){
        socket_close($this->handler);
    }

    public function getData($email){
        if($this->error){
        try{
            if(!socket_send($this->handler, $email, strlen($email), 0)){
                $this->error_message = $this->error_message = socket_strerror(socket_last_error());
                return json_encode(array("CODE"=>0, "ERROR"=>"Error in Querying the Server","ERROR_CODE"=>506));

            }
            if(socket_recv($this->handler, $result, 2045, MSG_WAITALL) === false){
                $this->error_message = $this->error_message = socket_strerror(socket_last_error());
                return json_encode(array("CODE"=>0, "ERROR"=>"Person Server Error", "ERROR_CODE"=>500));
            }

            $result_array  = json_decode($result, true);
            return json_encode(array("CODE"=>1, "RESULT"=>$result_array));
        }
        catch(Exception $e){
            return json_encode(array("CODE"=>0, "ERROR"=>$e->getMessage(), "ERROR_CODE"=>$e->getCode()));
        }
    }
        else{
            return json_encode(array("CODE"=>0, "ERROR"=>"Server Error", "ERROR_CODE"=>500));
        }

    }
}