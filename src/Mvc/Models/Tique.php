<?php

namespace SistemaTique\Mvc\Models;

use PDO;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use SistemaTique\Database\Connection;
use SistemaTique\Helpers\NewLogger;

/* It's a class that represents a ticket. */
class Tique
{
    private int $id_tique;
    private int $id_usuario_crea;
    private int $id_usuario_cierra;
    private int $id_estado;
    private string $rut_cliente;
    private int $id_area;
    private int $id_critiidad;
    private string $fecha_creacion;
    private string $detalle_problema;
    private string $detalle_servicio;
    private string $observacion;
    private PDO|bool $conn;
    private LoggerInterface $logger;

    private int $idTipoTique;
    private string $nombreTipoTique;

    /**
     * The function __construct() is a constructor function that creates a new logger object and a new
     * database connection object.
     */
    public function __construct()
    {
        $this->logger = NewLogger::newLogger('TIQUE_MODEL');
        $this->conn = Connection::dbConnection();
    }

    /**
     * It sets the value of the private property `` to the value of the parameter ``
     * 
     * @param int id The id of the area
     */
    public function setIdArea( int $id )
    {
        $this->id_area = $id;
    }

    /**
     * It sets the id_tique variable to the value of the parameter .
     * 
     * @param int id The id of the ticket
     */
    public function setIdTique( int $id )
    {
        $this->id_tique = $id;
    }

    /**
     * It sets the value of the id_estado property.
     * 
     * @param int id
     */
    public function setEstado( int $id )
    {
        $this->id_estado = $id;
    }

    /**
     * It sets the value of the variable .
     * 
     * @param string observacion
     */
    public function setObservacion( string $observacion)
    {
        $this->observacion = $observacion;
    }

    /**
     * It sets the value of the variable  to the value of the variable .
     * 
     * @param int id
     */
    public function setIdTipoTique( int $id )
    {
        $this->idTipoTique = $id;
    }

    /**
     * This function sets the value of the variable  to the value of the variable
     * 
     * 
     * @param string nombre
     */
    public function setNombreTipoTique( string $nombre ):void
    {
        $this->nombreTipoTique = $nombre;
    }

    /**
     * It sets the value of the id_usuario_crea property.
     * 
     * @param int id
     */
    public function setIdUsuarioCrea( int $id )
    {
        $this->id_usuario_crea = $id;
    }

    /**
     * It sets the value of the id_usuario_cierra property.
     * 
     * @param int id
     */
    public function setIdUsuarioCierra(int $id)
    {
        $this->id_usuario_cierra = $id;
    }

    /**
     * It sets the date of creation.
     * 
     * @param string fecha
     */
    public function setFechaCreacion( string $fecha )
    {
        $this->fecha_creacion = $fecha;
    }

    /**
     * It takes an array of data and stores it in the object
     * 
     * @param array data array of values to be stored
     * 
     * @return bool The result of the function.
     */
    public function storeFormValues( array $data ): bool
    {
        $availableIntFields = [
            'id_usuario_crea', 'id_usuario_cierra', 'id_estado', 'id_tipo', 'id_area', 'id_criticidad'
        ];
        $availableStrFields = [
            'rut_cliente', 'detalle_problema', 'detalle_servicio', 'observacion', 'fecha_creacion'
        ];
        $result = false;
        try {
            foreach ($availableIntFields as $field) {
                if( isset($data[$field]) ) $this->$field = (int) $data[$field];
            }

            foreach ( $availableStrFields as $field ) {
                if( isset($data[$field]) ) $this->$field = (string) $data[$field];
            }

            $result = true;
        } catch ( Exception $exception ){
            $this->logger->error('Could not store form values', array('exception' => $exception));
        }

        return $result;

    }


    /**
     * It gets all the data from the database and then it displays it in a table
     * 
     * @return bool|array The result of the query.
     */
    public function getAll(): bool|array
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
            $this->logger->debug('Trying to get all Tiques');
            $sql = "SELECT CONCAT(u.nombre,' ',u.apellido) AS nombre_creador, t.fecha_creacion, tt.nombre AS nombre_ttique, \n"

