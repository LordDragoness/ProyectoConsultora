<?php

namespace SistemaTique\Mvc\Models;

use PDO;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use SistemaTique\Database\Connection;
use SistemaTique\Helpers\NewLogger;

/* It's a class that handles the CRUD operations of the Area table */
class Area
{
    private int $id_area;
    private string $nombre;
    private PDO|bool $conn;
    private LoggerInterface $logger;


    /**
     * The function __construct() is a constructor function that creates a new database connection and
     * a new logger.
     */
    public function __construct()
    {
        $this->conn = Connection::dbConnection();
        $this->logger = NewLogger::newLogger('AREA_MODEL');
    }

    /**
     * This function sets the value of the id_area property
     * 
     * @param int id The id of the area
     */
    public function setId_area( int $id ): void
    {
        $this->id_area = $id;
    }

    /**
     * This function sets the value of the variable  to the value of the parameter 
     * 
     * @param string nombre The name of the property.
     */
    public function setNombre( string $nombre ): void
    {
        $this->nombre = $nombre;
    }

    /**
     * It gets all the areas from the database
     * 
     * @return bool|array An array of associative arrays.
     */
    public function getAll(): bool|array
    {
        $result = false;
        try {
            $this->logger->debug('Trying to get all Areas');
            $sql = "SELECT * FROM area";

            $st = $this->conn->prepare($sql);
            $query = $st->execute();

            if( $query ){
                $this->logger->debug('Areas data has been collected');
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }else{
                $this->logger->warning('Cannot collect Areas data');
            }

        } catch ( \Exception $exception){
            $this->logger->warning('Something went wrong while trying to get Areas info', array('exception' => $exception));
        }

        return $result;
    }

   /**
    * It creates a new record in the database
    * 
    * @return bool A boolean value.
    */
    public function create(): bool
    {
        $result = false;
        try {
            $this->logger->debug('Trying to create a new Areas');
            $sql = "INSERT INTO area(id_area, nombre) VALUES(:id_area, :nombre)";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_area', null, PDO::PARAM_NULL);
            $st->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);

            $query = $st->execute();
            if( $query ){
                $this->logger->debug('Areas has been created successfully');
                $result = true;
            }else {
                $this->logger->debug('Areas creation has failed');
            }

            $st->closeCursor();

        } catch ( \Exception $exception ){
            $this->logger->error('Something went wrong while creating a new Areas', array('exception'=>$exception));

        }

        return $result;
    }


    /**
     * It updates the area table with the new name of the area
     * 
     * @return bool A boolean value.
     */
    public function update(): bool
    {
        $result = false;
        try {
            $this->logger->debug('Trying to update an area');
            $sql = "UPDATE area SET nombre=:nombre WHERE id_area=:id_area";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $st->bindValue(':id_area', $this->id_area, PDO::PARAM_INT);

            $query = $st->execute();

            if( $query ) {
                $this->logger->debug('Areas updated successfully');
                $result = true;
            }else {
                $this->logger->warning('Cannot update the area');
            }

        } catch (\Exception $exception) {
            $this->logger->error('Somehting went wrong while updating the area', array('exception' => $exception));
        }
        return $result;
    }

    /**
     * It deletes an area from the database.
     * 
     * @return The result of the query.
     */
    public function delete()
    {
        $result = false;
        try {
            $sql = "DELETE FROM area WHERE id_area=:id_area";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_area', $this->id_area, PDO::PARAM_INT);

            $query = $st->execute();
            if( $query ) {
                $affectedRows = $st->rowCount();
                if( $affectedRows !== 0 ) {
                    $result = true;
                    $this->logger->debug('Area was deleted successfully');
                }else {
                    $this->logger->debug('The Area do not exist');
                }
            }

        }catch ( \Exception $exception){
            $this->logger->debug('Something went wrong while trying to delete an Area', array('exception'=>$exception));
        }

        return $result;
    }


    // Verifies if this current area is being used in some tique
    public function idInUse(): bool
    {
        $result = false;
        try {
            $sqlTique = "SELECT COUNT(*) AS uso FROM tique WHERE id_area=:id_area";
            $sqlUsuario = "SELECT COUNT(*) AS uso FROM usuario  WHERE id_area=:id_area";

            $firstSt = $this->conn->prepare($sqlTique);
            $secondSt = $this->conn->prepare($sqlUsuario);

            $firstSt->bindValue(':id_area', $this->id_area, PDO::PARAM_INT);

            $query = $firstSt->execute();

            if( $query ) {
                $TiqueUsage = $firstSt->fetchColumn();
                $firstSt->closeCursor();

                $secondSt->bindValue(':id_area', $this->id_area, PDO::PARAM_INT);

                $secondQuery = $secondSt->execute();

                if( $secondQuery ) {
                    $UsuarioUsage = $secondSt->fetchColumn();

                    if( $TiqueUsage !== 0 || $UsuarioUsage !== 0 ) {
                        $result = true;
                        $this->logger->debug('There is an usage of this area, It cannot be deleted');
                    }else {
                        $this->logger->debug('There is no usage of the area id, It can be deleted');
                    }
                }

                $secondSt->closeCursor();

            }


        }catch ( \Exception $exception){
            $this->logger->debug('Something went wrong while trying to verify the usage', array('exception'=>$exception));
        }

        return $result;
    }

}