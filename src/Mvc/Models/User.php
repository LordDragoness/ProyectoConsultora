<?php

namespace SistemaTique\Mvc\Models;

use PDO;
use Psr\Log\LoggerInterface;
use SistemaTique\Database\Connection;
use SistemaTique\Helpers\NewLogger;
use Exception;

/* This class is a representation of a user in the database. */
class User
{

    private int $id_usario;
    private int $id_tipo;
    private int $id_area;
    private bool $login_habilitado;
    private string $nombre;
    private string $apellido;
    private string $telefono;
    private string $fecha_nacimiento;
    private string $correo;
    private string $rut;
    private string $password;
    private string $expiration_password;

    private LoggerInterface $log;
    private PDO|bool $conn;


    /**
     * This function sets the password of the user
     * 
     * @param string password The password to be hashed.
     */
    public function setPassword( string $password )
    {
        $this->password = $password;
    }

    /**
     * This function sets the expiration date of the password
     * 
     * @param string passwordExpiration The date the password will expire.
     */
    public function setPasswordExpiration( string $passwordExpiration )
    {
        $this->expiration_password = $passwordExpiration;
    }

    /**
     * It sets the login access to true or false.
     * 
     * @param bool hasAccess true or false
     */
    public function setLoginAccess( bool $hasAccess )
    {
        $this->login_habilitado = $hasAccess;
    }

    /**
     * This function is used to create a new instance of the class.
     */
    public function __construct()
    {
        $this->log = NewLogger::newLogger('USER_MODEL');
        $this->log->debug('Class has been instancied.');
        $this->conn = Connection::dbConnection();

    }

    /**
     * It takes an array of data and stores it in the object
     * 
     * @param array data
     * 
     * @return bool A boolean value.
     */
    public function storeFormValues( array $data ): bool
    {
        $result = false;
        try {
            $this->log->debug('Trying to store form values');
            if( isset($data['rut'])) $this->rut = trim($data['rut']);
            if( isset($data['correo'])) $this->correo = trim($data['correo']);
            if( isset($data['fecha_nacimiento'])) $this->fecha_nacimiento = trim($data['fecha_nacimiento']);
            if( isset($data['telefono'])) $this->telefono = (string) trim($data['telefono']);
            if( isset($data['nombre'])) $this->nombre = trim($data['nombre']);
            if( isset($data['apellido'])) $this->apellido = trim($data['apellido']);
            if( isset($data['id_tipo'])) $this->id_tipo = (int) $data['id_tipo'];
            if( isset($data['id_area'])) $this->id_area = (int) $data['id_area'];

            $result = true;
        } catch ( Exception $exception ) {
            $this->log->error('Something went wrong while storing form values', array('exception' => $exception));
        }

        return $result;
    }

    /**
     * It sets the id of the user.
     * 
     * @param int id The id of the user
     */
    public function setId( int $id )
    {
        $this->id_usario = $id;
    }

    /**
     * This function sets the value of the rut property
     * 
     * @param string rut
     */
    public function setRut( string $rut )
    {
        $this->rut = $rut;
    }

    /**
     * It returns a boolean value of true if the connection is established, and false if it is not
     * 
     * @return bool The connection to the database.
     */
    public function verifyConnection(): bool
    {
        return $this->conn;
    }

    // This method gets user data using rut by default

    /**
     * @throws Exception
     */
    public function getOneByRut()
    {
        $result = false;

        try {
            $this->log->info('Trying to get user data');
            $wantedData = array(
                'u.id_usuario', 'u.id_tipo', 'tu.nombre AS nombreTipo', 'u.id_area', 'u.login_habilitado', 'u.nombre',
                'u.apellido', 'u.telefono','u.correo', 'u.rut', 'u.expiration_password', 'u.password',
                'a.nombre AS nombreArea'
            );
            $sql = "SELECT ".implode(",", $wantedData).", fecha_nacimiento AS fechaNacimiento ";
            $sql .= "FROM usuario u INNER JOIN area a ON a.id_area=u.id_area ";
            $sql .= "INNER JOIN tipo_usuario tu ON tu.id_tipo=u.id_tipo ";
            $sql .= "WHERE rut=:rut";
            $st = $this->conn->prepare($sql);

            $st->bindParam(':rut',$this->rut,PDO::PARAM_STR);
            $query = $st->execute();


            if( $query ) {
                if( $st->rowCount() !== 0 ) {
                    $this->log->info('User data has been collected successfully.');
                    $result = $st->fetchObject();
                }
            }


        } catch ( \Exception $exception ) {
            $this->log->error('Somehting wrong while trying to get User data', array('exception', $exception));
        }

        $st->closeCursor();

        return $result;
    }


