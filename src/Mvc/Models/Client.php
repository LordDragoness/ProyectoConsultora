<?php

namespace SistemaTique\Mvc\Models;

use PDO;
use PHPUnit\Exception;
use Psr\Log\LoggerInterface;
use SistemaTique\Database\Connection;
use SistemaTique\Helpers\NewLogger;

/* It's a class that allows you to create, update and get information from a client */
class Client
{
    private string $rut_cliente;
    private string $nombre;
    private string $apellido;
    private string $fecha_nacimiento;
    private string $telefono;
    private string $correo;
    private PDO|bool $conn;
    private LoggerInterface $logger;

    /**
     * The function __construct() is a constructor function that creates a new logger object and a new
     * database connection object.
     */
    public function __construct()
    {
        $this->logger = NewLogger::newLogger('CLIENT_MODEL');
        $this->conn = Connection::dbConnection();
    }

    /**
     * It sets the value of the variable .
     * 
     * @param string rut_cliente
     */
    public function setRutCliente( string $rut_cliente )
    {
        $this->rut_cliente = $rut_cliente;
    }

    /**
     * It takes an array of data and stores it in the object
     * 
     * @param array data array of data to be stored
     * 
     * @return bool A boolean value.
     */
    public function storeFormValues(array $data): bool
    {
        $this->logger->debug('Trying to store data');
        $result = false;
        try {

            $availabeData = array(
                'rut_cliente','nombre','apellido','fecha_nacimiento','telefono','correo'
            );

            foreach ($availabeData as $option ) {
                if( isset($data[$option]) ) $this->$option = (string) $data[$option];
            }
            $result = true;
            $this->logger->debug('Data has been stored successfully');

        } catch ( \Exception $exception) {
            $this->logger->error('Something went wrong while trying to save data', array('exception' => $exception));
        }

        return $result;

    }

    /**
     * It gets the client info from the database
     * 
     * @return An object with the data of the client.
     */
    public function getClientInfo()
    {
        $result = false;
        try {

            $sql = "SELECT * FROM datos_cliente WHERE rut_cliente=:rut_cliente";
            $st = $this->conn->prepare($sql);

            $st->bindValue(':rut_cliente', $this->rut_cliente, PDO::PARAM_STR);
            $query = $st->execute();

            if( $query ) {

                if( $st->columnCount() > 0 ) {
                    $result = $st->fetchObject();
                    $this->logger->debug('Client data has been collected', array('data' => $result));
                }else {
                    $this->logger->debug('Client with rut: '.$this->rut_cliente.' do not exists');
                }

            }else {
                $this->logger->debug('Client data cannot be collected');
            }


        } catch (\Exception $exception) {
            $this->logger->error('Something went wrong while trying to get Client info', array('exception' => $exception));
        }

        return $result;
    }

    /**
     * It creates a new client in the database
     * 
     * @return bool A boolean value.
     */
    public function create(): bool
    {
        $result = false;
        try {
            $this->logger->debug('Trying to create a new Client');
            $fields = ['rut_cliente', 'nombre', 'apellido', 'fecha_nacimiento', 'telefono', 'correo'];
            $sql = "INSERT INTO datos_cliente (rut_cliente, nombre, apellido, fecha_nacimiento,telefono, correo)";
            $sql .= "VALUES(:rut_cliente, :nombre, :apellido, :fecha_nacimiento, :telefono, :correo)";

            $st = $this->conn->prepare($sql);
            foreach ($fields as $field) {
                $st->bindValue(':'.$field, $this->$field, PDO::PARAM_STR);
            }

            $query = $st->execute();
            if( $query ) {
                $this->logger->debug('Client was created successfully');
                $result = true;
            }else {
                $this->logger->warning('Something went wrong while trying to create a new client');
            }

            $st->closeCursor();

        } catch ( \Exception $exception){
            $this->logger->error('Something went wrong while trying to create a new Client', array('exception' => $exception));

        }


        return $result;
    }

    /**
     * It updates the client's information in the database
     * 
     * @param array options
     * 
     * @return bool A boolean value.
     */
    public function update( array $options ):bool
    {
        $result = false;
        $availableFields= ['nombre', 'apellido', 'fecha_nacimiento', 'correo', 'telefono'];
        try {
            $this->logger->debug('Trying to update client information');
            $toBeUpdate = [];
            foreach ($availableFields as $field) {
                if( isset($options[$field]) ) $toBeUpdate[] = $field.'='.':'.$field;
            }

            $sql = "UPDATE datos_cliente SET ".implode(',', $toBeUpdate)." WHERE rut_cliente=:rut_cliente";
            $st = $this->conn->prepare($sql);
            $this->logger->debug('SQL generated', array('sql statement' => $sql));

            foreach ($availableFields as $field) {
                if( isset( $options[$field] ) ) $st->bindValue(":$field", $options[$field],PDO::PARAM_STR);
            }
            $st->bindValue(':rut_cliente', $this->rut_cliente,PDO::PARAM_STR);

            $query = $st->execute();
            if( $query ){
                $this->logger->debug('Client updated successfully');
                $result = true;
            }else {
                $this->logger->debug('Client could not be updated');
            }

        } catch ( \Exception $exception){
            $this->logger->error('Cannot update the client', array('exception' => $exception));
        }

        return $result;

    }


}