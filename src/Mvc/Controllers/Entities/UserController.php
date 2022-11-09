<?php

namespace SistemaTique\Mvc\Controllers\Entities;

use SistemaTique\Helpers\FormVerifier;
use SistemaTique\Helpers\Helpers;
use SistemaTique\Helpers\NewLogger;
use SistemaTique\Middleware\RenderView;
use SistemaTique\Mvc\Controllers\HandleController\HandleController;
use SistemaTique\Mvc\Models\User;

/* It's a controller that handles the user's actions. */
class UserController
{

    /**
     * This function is a public function that returns nothing and is called index
     */
    public function index(): void
    {
        echo 'hola esta es la pagina principal';

    }

    /**
     * If the user doesn't exist, render the login view
     */
    public function login(): void
    {
        if( !Helpers::userExist('user') ) {
            RenderView::render('login');
        }else {
            header("Location:".BASE_URL."/admin-home");
            exit();
        }
    }

    /**
     * It checks if the user is an admin, if it is, it calls the manageHome method of the
     * HandleController class.
     */
    public function adminHome(): void
    {
        $log = NewLogger::newLogger('USER_CONTROLLER', 'FirePHPHandler');

        if( Helpers::userExist('user') ) {
            try {

                $handler = new HandleController();

                $handler->manageHome();

            } catch (\Exception $exception) {
                $log->error('Something went wrong in adminHome Method', array('exception' => $exception));
            }

        }else {
            echo 'No es un admin';
        }

    }

    /**
     * It removes the session and redirects the user to the login page
     */
    public function logout():void
    {
        Helpers::removeSession('user');
        header('Location:'.BASE_URL.'/admins-login');
        exit();
    }


    /**
     * If the user exists, then check if the password is correct, if it is, then check if the password
     * has expired, if it hasn't, then log the user in.
     */
    public function loginVerify():void
    {
        if( isset($_POST) && !Helpers::userExist('user') ) {

            $user = new User();

            $user->setRut( $_POST['rut'] );
            $userData = $user->getOneByRut();

            if( $userData ) {
                if( password_verify( $_POST['password'], $userData->password  ) ) {

                    if( time() >= intval($userData->expiration_password) && $userData->expiration_password !== null  ) {
                        header("Location:".BASE_URL."/admins-login");
                        $_SESSION['error-message'] = "Su contraseña temporal a caducado, porfavor contacte al Jefe de Mesa para su reposición.";
                    }else {
                        $userSecureData =  array(
                            'id_usuario' => $userData->id_usuario,
                            'id_area' => $userData->id_area,
                            'correo' => $userData->correo,
                            'telefono' => $userData->telefono,
                            'fechaNacimiento' => $userData->fechaNacimiento,
                            'nombreArea' => $userData->nombreArea,
                            'nombreCompleto' => $userData->nombre.' '.$userData->apellido,
                            'nombre' => $userData->nombre,
                            'apellido' => $userData->apellido,
                            'rut' => $userData->rut,
                            'id_tipo' => $userData->id_tipo,
                            'nombreTipo' =>$userData->nombreTipo,
                            'expiracionPassword' => $userData->expiration_password
                        );

                        $_SESSION['user'] = $userSecureData;
                        sleep(1);
                        header("Location:".BASE_URL.'/admin-home');
                        exit();
                    }

                }else {
                    $_SESSION['error-message'] = 'Contraseña incorrecta';
                }
            }else {
                $_SESSION['error-message'] = 'No existe el usuario con rut: '.$_POST['rut'];
            }

        }

        header("Location:".BASE_URL."/admins-login");
        exit();
    }

    /**
     * It creates a new user in the database
     */
    public function create():void
    {
        Helpers::isAdmin(2);
        if( isset($_POST) && !empty($_POST) ){

            $validData = FormVerifier::verifyInputs($_POST);
            if( $validData && FormVerifier::verifyKeys(['nombre', 'rut', 'apellido', 'id_tipo', 'id_area', 'correo', 'fecha_nacimiento', 'telefono'], $_POST) ){
                $newUser = new User();
                $newUser->storeFormValues($_POST);
                $temporalPassword = Helpers::generateRandomPassword();
                $newUser->setPassword( password_hash($temporalPassword, PASSWORD_BCRYPT, ['cost' => 12] ) );
                $newUser->setPasswordExpiration(time() + (7 * 24 * 60 * 60)); // The next week will expirate the password
                $newUser->setLoginAccess(true);

                $create = $newUser->create();

                if( $create ) {
                    $_SESSION['success-message'] = "Usuario creado exitosamente, la contraseña temporal es: $temporalPassword";
                }else {
                    $_SESSION['error-message'] = "No se pudo crear el usuario, asegurese de que el rut y correo sean únicos";
                }

            }else{
                $_SESSION['error-message'] = 'Faltan campos por rellenar';
            }

        }else {
            $_SESSION['error-message'] = 'No se han enviado los valores necesarios para crear un usuario';
        }

        if( isset($_SESSION['error-message']) ) {
            header('Location:'.BASE_URL.'/admin-home/usuarios/crear');
            exit();
        }

        header('Location:'.BASE_URL.'/admin-home/usuarios/');
        exit();
    }


