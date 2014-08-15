<?php

class ApiController extends CController
{

        private $app_id;
        
        private $user_id;
        
        private $method = array(
                'authenticate',
                'saveKey',
                'getKey',
                'deleteKey'
        );
        
        /**
         * Handles the case of an error occuring in the $methods array
         * Involked by any call to an non-existant function within the Object
         * @param string $method - Method that was desired to be called
         * @param array $args    - Arguments passed to the method
         * @see APIController::endScript()
         **/
        public function __call($method, $args)
        {
                return $this->endScriptWithCode(8, 1);
        }
        
        /**
         * Handles successfuly responses
         * Involked whenever a request is successfuly and has been fulfilled
         * @param array $response - The data to be returned
         **/
        private function endScriptWithResponse(array $data)
        {
                $response = array(
                        'response'=>array(
                                'code'=>1,
                                'data'=>$data
                        )
                );
                
		// Terminate the script
                $this->endScript($response, 1);
        }
        
        /**
         * Handles the simple method of erroring out with a response code
         * Involked when the API only needs to return a response code back to the developer
         * @param interger $code        - The error code to report in the response
         * @param integer $exitCode     - The exit code to pass to endScript
         * @return @see APIController::endScript()
         **/
        private function endScriptWithCode($code, $exitCode=0)
        {
                $response = array(
                        'response'=>array(
                                'code'=>$code
                        )
                );
                
                return $this->endScript($response, $exitCode);
        }
        
	/**
	 * Handles proper exiting of the API as a wrapper for exit($status)
	 * This action can be called by any function wanting to terminate futher API action and return to the programmer
	 * @param array $response - The json encoded array to return to the progrmamer
	 * @param integer $code   - The response code to error out with, defaults to normal exit
	 * @return exit()
	 **/
	private function endScript(array $response, $code=0)
	{
		echo json_encode($response);

		exit($code);
	}
	
	/**
  	 * Checks to make sure the API is enabled
  	 * This method is called before every request to the API
	 * @return array response
	 **/
	private function beforeRequest()
	{
		// If the API is disabled, return a proper response code and kill the script
		if (Configuration::model()->findByPk(1)->value == 0)	// API_ENABLED
		{
			$this->endScriptWithCode(100);
		}
	}

	/**
	 * Automates beforeRequest for bootstrapping
	 * This method is involked before any action is called
	 * @param string $actionID - The requested action
	 * @see APIController::beforeRequest()
	 **/
	protected function beforeAction($actionID)
	{
		$this->beforeRequest();
		return true;
	}

	/**
	 * Handles possible API abuse
         * This method is invoked when the controller cannot find the requested action.
         * When involked, this action returns an invalid api url error code
         * @param string $actionID - The missing action name
         * @param integer $code    - Default status code to return
         * @return APIController::endScript()
         */
	public function missingAction($actionID, $code=2)
	{
		$this->endScriptWithCode($code);
	}
	
	/**
	 * Bootstrapped entry point for the API
	 *
	 *
	 *
	 **/
	public function actionIndex()
	{      
	        // Run the proper verifications needs to allow access to the requested API component
	        $this->verification($_POST);
	        
	        // Update the API Request Count
	        $this->updateRequestCount($_POST['request']['APIKey']);
	        
	        // Assumming all verification was successful, we can then run the developer's request and pass the 'data' params to that method
	        // If somehow the method bypasses the above checks, __call will override the method and return invalid method
	        // Methods should return $this->endScript... of some kind to prevent an error from occuring.
	        $this->$_POST['request']['method']($_POST['request']['data']);
	        
	        // We should never reach this point, but if we do, indicate the request was successful, but that it errored out.
	        $this->endScriptWithCode(9);
	}
	
	
	// --------------------------------------------------------------------------------
	// All subsequent methods are called from actionIndex()
	// --------------------------------------------------------------------------------
	
	private function verification($post)
	{       
	        // Verify the the request has the proper items
	        $this->verifyInitialRequest($post);
	        
	        // Verify the API key is permitted to use the API
                $this->verifyAPIKey($post['request']['APIKey']);
                                
	        // Verify the method is valid
	        $this->verifyMethod($post['request']['method']);
                
                // Verify that user credentials and tokens
                $this->verifyAuthentication($post['request']);
	}
	