                . "c.nombre AS nombre_criticidad, a.nombre AS nombre_area, et.nombre AS nombre_etique \n"

                . "FROM `tique` t \n"

                . "LEFT JOIN usuario u ON t.id_usuario_crea=u.id_usuario\n"

                . "LEFT JOIN tipo_tique tt ON t.id_tipo=tt.id_tipo\n"

                . "LEFT JOIN criticidad c ON t.id_criticidad=c.id_criticidad\n"

                . "LEFT JOIN area a ON t.id_area=a.id_area\n"

                . "LEFT JOIN estado_tique et ON t.id_estado=et.id_estado\n"

                . "WHERE t.id_usuario_cierra IS NULL OR t.id_usuario_cierra IS NOT NULL";
            $st = $this->conn->prepare($sql);

            $query = $st->execute();

            if( $query ) {
                $numberOfRows = $st->rowCount();
                if( $numberOfRows > 0 ) {
                    // determine the total number of pages available
                    $_SESSION['numberOfPage']= ceil($numberOfRows/ADMIN_ROWS_LIMIT);
                    $st->closeCursor();


                    if( isset($data['orderByFecha']) && isset($data['orderByCriticidad']) ){
                        $sql .= " ORDER BY t.fecha_creacion, c.valor ASC";
                    }else if( isset($data['orderByFecha']) ) {
                        $sql .= " ORDER BY t.fecha_creacion ASC";
                    }else if( isset($data['orderByCriticidad']) ) {
                        $sql .= " ORDER BY c.valor ASC";
                    }

                    $sql .= " LIMIT :pageFirstResult,:resultPerPage";

                    $st = $this->conn->prepare($sql);
                    $st->bindParam(':pageFirstResult',$pageFirstResult,PDO::PARAM_INT);
                    $st->bindParam(':resultPerPage', $resultPerPage , PDO::PARAM_INT);

                    $st->execute();

                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                }
            }else {
                $this->logger->debug('Get all Tiques query has failed');
            }