    /**
     * It gets all the users from the database and returns them as an array
     * 
     * @return array|bool An array of associative arrays.
     */
    public function getAll(): array|bool
    {
        if (!isset ($_GET['page']) ) {
            $_GET['page'] = 1;
            $page = 1;
        } else {
            $page = $_GET['page'];
        }
        $resultPerPage = ADMIN_ROWS_LIMIT;
        $pageFirstResult = ($page-1) * $resultPerPage;
        $result = false;
        try {

            $sql = "SELECT u.id_usuario AS id_usuario,u.id_tipo, tu.nombre AS tipoUsuario, a.nombre AS area,u.id_area, u.login_habilitado AS habilitado, u.nombre, u.apellido, u.telefono";
            $sql .= ",UNIX_TIMESTAMP(u.fecha_nacimiento) AS fechaNacimiento, u.correo, u.rut FROM usuario u";
            $sql .= " INNER JOIN tipo_usuario tu ON u.id_tipo=tu.id_tipo";
            $sql .= " INNER JOIN area a ON u.id_area=a.id_area";
            $st = $this->conn->prepare($sql);
            $query = $st->execute();


            if( $query ) {


                $numberOfRows = $st->rowCount();
                if( $numberOfRows > 0 ) {
                    // determine the total number of pages available
                    $_SESSION['numberOfPage']= ceil($numberOfRows/ADMIN_ROWS_LIMIT);
                    $st->closeCursor();

                    $sql .= " LIMIT :pageFirstResult,:resultPerPage";
                    $st = $this->conn->prepare($sql);
                    $st->bindParam(':pageFirstResult',$pageFirstResult,PDO::PARAM_INT);
                    $st->bindParam(':resultPerPage', $resultPerPage , PDO::PARAM_INT);

                    $st->execute();

                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                }

                $this->log->info('Users data has been collected successfully.');
            }

            $st->closeCursor();

        } catch ( \Exception $exception) {
            $this->log->debug('Something went wrong while trying to collect Users data', array('exception' => $exception));
        }
        return $result;
    }

    /**
     * It gets all the user types from the database
     * 
     * @return array|false An array of associative arrays.
     */
    public function getUserTypes(): array|false
    {
        $result = false;
        try {
            $sql = "SELECT * FROM tipo_usuario";
            $st = $this->conn->prepare($sql);
            $query = $st->execute();


            if( $query ) {
                $this->log->debug('User Types data collected successfully');
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
                $this->log->debug('Data', array('data' => $result));
            }

        } catch ( \Exception $exception ) {
            $this->log->debug('Something went wrong while trying to collect User type data', array('exception' => $exception));
        }
        $st->closeCursor();
        return $result;
    }

    /**
     * It inserts a new user into the database
     * 
     * @return bool A boolean value.
     */
    public function create(): bool
    {
        $result = false;
        try {
            $sql = "INSERT INTO usuario (id_usuario, id_tipo, id_area, login_habilitado, nombre, apellido, telefono, fecha_nacimiento, correo, rut, password, expiration_password) ";
            $sql .= "VALUES(:id_usuario, :id_tipo, :id_area, :login_habilitado, :nombre, :apellido, :telefono, :fecha_nacimiento, :correo, :rut, :password, :expiration_password)";
            $st = $this->conn->prepare($sql);
            $st->bindValue(':id_usuario', null, PDO::PARAM_NULL);
            $st->bindValue(':id_tipo', $this->id_tipo, PDO::PARAM_INT);
            $st->bindValue(':id_area', $this->id_area, PDO::PARAM_INT);
            $st->bindValue(':login_habilitado', $this->login_habilitado, PDO::PARAM_BOOL);
            $st->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $st->bindValue(':apellido', $this->apellido, PDO::PARAM_STR);
            $st->bindValue(':telefono', $this->telefono, PDO::PARAM_STR);
            $st->bindValue(':fecha_nacimiento', $this->fecha_nacimiento, PDO::PARAM_STR);
            $st->bindValue(':rut', $this->rut, PDO::PARAM_STR);
            $st->bindValue(':password', $this->password, PDO::PARAM_STR);
            $st->bindValue(':correo', $this->correo, PDO::PARAM_STR);
            $st->bindValue('expiration_password', $this->expiration_password, PDO::PARAM_STR);

            $query = $st->execute();

            if( $query ) {
                $this->log->debug('User has been created successfully');
                $result = true;
            }else {
                $this->log->warning('Could not create a new User');
            }

            $st->closeCursor();

        } catch ( \Exception $exception) {
            $this->log->error('Something went wrong while trying to insert an new User', array('exception'=>$exception));
        }

        return $result;

    }