	/**
	 * Checks to make sure that at minimum everything is set and not empty
	 * Involked by APIController::verification()
	 * @return @see APIController::endScript()
	 */
	private function verifyInitialRequest(array $post)
	{	        
	        // Conditional branching to verify that all the 'basics' are at least initialized with some data
	        // In order for the request not to result in an error, the if branch must end at the nested return bracket
	        if (isset($post['request']))
	        {
	                // Verify the APIKey is set
	                if (isset($post['request']['APIKey']) && $post['request']['APIKey'] != NULL)
	                {
	                        // Verify the data is at least there
	                        if (isset($post['request']['data']) && !empty($post['request']['data']))
	                        {
	                                // Verify either a token or an authentication request
	                                if ((isset($post['request']['token']) && $post['request']['token'] != NULL) || (isset($post['request']['data']['email']) && isset($post['request']['data']['password'])))
	                                {
	                                        // Verify a method is set
	                                        if (isset($post['request']['method']) && $post['request']['method'] != NULL)
	                                        {
	                                                return;
	                                        }
	                                }
	                        }
	                }
	        }
	        
	        // If we hit this point, bail with an error code
	        return $this->endScriptWithCode(7);
	}
	
	/**
	 * Verifies that the API key passes exists and can be used
	 * Involked by APIController::verification()
	 * @param string $key - The API key to verify
	 **/
	private function verifyAPIKey($key)
        {
                // Verify the API Key exists in the database via count
                $APIKeys = new APIKeys();
                $count = $APIKeys->countByAttributes(array('api_key'=>$key));
                
                
                // If we do not find the API key in the database, terminate the request
                if ($count == 0)
                {
                        $this->endScriptWithCode(6);
                }
                
                // Retrieve the APIKey Data
                $response = $APIKeys->findByAttributes(array('api_key'=>$key));
                
                // Return the developer key status_id if it is not valid
                if ($response->attributes['api_status'] != 1)
                {
                        $this->endScriptWithCode($response->attributes['api_status']);
                }
                
                $this->app_id = $response->attributes['app_id'];
        }
        
        /**
	 * Verifies that the requested method is valid, exists, and ca be used
	 * Involked by APIController::verification()
	 * @param string $method - The requested method to be called
	 **/
	private function verifyMethod($method)
	{
	        if (!in_array($method, $this->method) || !method_exists('ApiController', $method))
	        {
	                $this->endWithScriptCode(8);
	        }
	        
	}
        
        /**
         * Handles authentication for the user
         * If a token is provided through the API, this method will verify that the token is active and can be used.
         * If the token is invalid, or authentication credentials have been provided, this method will attempt to create a token to return
         * To the developer
         * Involked by APIController::verification()
         * @param array $request - The full POST requst
         */
        private function verifyAuthentication(array $request)
        {
                // If we have a token passes
                if (isset($request['token']) && $request['token'] != NULL)
                {
                        $token = new Tokens();
                        $count = $token->countByAttributes(array('token'=>$request['token']));
                        
                        // If no tokens were found, return an error
                        if ($count == 0)
                        {
                                $this->endScriptWithCode(10);
                        }
                        else
                        {
                                $response = $token->findByAttributes(array('token'=>$request['token']));
                                
                                $datetime1 = new DateTime('now');
                                $datetime2 = new DateTime($response->attributes['created']);
                                $interval = $datetime1->diff($datetime2);

                                // Tokens are valid for 30 days. If the token is older than 30 days, consider it to be expired
                                if (($interval->m*30 + $interval->d) > 30)
                                {
                                        $this->endScriptWithCode(11);
                                }
                                
                                $this->user_id = $response->attributes['user_id'];
                                
                                // If we reach this point, then our token is vaid and we can proceed.
                                // Terminate execution of this method
                                return;
                        }
                }
                
                // Attempt to Authenticate the user
                if ($request['method'] == 'authenticate')
                {
                        $this->authenticate($request['data']);
                }
                
                // If the request was not to authenticate the user, then return an authentication error
                $this->endScriptWithCode(12);
        }
        
        /**
         * Verifies the parameter is set, not null, and has a non empty value
         * Involked as needed by other functions that required parameter verification
         * @param string $param - The parameter to check
         **/
        private function verifyParam($param, $checkType=false)
        {
                if ($param == NULL || $param == '')
                {
                        $this->endScriptWithCode(21);
                }
                
                if ($checkType)
                {
                        if (!is_string($param))
                        {
                                $this->endScriptWithCode(20);
                        }
                }
        }
        
        /**
         * Authenticate the user for use with the API, and generated an API token for the developer to use
         * Involked by APIController::verifyAuthentication
         * @param array $data - The data to be authenticated
         **/
        private function authenticate(array $data)
        {
                // Verify the necessary fields are set and not empty
                $this->verifyParam($data['email']);
                $this->verifyParam($data['password']);
                
                $user = new Users();
                
                $encryptionKey = Yii::app()->params['encryptionKey'];
                
                // Verify the authentication tokens
                $count = $user->countByAttributes(
                        array(
                                'email'=>$data['email'],
                                'password'=>$user->_encryptHash($data['email'], $data['password'], $encryptionKey)
                        )
                );
                
                if ($count == 1)
                {
                        $response = $user->findByAttributes(
                                array(
                                        'email'=>$data['email'],
                                        'password'=>$user->_encryptHash($data['email'], $data['password'], $encryptionKey)
                                )
                        );
                        
                        $token = new Tokens();
                        $token->attributes = array(
                                'user_id'=>$response->attributes['id'],
                                'token'=>$token->generateToken($response->attributes['id'], $encryptionKey)
                        );
                        
                        // If the token successfully saves, exit with a successful response code
                        if($token->save())
                        {
                                $response = array(
                                        'token'=>$token->attributes['token']
                                );
                                
                                $this->endScriptWithResponse($response);
                        }
                }
                
                // Exit with an error code if anything else happens
                $this->endScriptWithCode(21);
        }