            $st->closeCursor();

        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to collect all Tiques data', array('exception' => $exception));
        }

        return $result;
    }

    /**
     * It updates a row in the database
     * 
     * @return bool A boolean value.
     */
    public function update(): bool
    {
        $result = false;
        try {
            $this->logger->debug('Trying to udpate Tique');
            $sql = "UPDATE tique SET id_estado=:id_estado, observacion=:observacion, id_usuario_cierra=:id_usuario_cierra WHERE id_tique=:id_tique";

            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_tique', $this->id_tique, PDO::PARAM_INT);
            $st->bindValue(':id_estado', $this->id_estado, PDO::PARAM_INT);
            $st->bindValue(':id_usuario_cierra', $this->id_usuario_cierra, PDO::PARAM_INT);
            $st->bindValue(':observacion', $this->observacion, PDO::PARAM_STR);

            $query = $st->execute();

            if( $query ) {
                if( $st->rowCount() ) {
                    $result = true;
                    $this->logger->debug('Tique updated successfully');
                }else {
                    $this->logger->debug('Tique could not be updated');
                }
            }

            $st->closeCursor();

        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to update Tique', array('exception' => $exception));
        }

        return $result;
    }

    /**
     * It's a function that creates a new tique in the database
     * 
     * @return bool A boolean value.
     */
    public function create(): bool
    {
        $availableIntFields = ['id_usuario_crea', 'id_tipo', 'id_area', 'id_criticidad'];
        $availableStrFields = ['rut_cliente', 'detalle_problema', 'detalle_servicio', 'fecha_creacion'];
        $result = false;
        try {
            $this->logger->debug('Trying to create a new tique');
            $sql = "INSERT INTO tique (id_tique, id_usuario_crea, id_usuario_cierra, id_estado, rut_cliente, id_tipo,";
            $sql .= "id_area, id_criticidad, fecha_creacion, detalle_problema, detalle_servicio, observacion) ";
            $sql .= "VALUES(:id_tique, :id_usuario_crea, :id_usuario_cierra, :id_estado, :rut_cliente, :id_tipo,";
            $sql .= ":id_area, :id_criticidad, :fecha_creacion, :detalle_problema, :detalle_servicio, :observacion)";

            $st = $this->conn->prepare($sql);
            $st->bindValue(':id_tique', null, PDO::PARAM_NULL);
            $st->bindValue(':id_usuario_cierra', null, PDO::PARAM_NULL);
            $st->bindValue(':observacion', null, PDO::PARAM_NULL);
            $st->bindValue(':id_estado', $this->id_estado, PDO::PARAM_INT);
            foreach ( $availableIntFields as $field ){
                $st->bindValue(':'.$field, $this->$field, PDO::PARAM_INT);
            }
            foreach ( $availableStrFields as $field ){
                $st->bindValue(':'.$field, $this->$field, PDO::PARAM_STR);
            }

            $query = $st->execute();

            if( $query ){
                $this->logger->debug('New Tique has been created successfully');
                $result = true;
            }else {
                $this->logger->debug('Failed to create a new Tique');
            }

        } catch (\Exception $exception){
            $this->logger->error('Something went wrong while trying to create a new tique', array('exception' => $exception));
        }

        return $result;

    }

    /**
     * It gets all the states from the database and returns them as an array
     * 
     * @return An array of associative arrays.
     */
    public function getStates()
    {
        $result = false;
        $this->logger->debug('Trying to collect Tique States data');
        try {
            $sql = "SELECT * FROM estado_tique WHERE id_estado > 1";
            $st = $this->conn->prepare($sql);

            $query = $st->execute();

            if( $query ){
                if( $st->rowCount() !== 0) {
                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                    $this->logger->debug('Tique States collected successfully');
                }
            }else {
                $this->logger->debug('Failed to get Tique States data');
            }
        } catch (\Exception $exception) {
            $this->logger->debug('Something went wrong while trying to collect Tique States data', array('exception' => $exception));
        }

        return $result;
    }


    /**
     * It gets all the tique types from the database
     * 
     * @return An array of associative arrays.
     */
    public function getTiqueTypes()
    {
        $result = false;
        try {
            $sql = "SELECT * FROM tipo_tique";
            $st = $this->conn->prepare($sql);

            $query = $st->execute();

            if( $query ) {
                $this->logger->debug('Tique types collectes successfully');
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }

        } catch (\Exception $exception){
            $this->logger->error('Something went wrong while trying to collect Tique types', array('exception' => $exception));
        }

        return $result;
    }

    /**
     * It creates a new row in the database table tipo_tique.
     * 
     * @return A boolean value.
     */
    public function createTipo()
    {
        $result = false;
        try {
            $sql = "INSERT INTO tipo_tique(id_tipo, nombre) VALUES(:id_tipo, :nombre)";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_tipo', null, PDO::PARAM_NULL);
            $st->bindValue(':nombre', $this->nombreTipoTique, PDO::PARAM_STR);

            $query = $st->execute();

            if( $query ) {
                $result = true;
                $this->logger->debug('Tipo Tique was created successfully');
            }

            $st->closeCursor();

        } catch (\Exception $exception){
            $this->logger->error('Something went wrong while trying to create a new Tique tipo', array('exception' => $exception));
        }

        return $result;
    }

    /**
     * It updates a row in the database with the values of the object's properties
     * 
     * @return A boolean value.
     */
    public function updateTipo()
    {
        $result = false;
        try {
            $sql = "UPDATE tipo_tique SET nombre=:nombre WHERE id_tipo=:id_tipo";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':nombre', $this->nombreTipoTique, PDO::PARAM_STR);
            $st->bindValue(':id_tipo', $this->idTipoTique, PDO::PARAM_INT);

            $query = $st->execute();

            if( $query ) {
                $result = true;
                $this->logger->debug('Tipo Tique updated successfully');
            }else {
                $this->logger->debug('Cannot update Tipo Tique, contact support');
            }

            $st->closeCursor();

        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to update tipo tique', array('exception' => $exception));
        }
        return $result;
    }

    /**
     * It deletes a row from the database
     * 
     * @return The result of the query.
     */
    public function deleteTipo()
    {
        $result = false;
        try {
            $sql = "DELETE FROM tipo_tique WHERE id_tipo=:id_tipo";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_tipo', $this->idTipoTique, PDO::PARAM_INT);

            $query = $st->execute();
            if( $query ) {
                $affectedRows = $st->rowCount();
                if( $affectedRows !== 0 ) {
                    $result = true;
                    $this->logger->debug('Tipo tique was deleted successfully');
                }else {
                    $this->logger->debug('Tipo tique do not exists');
                }
            }

        }catch (\Exception $exception){
            $this->logger->debug('Something went wrong while trying to delete a tipo tique', array('exception'=>$exception));
        }

        return $result;
    }


    // Verifies if this current tipo tique is being used in some tique
    public function idInUse(): bool
    {
        $result = false;
        try {
            $sql = "SELECT COUNT(*) AS uso FROM tique WHERE id_tipo=:id_tipo";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_tipo', $this->idTipoTique, PDO::PARAM_INT);

            $query = $st->execute();
            if( $query ) {
                $usage = $st->fetchColumn();
                if( $usage !== 0 ) {
                    $result = true;
                    $this->logger->debug('There is an usage of this tipo tique, It cannot be deleted');
                }else {
                    $this->logger->debug('There is no usage of the tipo tique id, It can be deleted');
                }
            }

            $st->closeCursor();
        }catch ( \Exception $exception){
            $this->logger->debug('Something went wrong while trying to verify the usage', array('exception'=>$exception));
        }

        return $result;
    }

    /**
     * I'm trying to get all the data from the database, but I'm filtering it by the parameters that
     * I'm receiving in the array 
     * 
     * @param array data array of parameters to filter the query
     * @param bool includeClientInfo if true, it will include the client info in the query.
     * 
     * @return The result of the query.
     */
    public function getAllFiltered(array $data, bool $includeClientInfo = null)
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
            $this->logger->debug('Trying to get tique filtered data');
            if( $includeClientInfo ) {
                $sql = $this->getSQLForGetAll2();
            }else {
                $sql = $this->getSQLForGetAll();
            }

            if( isset($data['fecha']) ) $fecha = $data['fecha'];
            if( isset($data['id_criticidad']) ) $idCriticidad = $data['id_criticidad'];
            if( isset($data['id_tipo']) ) $idTipoTique = $data['id_tipo'];
            if( isset($data['id_area']) ) $idArea = $data['id_area'];
            if( isset($data['rut_usuario_crea']) ) $rutUserCrea = $data['rut_usuario_crea'];
            if( isset($data['rut_usuario_cierra']) ) $rutUserCierra = $data['rut_usuario_cierra'];
            if( isset($data['id_estado']) ) $idEstado = $data['id_estado'];



            $conditions = array();

            if( !empty($fecha) ) {
                $fecha = $this->conn->quote($fecha);
                $conditions[] = "t.fecha_creacion={$fecha}";
            }

            if( !empty($idCriticidad) ) {
                $idCriticidad = $this->conn->quote($idCriticidad);
                $conditions[] = "t.id_criticidad={$idCriticidad}";
            }

            if( !empty($idTipoTique) ) {
                $idTipoTique = $this->conn->quote($idTipoTique);
                $conditions[] = "t.id_tipo={$idTipoTique}";
            }

            if( !empty($idArea) ){
                $idArea = $this->conn->quote($idArea);
                $conditions[] = "t.id_area={$idArea}";
            }

            if( !empty($idEstado) ) {
                $idEstado = $this->conn->quote($idEstado);
                $conditions[] = "t.id_estado={$idEstado}";
            }

            if( !empty($rutUserCierra) ) {
                $user = new User();
                $user->setRut($rutUserCierra);
                $user = $user->getOneByRut();

                if( $user ) {
                    $idUserCierra = $this->conn->quote($user->id_usuario);
                    $conditions[] = "t.id_usuario_cierra={$idUserCierra}";
                }else {
                    $conditions[] = "t.id_usuario_cierra={$rutUserCierra}";
                }
            } else if( !empty($rutUserCrea) ) {
                $user = new User();
                $user->setRut($rutUserCrea);
                $user = $user->getOneByRut();

                $idUserCrea = $this->conn->quote($user->id_usuario);
                $conditions[] = "t.id_usuario_crea={$idUserCrea}";
            }


            if( count( $conditions ) > 0 ) $sql .= " WHERE ".implode(' AND ', $conditions);
            $sql .= " AND ( t.id_usuario_cierra IS NULL OR t.id_usuario_cierra IS NOT NULL)";


            $st = $this->conn->prepare($sql);

            $query = $st->execute();




            if( $query ) {
                $numberOfRows = $st->rowCount();
                if( $numberOfRows > 0 ) {
                    // determine the total number of pages available
                    $_SESSION['numberOfPage']= ceil($numberOfRows/ADMIN_ROWS_LIMIT);
                    $st->closeCursor();



                    if( isset($data['orderByFecha']) && isset($data['orderByCriticidad']) ){
                        $sql .= " ORDER BY t.fecha_creacion, c.valor DESC";
                    }else if( isset($data['orderByFecha']) ) {
                        $sql .= " ORDER BY t.fecha_creacion DESC";
                    }else if( isset($data['orderByCriticidad']) ) {
                        $sql .= " ORDER BY c.valor DESC";
                    }



                    $sql .= " LIMIT :pageFirstResult,:resultPerPage";
                    $st = $this->conn->prepare($sql);
                    $st->bindParam(':pageFirstResult',$pageFirstResult,PDO::PARAM_INT);
                    $st->bindParam(':resultPerPage', $resultPerPage , PDO::PARAM_INT);

                    $st->execute();

                    $result = $st->fetchAll(PDO::FETCH_ASSOC);
                }
            }else {
                $this->logger->debug('Query has failed');
            }


        } catch (\Exception $exception){
            $this->logger->error('Something went wrong while trying to get filtered tique data', array('exception' => $exception));
        }

        return $result;

    }

    /**
     * It returns a string that contains a SQL query that will return all the rows from the table
     * `tique` and all the rows from the tables `usuario`, `tipo_tique`, `criticidad`, `area` and
     * `estado_tique` that are related to the rows in `tique`
     * 
     * @return The SQL query that will be used to get all the records from the database.
     */
    private function getSQLForGetAll()
    {
        return "SELECT CONCAT(u.nombre,' ',u.apellido) AS nombre_creador, t.fecha_creacion, tt.nombre AS nombre_ttique, \n"

            . "c.nombre AS nombre_criticidad, a.nombre AS nombre_area, et.nombre AS nombre_etique \n"

            . "FROM `tique` t \n"

            . "LEFT JOIN usuario u ON t.id_usuario_crea=u.id_usuario\n"

            . "LEFT JOIN tipo_tique tt ON t.id_tipo=tt.id_tipo\n"

            . "LEFT JOIN criticidad c ON t.id_criticidad=c.id_criticidad\n"

            . "LEFT JOIN area a ON t.id_area=a.id_area\n"

            . "LEFT JOIN estado_tique et ON t.id_estado=et.id_estado";
    }

    /**
     * It returns a string that contains a SQL query
     * 
     * @return The query is returning the following:
     */
    private function getSQLForGetAll2()
    {
        return "SELECT CONCAT(u.nombre,' ',u.apellido) AS nombre_creador,t.id_tique, t.fecha_creacion, t.detalle_problema, t.detalle_servicio, tt.nombre AS nombre_ttique, \n"

            . "c.nombre AS nombre_criticidad, a.nombre AS nombre_area, et.nombre AS nombre_etique, dcli.rut_cliente, CONCAT(dcli.nombre,' ',dcli.apellido) AS nombreCliente, \n"

            . "dcli.telefono AS telefonoCliente, dcli.correo AS correoCliente \n"

            . "FROM `tique` t \n"

            . "LEFT JOIN usuario u ON t.id_usuario_crea=u.id_usuario\n"

            . "LEFT JOIN tipo_tique tt ON t.id_tipo=tt.id_tipo\n"

            . "LEFT JOIN criticidad c ON t.id_criticidad=c.id_criticidad\n"

            . "LEFT JOIN area a ON t.id_area=a.id_area\n"

            . "LEFT JOIN estado_tique et ON t.id_estado=et.id_estado\n"

            . "LEFT JOIN datos_cliente dcli ON t.rut_cliente=dcli.rut_cliente";
    }


    /**
     * It returns an array of arrays, each array containing the name of the state and the number of
     * tickets in that state
     * 
     * @return An array of associative arrays.
     */
    public function getStatsByState()
    {
        $result = false;
        try {
            $this->logger->debug('Trying to get Tique stats data');
            $sql = "SELECT et.nombre, COUNT(t.id_tique) as cantidad FROM `tique` t \n"

                . "INNER JOIN estado_tique et ON t.id_estado=et.id_estado GROUP BY et.nombre;";
            $st = $this->conn->prepare($sql);

            $query = $st->execute();

            if( $query ) {
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }

            $st->closeCursor();

        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to get Tique stats data', array('exception' => $exception));
        }

        return $result;
    }


    /**
     * I want to get the count of each type of tique, and if there are no tiques of a certain type, I
     * want to return 0 for that type.
     * 
     * @return An array of associative arrays.
     */
    public function getStatsByType()
    {
        $result = false;
        try {
            $this->logger->debug('Trying to get Tique stats data');
            $sql = "SELECT tt.nombre, COUNT(t.id_tique) as cantidad FROM `tique` t RIGHT JOIN tipo_tique tt ON t.id_tipo=tt.id_tipo GROUP BY tt.nombre HAVING cantidad >= 0;";
            $st = $this->conn->prepare($sql);

            $query = $st->execute();

            if( $query ) {
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }

            $st->closeCursor();

        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to get Tique stats data', array('exception' => $exception));
        }

        return $result;
    }


    /**
     * It returns the number of tickets created by a user in the current year, grouped by month
     * 
     * @param int userId 1
     * 
     * @return An array of associative arrays.
     */
    public function getCreationStatsByUser( int $userId )
    {
        $result = false;
        try {
            $this->logger->debug('Trying to get Tique stats data');
            $setLanguage = "SET lc_time_names = 'es_MX';";
            $sql = "SELECT COUNT(*) as cantidad, MONTHNAME(fecha_creacion) as mes FROM `tique` "
                ."WHERE id_usuario_crea=:id_user AND YEAR(fecha_creacion)= YEAR(CURDATE()) GROUP BY mes ORDER BY MONTH(fecha_creacion) ASC;";

            $set = $this->conn->prepare($setLanguage);
            $st = $this->conn->prepare($sql);
            $st->bindValue(':id_user', $userId, PDO::PARAM_INT);

            $set->execute();
            $query = $st->execute();

            if( $query ) {
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }

            $st->closeCursor();

        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to get Tique stats data', array('exception' => $exception));
        }

        return $result;
    }


    /**
     * It returns the number of tickets per status
     * 
     * @return Array
     * (
     *     [0] => Array
     *         (
     *             [cantidad] => 1
     *             [nombre] => Pendiente
     *         )
     */
    public function getAvailableStats()
    {
        $result = false;
        try {
            $this->logger->debug('Trying to get Tique stats data');
            $sql = "SELECT COUNT(*) as cantidad, et.nombre as nombre FROM `tique` t\n"

                . "INNER JOIN estado_tique et ON t.id_estado=et.id_estado WHERE id_area=:id_area GROUP BY nombre;";


            $st = $this->conn->prepare($sql);
            $st->bindValue(':id_area', $this->id_area, PDO::PARAM_INT);

            $query = $st->execute();

            if( $query ) {
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }

            $st->closeCursor();

        } catch ( \Exception $exception ) {
            $this->logger->error('Something went wrong while trying to get Tique stats data', array('exception' => $exception));
        }

        return $result;
    }
}
