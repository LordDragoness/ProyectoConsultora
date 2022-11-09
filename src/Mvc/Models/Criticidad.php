<?php

namespace SistemaTique\Mvc\Models;

use PDO;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use SistemaTique\Database\Connection;
use SistemaTique\Helpers\NewLogger;

/* It's a class that handles the CRUD operations of a criticidad table */
class Criticidad
{
    private int $id_criticidad;
    private string $nombre;
    private int $valor;
    private PDO|bool $conn;
    private LoggerInterface $logger;


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
     * This function sets the value of the variable  to the value of the parameter 
     * 
     * @param int valor The value of the coin.
     */
    public function setValor( int $valor ): void
    {
        $this->valor = $valor;
    }

    /**
     * It sets the id_criticidad property to the value of the  parameter.
     * 
     * @param int id
     */
    public function setIdCriticidad( int $id ): void
    {
        $this->id_criticidad = $id;
    }

    /**
     * It creates a new logger object and a new database connection object.
     */
    public function __construct()
    {
        $this->logger = NewLogger::newLogger('CRITICIDAD_MODEL');
        $this->conn = Connection::dbConnection();
    }

    /**
     * It gets all the data from the criticidad table and returns it as an array
     * 
     * @return An array of associative arrays.
     */
    public function getAll()
    {
        $result = false;
        try {
            $this->logger->debug('Trying to get all the Critcidad data');
            $sql = "SELECT * FROM criticidad ORDER BY valor DESC";

            $st = $this->conn->prepare($sql);
            $query = $st->execute();

            if( $query ) {
                $this->logger->debug('Data was collected successfully');
                $result = $st->fetchAll(PDO::FETCH_ASSOC);
            }else {
                $this->logger->debug('Failed while trying to collect data');
            }

            $st->closeCursor();

        } catch ( \Exception $exception ){
            $this->logger->error('Something went wrong while getting the data', array('exception'=>$exception));
        }
        return $result;
    }

    /**
     * It creates a new criticidad in the database
     * 
     * @return bool A boolean value.
     */
    public function create(): bool
    {
        $result = false;
        try {
            $this->logger->debug('Trying to create new Criticidad');
            $sql = "INSERT INTO criticidad(id_criticidad, nombre, valor) VALUES(:id_criticidad, :nombre, :valor)";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_criticidad', null, PDO::PARAM_NULL);
            $st->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $st->bindValue(':valor', $this->valor, PDO::PARAM_INT);

            $query = $st->execute();
            if( $query ){
                $this->logger->debug('Criticidad has been created successfully');
                $result = true;
            }else {
                $this->logger->debug('Criticidad creation has failed');
            }

            $st->closeCursor();

        } catch ( \Exception $exception ){
            $this->logger->error('Something went wrong while creating a new Criticidad', array('exception'=>$exception));
        }

        return $result;
    }


    /**
     * It updates a criticidad in the database
     * 
     * @return bool A boolean value.
     */
    public function update(): bool
    {
        $result = false;
        try {
            $this->logger->debug('Trying to update a criticidad');
            $sql = "UPDATE criticidad SET nombre=:nombre, valor=:valor WHERE id_criticidad=:id_criticidad";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':nombre', $this->nombre, PDO::PARAM_STR);
            $st->bindValue(':id_criticidad', $this->id_criticidad, PDO::PARAM_INT);
            $st->bindValue(':valor', $this->valor, PDO::PARAM_INT);

            $query = $st->execute();

            if( $query ) {

                $this->logger->debug('Criticidad updated successfully');
                $result = true;
            }else {
                $this->logger->warning('Cannot update the Criticidad');
            }

            $st->closeCursor();

        } catch ( \Exception $exception) {
            $this->logger->error('Somehting went wrong while updating the criticidad', array('exception' => $exception));
        }



        return $result;
    }

    // Verifies if this current criticidad is being used in some tique
    public function idInUse(): bool
    {
        $result = false;
        try {
            $sql = "SELECT COUNT(*) AS uso FROM tique WHERE id_criticidad=:id_criticidad";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_criticidad', $this->id_criticidad, PDO::PARAM_INT);

            $query = $st->execute();
            if( $query ) {
                $usage = $st->fetchColumn();
                if( $usage !== 0 ) {
                    $result = true;
                    $this->logger->debug('There is an usage of this criticidad, It cannot be deleted');
                }else {
                    $this->logger->debug('There is no usage of the criticidad id, It can be deleted');
                }
            }

            $st->closeCursor();
        }catch ( \Exception $exception){
            $this->logger->debug('Something went wrong while trying to verify the usage', array('exception'=>$exception));
        }

        return $result;
    }

    /**
     * It deletes a criticidad from the database
     * 
     * @return bool The result of the query.
     */
    public function delete(): bool
    {
        $result = false;
        try {
            $sql = "DELETE FROM criticidad WHERE id_criticidad=:id_criticidad";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':id_criticidad', $this->id_criticidad, PDO::PARAM_INT);

            $query = $st->execute();
            if( $query ) {
                $affectedRows = $st->rowCount();
                if( $affectedRows !== 0 ) {
                    $result = true;
                    $this->logger->debug('Criticidad was deleted successfully');
                }else {
                    $this->logger->debug('The criticidad do not exists');
                }
            }

        }catch ( \Exception $exception){
            $this->logger->debug('Something went wrong while trying to delete a criticidad', array('exception'=>$exception));
        }

        return $result;
    }
}