    /**
     * It changes the value of a column in a table in the database
     * 
     * @param string rut is the user's id
     */
    public function restrictAccess( string $rut = null ):void
    {
        Helpers::isAdmin(2);
        if( isset($rut) ){
            $user = new User();
            $user->setRut($rut);

            $updateAccess = $user->changeSystemAccess(false);

            if( $updateAccess ) {
                $_SESSION['success-message'] = 'El acceso al sistema ha sido deshabilitado';
            }else {
                $_SESSION['error-message'] = 'Algo salió mal, no se pudo actualizar el acceso al sistema';
            }
        }

        header("Location:".BASE_URL.'/admin-home/usuarios');
        exit();
    }

    /**
     * It checks if the user is an admin, then it checks if the rut is set, then it creates a new user,
     * then it sets the rut of the new user, then it changes the system access of the new user, then it
     * checks if the system access was changed, then it sets a success message, then it sets an error
     * message, then it redirects to the admin home page, then it exits
     * 
     * @param string rut is the user's id
     */
    public function allowAccess( string $rut = null ):void
    {
        Helpers::isAdmin(2);
        if( isset($rut) ){
            $user = new User();
            $user->setRut($rut);

            $updateAccess = $user->changeSystemAccess(true);

            if( $updateAccess ) {
                $_SESSION['success-message'] = 'El acceso al sistema ha sido habilitado';
            }else {
                $_SESSION['error-message'] = 'El usuario todavía tiene acceso al sistema.';
            }
        }

        header("Location:".BASE_URL.'/admin-home/usuarios');
        exit();
    }

    /**
     * It generates a random password, hashes it, sets the expiration date, updates the password in the
     * database, and then allows the user to access the system
     * 
     * @param string rut The user's rut
     */
    public function resetPassword( string $rut = null ):void
    {
        Helpers::isAdmin(2);
        if( isset($rut) ){
            $user = new User();
            $user->setRut($rut);
            $userInfo = $user->getOneByRut();

            if( $userInfo->login_habilitado ) {
                $_SESSION['error-message'] = 'El usuario todavía tiene acceso al sistema.';
            }else {
                $newPassword = Helpers::generateRandomPassword();
                $user->setPassword(password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => 12] ));
                $user->setPasswordExpiration(time() + (7 * 24 * 60 * 60)); // The next week will expirate the password

                $updatePassword = $user->resetPassword();

                if( $updatePassword ) {

                    $allowAccess = $user->changeSystemAccess(true);
                    if( $allowAccess ) {
                        $_SESSION['success-message'] = "Contraseña generada con éxito.<br> Rut Usuario: <b>$rut</b><br> Nueva contraseña: <b>$newPassword</b> ";
                    }else {
                        $_SESSION['error-message'] = "Algo salió mal, intente nuevamente.";
                    }

                }else {
                    $_SESSION['error-message'] = 'Algo salió mal al generar la contraseña, intente nuevamente.';
                }
            }
        }

        header("Location:".BASE_URL.'/admin-home/usuarios');
        exit();

    }

    /**
     * If the user exists, and the form is filled out correctly, then change the password.
     */
    public function changePassword(): void
    {
        if( Helpers::userExist('user') ) {

            if (isset($_POST) && !empty($_POST) && FormVerifier::verifyKeys(['password', 'repeat_password'], $_POST)) {
                if( $_POST['password'] === $_POST['repeat_password'] ) {

                    $user = new User();
                    $user->setRut($_SESSION['user']['rut']);
                    $user->setPassword(password_hash($_POST['password'], PASSWORD_BCRYPT, ['cost' => 12] ));

                    $changePass = $user->changePassword();

                    if( $changePass ) {
                        $_SESSION['success-message'] = 'Contraseña actualizada con éxito';
                        $_SESSION['user']['expiracionPassword'] = null;
                    }else {
                        $_SESSION['error-message'] = 'La contraseña no se pudo actualizar.';
                    }

                }else {
                    $_SESSION['error-message'] = 'Las contraseñas no coinciden';
                }
            }else {
                $_SESSION['error-message'] = 'Faltan datos necesarios para la operación';
            }
        }

        header('Location:'.BASE_URL.'/admin-home/perfil/');
        exit();
    }
}