    /**
     * It updates a user in the database
     * 
     * @param array options
     * 
     * @return bool A boolean value.
     */
    public function update( array $options = null):bool
    {
        $result = false;
        $availableVarCharOptions = array('nombre', 'apellido', 'telefono', 'correo', 'rut', 'fecha_nacimiento');
        $availableIntOptions = array('id_area', 'id_tipo','login_habilitado');

        try {
            $this->log->debug('Trying to update a User');
            $toBeUpdate = [];
            if( isset($options['nombre']) ) $toBeUpdate[] = "nombre=:nombre";
            if( isset($options['apellido'])) $toBeUpdate[] = "apellido=:apellido";
            if( isset($options['telefono']) ) $toBeUpdate[] = "telefono=:telefono";
            if( isset($options['fecha_nacimiento'])) $toBeUpdate[] = "fecha_nacimiento=:fecha_nacimiento";
            if( isset($options['id_area']) ) $toBeUpdate[] = "id_area=:id_area";
            if( isset($options['id_tipo']) ) $toBeUpdate[] = "id_tipo=:id_tipo";
            if( isset($options['login_habilitado'])) $toBeUpdate[] = "login_habilitado=:login_habilitado";
            if( isset($options['rut']) ) $toBeUpdate[] = "rut=:rut";
            if( isset($options['correo'])) $toBeUpdate[] = "correo=:correo";

            $sql = "UPDATE usuario SET ".implode(',',$toBeUpdate)." WHERE id_usuario=:id_usuario";
            $st = $this->conn->prepare($sql);

            foreach ($availableVarCharOptions as $posibleOption){
                if( isset($options[$posibleOption]) ) $st->bindValue(":".$posibleOption, $options[$posibleOption],PDO::PARAM_STR);
            }
            foreach ($availableIntOptions as $posibleOption){
                if( isset($options[$posibleOption]) ) $st->bindValue(":".$posibleOption, $options[$posibleOption],PDO::PARAM_INT);
            }
            $st->bindValue(':id_usuario', $this->id_usario, PDO::PARAM_INT);

            $query = $st->execute();

            if( $query ) {
                $this->log->debug('User updated successfully');
                $result = true;
            }else {
                $this->log->warning('User could not be updated');
            }


        } catch ( \Exception $exception) {
            $this->log->error('Something went wrong while trying to update an user', array('exception'=> $exception));
        }

        return $result;
    }

    /**
     * It updates the user's system access status
     * 
     * @param bool allow boolean
     * 
     * @return A boolean value.
     */
    public function changeSystemAccess( bool $allow )
    {
        $result = false;
        try{
            $this->log->debug('Trying to update user system access');
            $sql = "UPDATE usuario SET login_habilitado=:login_habilitado WHERE rut=:rut";

            $st = $this->conn->prepare($sql);

            $st->bindValue(':login_habilitado', $allow, PDO::PARAM_BOOL);
            $st->bindValue(':rut', $this->rut, PDO::PARAM_STR);

            $query = $st->execute();

            if( $query ) {
                if( $st->rowCount() !== 0 ){
                    $result = true;
                    $this->log->debug('User system access status changed successfully');
                }
            }else {
                $this->log->debug('Failed to update User system access status');
            }

            $st->closeCursor();

        }catch ( \Exception $exception ){
            $this->log->error('Something went wrong while trying to change system access');
        }

        return $result;
    }


    /**
     * I'm trying to update a user's password and expiration date in the database.
     * </code>
     * 
     * @return A boolean value.
     */
    public function resetPassword()
    {
        $result = false;
        try{
            $this->log->debug('Trying to update user system access');
            $sql = "UPDATE usuario SET password=:password, expiration_password=:expiration_password WHERE rut=:rut";

            $st = $this->conn->prepare($sql);

            $st->bindValue(':rut', $this->rut, PDO::PARAM_STR);
            $st->bindValue(':password', $this->password, PDO::PARAM_STR);
            $st->bindValue(':expiration_password', $this->expiration_password, PDO::PARAM_STR);

            $query = $st->execute();

            if( $query ) {
                if( $st->rowCount() !== 0 ){
                    $result = true;
                    $this->log->debug('User system access status changed successfully');
                }
            }else {
                $this->log->debug('Failed to update User system access status');
            }

            $st->closeCursor();

        }catch ( \Exception $exception ){
            $this->log->error('Something went wrong while trying to change system access');
        }

        return $result;
    }

    /**
     * It updates the user's password and expiration date in the database.
     * </code>
     * 
     * @return A boolean value.
     */
    public function changePassword()
    {

        $result = false;
        try{
            $this->log->debug('Trying to update user system access');
            $sql = "UPDATE usuario SET password=:password, expiration_password=:expiration_password WHERE rut=:rut";

            $st = $this->conn->prepare($sql);

            $st->bindValue(':rut', $this->rut, PDO::PARAM_STR);
            $st->bindValue(':password', $this->password, PDO::PARAM_STR);
            $st->bindValue(':expiration_password', null, PDO::PARAM_NULL);

            $query = $st->execute();

            if( $query ) {
                if( $st->rowCount() !== 0 ){
                    $result = true;
                    $this->log->debug('User system access status changed successfully');
                }
            }else {
                $this->log->debug('Failed to update User system access status');
            }

            $st->closeCursor();

        }catch ( \Exception $exception ){
            $this->log->error('Something went wrong while trying to change system access');
        }

        return $result;

    }



}