        /**
         * Updates the number of requests made to by the API
         * Involked on each successfuly verification request by APIController::actionIndex()
         * @param string $key - The API Key to Update
         **/
        private function updateRequestCount($key)
        {
                // Completely bypass the model loading for performance, and run the query via PDO::DAO
                $connection=Yii::app()->db;  
                $sql = 'UPDATE api_keys SET request_count = (request_count+1) WHERE api_key = :api_key';
                $command=$connection->createCommand($sql);
                $command->bindParam(':api_key',$key,PDO::PARAM_STR);
                $command->execute();
        }
        
        // --------------------------------------------------------------------------------
	// All subsequent methods are callable API methods
	// --------------------------------------------------------------------------------
        
        /**
         * Handles the the creation of new records and updating of current records
         * Involked via client
         * @param array $data - The data to be processed
         **/
        private function saveKey($data)
        {
                
                $this->verifyParam($data['key']);
                $this->verifyParam($data['value'], true);
                
                
                // Generated the hashed key
                $key = mb_strimwidth(hash('sha512', md5($this->app_id) . md5($this->user_id) . md5($data['key'])), 0, 64);

                $connection=Yii::app()->db;  
                $sql = 'INSERT INTO kv_data (`key`, `app_id`, `user_id`, `value`, `created`, `updated`) VALUES (:key, :app_id, :user_id, :value, NOW(), NOW()) ON DUPLICATE KEY UPDATE value = :value, updated = NOW()';
                $command=$connection->createCommand($sql);
                $command->bindParam(':key',$key,PDO::PARAM_STR);
                $command->bindParam(':value',$data['value'],PDO::PARAM_STR);
                $command->bindParam(':app_id',$this->app_id,PDO::PARAM_STR);
                $command->bindParam(':user_id',$this->user_id,PDO::PARAM_STR);
                $rowsAffected = $command->execute();
                
                if ($rowsAffected == 1 || $rowsAffected == 2)  // Insert OR Update
                {
                        $response = array(
                                'key'=>$key
                        );
                        
                        $this->endScriptWithResponse($response);
                }            
                
                $this->endScriptWithCode(20);
        }
        
        /**
         * Handles reading a key for use later by the client
         * Involked via client
         * @param array $data - The data to be processed
         **/
        private function getKey($data)
        {
                $this->verifyParam($data['key']);
                
                $kv = new KVData();
                
                // Generated the hashed key
                $key = mb_strimwidth(hash('sha512', md5($this->app_id) . md5($this->user_id) . md5($data['key'])), 0, 64);
                
                // Erase all other data
                unset($data);
                
                $count = $kv->countByAttributes(array('key'=>$key, 'app_id'=>$this->app_id, 'user_id'=>$this->user_id));
                
                if ($count == 1)
                {
                        $results = $kv->findByAttributes(array('key'=>$key, 'app_id'=>$this->app_id, 'user_id'=>$this->user_id));
                        
                        $response = array(
                                'value'=>$results->attributes['value'],
                                'created'=>$results->attributes['created'],
                                'updated'=>$results->attributes['updated']
                        );
                        
                        $this->endScriptWithResponse($response);
                }
                
                $this->endScriptWithCode(12);
        }
        
        /**
         *
         *
         *
         **/
        private function deleteKey($data)
        {
                $this->verifyParam($data['key']);
                
                $kv = new KVData();
                
                // Generated the hashed key
                $key = mb_strimwidth(hash('sha512', md5($this->app_id) . md5($this->user_id) . md5($data['key'])), 0, 64);
                
                // Erase all other data
                unset($data);
                
                $count = $kv->countByAttributes(array('key'=>$key, 'app_id'=>$this->app_id, 'user_id'=>$this->user_id));
                
                if ($count == 1)
                {
                        $kv->deleteByPk($key);
                        
                        $this->endScriptWithCode(1);
                }
                
                $this->endScriptWithCode(12);
        }
        
        /**
         * API debugging tool
         * @param object:array $debug - Data to be debugged cleanly
         * @return print_r 
         **/
        private function debug($debug)
        {
                print_r($debug);
        }

}

?>
