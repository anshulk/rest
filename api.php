<?php
	require_once("rest.php");
	
	class API extends REST {
	
		public $data = "";
		
		const DB_SERVER = "localhost";
		const DB_USER = "root";
		const DB_PASSWORD = "";
		const DB = "restful";
		
		private $db = NULL;
	
		public function __construct(){
			parent::__construct();				
			$this->dbConnect();					
		}
		
        private function dbConnect(){
			$this->db = mysql_connect(self::DB_SERVER,self::DB_USER,self::DB_PASSWORD);
			if($this->db)
				mysql_select_db(self::DB,$this->db);
		}
		
		public function processApi(){
			$func = strtolower(trim(str_replace("/","",$_REQUEST['q'])));
			if((int)method_exists($this,$func) > 0)
				$this->$func();
			else
				$this->response('',404);				// If the method not exist with in this class, response would be "Page not found".
		}
		
        // User functions 
        
        private function insertUser(){
            if($this->get_request_method() != "POST"){
                $this->response('', 406);
            }
            
            $email = $this->_request['email'];
            $name = $this->_request['name'];
            $pwd = $this->_request['pwd'];
            $sql = "INSERT INTO `users` (`user_id`, `user_fullname`, `user_email`, `user_password`) VALUES (NULL, '$name', '$email', '$pwd') ON DUPLICATE KEY UPDATE user_fullname = '$name', user_email='$email', user_password = '$pwd' ;";
            if(mysql_query($sql))
            {
                $success = array('status' => "Success", "msg" => "Successfully inserted User");
				$this->response(json_encode($success),200);                
            }
            else
            {
                $error = array('status' => "Failed", "msg" => mysql_error());
			    $this->response(json_encode($error), 417);
            }
                
        }
        
        private function updateUser(){
            if($this->get_request_method() != "PUT"){
                $this->response('', 406);
            }
            
            $email = $this->_request['email'];
            $name = $this->_request['name'];
            $pwd = $this->_request['pwd'];
            $id = $this->_request['id'];
            
            $sql = "INSERT INTO `users` (`user_id`, `user_fullname`, `user_email`, `user_password`) VALUES ($id, '$name', '$email', '$pwd') ON DUPLICATE KEY UPDATE user_fullname = '$name', user_email='$email', user_password = '$pwd' ;";
            if(mysql_query($sql))
            {
                $success = array('status' => "Success", "msg" => "Successfully updated User with ID $id");
				$this->response(json_encode($success),200);                
            }
            else
            {
                $error = array('status' => "Failed", "msg" => mysql_error());
			    $this->response(json_encode($error), 417);
            }
                
        }
        
        private function userDetails(){	
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
            $id = (int)$this->_request['id'];
            if($id > 0){				
				$sql = mysql_query("SELECT user_id, user_fullname, user_email FROM users WHERE user_id = $id");
				$user = mysql_fetch_array($sql, MYSQL_ASSOC);
				$this->response(json_encode($user),200);
			}else
				$this->response('',204);	
		
			$this->response('',204);	
		}
		
		private function deleteUser(){
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				mysql_query("DELETE FROM users WHERE user_id = $id");
				$success = array('status' => "Success", "msg" => "Successfully deleted user with UserID $id / User didn't exist");
				$this->response(json_encode($success),200);
			}else
				$this->response('',204);	
		}
        
        // Order functions
        
        private function insertOrder(){
            if($this->get_request_method() != "POST"){
                $this->response('', 406);
            }
            
            $res = $this->_request['resid'];
            $details = $this->_request['det'];
            $sql = "INSERT INTO `orders` (`order_id`, `res_id`, `details`) VALUES (NULL, '$res', '$details') ON DUPLICATE KEY UPDATE res_id = '$res', details='$details';";
            if(mysql_query($sql))
            {
                $success = array('status' => "Success", "msg" => "Successfully inserted order");
				$this->response(json_encode($success),200);                
            }
            else
            {
                $error = array('status' => "Failed", "msg" => mysql_error());
			    $this->response(json_encode($error), 417);
            }
                
        }
        
        private function UpdateOrder(){
            if($this->get_request_method() != "PUT"){
                $this->response('', 406);
            }
            
            $res = $this->_request['resid'];
            $details = $this->_request['det'];
            $id = $this->_request['id'];
            $sql = "INSERT INTO `orders` (`order_id`, `res_id`, `details`) VALUES ($id, '$res', '$details') ON DUPLICATE KEY UPDATE res_id = '$res', details='$details';";
            if(mysql_query($sql))
            {
                $success = array('status' => "Success", "msg" => "Successfully Updated order with ID $id");
				$this->response(json_encode($success),200);                
            }
            else
            {
                $error = array('status' => "Failed", "msg" => mysql_error());
			    $this->response(json_encode($error), 417);
            }
        }
        
        private function orderDetails(){	
			
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}
            $id = (int)$this->_request['id'];
            if($id > 0){				
				$sql = mysql_query("SELECT order_id, res_id, details FROM orders WHERE order_id = $id");
				$user = mysql_fetch_array($sql, MYSQL_ASSOC);
				$this->response(json_encode($user),200);
			}else
				$this->response('',204);	
		
			$this->response('',204);	
		}
		
		private function deleteOrder(){
			
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){				
				mysql_query("DELETE FROM orders WHERE user_id = $id");
				$success = array('status' => "Success", "msg" => "Successfully deleted order with OrderID $id / Order didn't exist");
				$this->response(json_encode($success),200);
			}else
				$this->response('',204);	
		}		
	}
	
	$api = new API;
	$api->processApi();
?>