<?php

namespace SistemaTique\API;

use PHPUnit\Exception;
use SistemaTique\API\BaseController\BaseController;
use SistemaTique\Helpers\FormVerifier;
use SistemaTique\Helpers\Helpers;
use SistemaTique\Helpers\NewLogger;
use SistemaTique\Mvc\Models\User;

/* It receives a request, validates the data, creates a new user and returns a JSON response */
class UserController extends BaseController
{
    /**
     * It gets all the users from the database if no rut is provided, otherwise it gets the user with
     * the provided rut
     * 
     * @param rut The rut of the user you want to get.
     */
    public function get( $rut = null)
    {
        if( $_SERVER['REQUEST_METHOD'] === 'GET' ){
            try {
                $userModel = new User();
                if( !isset($rut) ) {
                    $responseData = json_encode($userModel->getAll(), JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                }else {
                    $userModel->setRut($rut);
                    $userData = $userModel->getOneByRut();

                    if( $userData ) {
                        $responseData = json_encode($userModel->getOneByRut(),JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                    }else {
                        $responseData = json_encode(['status' => 'OK', 'message' => "The user with rut $rut does not exists"], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE);
                    }

                }
            } catch (Exception $exception ) {
                $strErrorDesc = $exception->getMessage(). 'Something went wrong!! Please contact support';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }

        }else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if( !isset($strErrorHeader) ) {
            $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
        }else {
            $this->sendOutput(json_encode([ 'error' => $strErrorDesc ]), [$strErrorHeader]);
        }
        exit();
    }

    /**
     * It receives a POST request, validates the data, creates a new user and returns a JSON response
     */
    public function create()
    {
        if( $_SERVER['REQUEST_METHOD'] === 'POST' ){
            if( isset($_POST) && !empty($_POST)  ){
                // Process Request
                try {
                    $validData = FormVerifier::verifyInputs($_POST);
                    if( $validData && $this->verifyKeys(array('nombre', 'rut', 'apellido', 'id_tipo', 'id_area', 'correo'), $_POST) ) {
                        $newUser = new User();
                        $newUser->storeFormValues($_POST);
                        $temporalPassword = Helpers::generateRandomPassword();
                        $newUser->setPassword( password_hash($temporalPassword, PASSWORD_BCRYPT, ['cost' => 12] ) );
                        $newUser->setPasswordExpiration(time() + (7 * 24 * 60 * 60)); // The next week will expirate the password
                        $newUser->setLoginAccess(true);

                        $createUser = $newUser->create();
                        if( $createUser  ) {
                            $responseData = json_encode(['success' => 'New user created', 'temporalPassword' => $temporalPassword]);
                        }else {
                            $strErrorDesc = 'User creation has failed';
                            $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                        }
                    }else {
                        $strErrorDesc = 'Malformed request, checks if the data is correct and all fields exists';
                        $strErrorHeader = 'HTTP/1.1 400 Bad Request';
                    }

                } catch ( Exception $exception ) {
                    $strErrorDesc = $exception->getMessage(). 'Something went wrong!! Please contact support';
                    $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                }

            }else {
                $strErrorDesc = 'Malformed request, checks if the data is correct and all fields exists';
                $strErrorHeader = 'HTTP/1.1 400 Bad Request';
            }
        } else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

        // send output
        if( !isset($strErrorHeader) ) {
            $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
        }else {
            $this->sendOutput(json_encode([ 'error' => $strErrorDesc ], JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE), [$strErrorHeader]);
        }
        exit();
    }

    /**
     * It updates a user's data in the database
     * 
     * @param id The id of the user to be updated
     */
    public function update($id = null)
    {

        $logger = NewLogger::newLogger('API_USER_CONTROLLER');
        if( $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'PATCH' ) {
            $data = $this->getPUTdata();
            $validData = FormVerifier::verifyInputs($data);

            try {

                if( $validData && isset($id) && is_numeric($id)) {
                    $userToBeUpdate = new User();
                    $userToBeUpdate->setId($id);
                    $update = $userToBeUpdate->update($data);

                    if( $update ) {
                        $logger->debug('User updated successfully', array('data'=>$data));
                        $responseData = json_encode(['status' => 'success', 'data' => $data], JSON_PRETTY_PRINT, 2);
                    }else {
                        $strErrorDesc = 'Failed to update the user, please contact support';
                        $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
                    }

                }else {
                    $strErrorDesc = 'Malformed request, checks if the data is correct and all fields exists';
                    $strErrorHeader = 'HTTP/1.1 400 Bad Request';
                }

            } catch (Exception $exception) {
                $strErrorDesc = $exception->getMessage(). 'Something went wrong!! Please contact support';
                $strErrorHeader = 'HTTP/1.1 500 Internal Server Error';
            }


        }else {
            $strErrorDesc = 'Method not supported';
            $strErrorHeader = 'HTTP/1.1 422 Unprocessable Entity';
        }

         // send output
         if( !isset($strErrorHeader) ) {
             $this->sendOutput($responseData, ['Content-Type: application/json', 'HTTP/1.1 200 OK']);
         }else {
             $this->sendOutput(json_encode([ 'error' => $strErrorDesc ], JSON_UNESCAPED_UNICODE|JSON_PRETTY_PRINT,2), [$strErrorHeader]);
         }
         exit();


    }
}