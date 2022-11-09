<?php

namespace SistemaTique\Mvc\Controllers\Entities;

use SistemaTique\Helpers\FormVerifier;
use SistemaTique\Helpers\Helpers;
use SistemaTique\Mvc\Models\Client;
use SistemaTique\Mvc\Models\Tique;

/* I'm trying to create a new ticket, but if the client doesn't exist, I create a new client and then
create the ticket */
class TiqueController
{

    /**
     * I'm trying to create a new ticket, but if the client doesn't exist, I create a new client and
     * then create the ticket
     * 
     * @param array data The data to be validated.
     */
    private function createTiqueOpreation(  array $data ): void
    {
        $newTique = new Tique();
        $saveTiqueData = $newTique->storeFormValues($data);

        if( $saveTiqueData ) {

            $newTique->setEstado(1);
            $newTique->setIdUsuarioCrea($_SESSION['user']['id_usuario']);
            $newTique->setFechaCreacion(date("Y-m-d", time()));

            $createTique = $newTique->create();

            if( $createTique ) {
                $_SESSION['success-message'] = "Tique creado con éxito";
            }
        }else {
            $_SESSION['error-message'] = "Algo salió mal, no se pudo crear el tique";
        }
    }

    /**
     * If the user is an admin, and the form is filled out correctly, create a new client, and then
     * create a new ticket.
     */
    public function create(): void
    {
        Helpers::isAdmin(1);
        $necessaryInput = [
            'rut_cliente', 'nombre', 'apellido', 'fecha_nacimiento', 'telefono',
            'correo', 'id_criticidad', 'id_tipo', 'id_area', 'detalle_problema',
            'detalle_servicio'
        ];

        if( isset($_POST) && !empty($_POST) && FormVerifier::verifyKeys($necessaryInput, $_POST) ){
            if( isset($_SESSION['newClient']) && $_SESSION['newClient']) {
                Helpers::removeSession('newClient');
                $newClient = new Client();
                // Second verification
                $newClient->setRutCliente($_POST['rut_cliente']);
                $clientInfo = $newClient->getClientInfo();

                if( !$clientInfo ) {
                    $saveData = $newClient->storeFormValues($_POST);

                    if( $saveData ) {
                        $createUser = $newClient->create();

                        if( $createUser ) {
                            $this->createTiqueOpreation($_POST);
                        }
                    }else{
                        $_SESSION['error-message'] = "Alguno de los datos no es válido";
                    }
                }else {
                    $_SESSION['clientInfo'] = $clientInfo;
                    $_SESSION['error-message'] = 'El cliente ya existe';
                }

            }else{
                $this->createTiqueOpreation($_POST);
            }
        }else {
            $_SESSION['error-message'] = 'Los campos enviados son inválidos';
        }

        header('Location:'.BASE_URL.'/admin-home/tiques/');
        exit();
    }

    /**
     * It takes the id of a ticket, the id of a status, the observation and the id of the user who
     * closes the ticket and updates the ticket with the new status, observation and user who closed
     * it.
     * </code>
     */
    public function closeTique(): void
    {
        Helpers::isAdmin(3);
        if( isset($_POST) && !empty($_POST) && FormVerifier::verifyKeys(['id_tique','id_estado', 'observacion'], $_POST) && FormVerifier::verifyInputs($_POST)) {
            $tique = new Tique();
            $tique->setIdTique($_POST['id_tique']);
            $tique->setEstado($_POST['id_estado']);
            $tique->setObservacion($_POST['observacion']);
            $tique->setIdUsuarioCierra($_SESSION['user']['id_usuario']);

            $closeTique = $tique->update();

            if( $closeTique ) {
                $_SESSION['success-message']  = 'El tique fue cerrado con éxito';
            }else {
                $_SESSION['error-message'] = 'No se pudo cerrar el tique, contacte con soporte';
            }

        }else {
            $_SESSION['error-message'] = 'Datos enviados inválidos';
        }

        header('Location:'.BASE_URL.'/admin-home/tiques/');
        exit();
    }


    /**
     * It creates a new type of ticket
     */
    public function createTipo(): void
    {
        Helpers::isAdmin(2);
        if( isset($_POST) && !empty($_POST) ){
           $validData = FormVerifier::verifyInputs($_POST);

           if( $validData && FormVerifier::verifyKeys(['nombre'], $_POST)){
               $tique = new Tique();
               $tique->setNombreTipoTique($_POST['nombre']);
               $createNewTipoTique = $tique->createTipo();

               if( $createNewTipoTique ){
                   $_SESSION['success-message'] = 'Tipo de tique creado con éxito';
               }else{
                   $_SESSION['error-message'] = 'El tipo de tique ya existe';
               }
           }else {
               $_SESSION['error-message'] = 'Campo no válido';
           }
        }else {
            $_SESSION['error-message'] = 'Faltan datos';
        }

        header('Location:'.BASE_URL.'/admin-home/tipos-tique/');
        exit();
    }

    /**
     * It updates a type of ticket
     */
    public function updateTipo(): void
    {
        Helpers::isAdmin(2);
        if( isset($_POST) && !empty($_POST) ){
            $validData =  FormVerifier::verifyInputs($_POST);

            if( $validData && FormVerifier::verifyKeys(['nombre', 'id_tipo'], $_POST) ){
                $tique = new Tique();
                $tique->setIdTipoTique($_POST['id_tipo']);
                $tique->setNombreTipoTique($_POST['nombre']);

                $updateTipoTique = $tique->updateTipo();

                if( $updateTipoTique ) {
                    $_SESSION['success-message'] = 'Tipo de Tique actualizado con éxito';
                }else {
                    $_SESSION['error-message'] = 'No se pudo actualizar el tipo de Tique';
                }
            }else {
                $_SESSION['error-message'] = 'Los campos enviados son inválidos';
            }
        }else {
            $_SESSION['error-message'] = 'Faltan campos por rellenar';
        }

        header('Location:'.BASE_URL.'/admin-home/tipos-tique');
        exit();

    }

    /**
     * It deletes a type of ticket from the database
     * 
     * @param int id The id of the type of ticket to be deleted
     */
    public function deleteTipo( int $id = null ): void
    {
        Helpers::isAdmin(2);

        if( isset($id) ){

            $tique = new Tique();
            $tique->setIdTipoTique($id);
            $isUsed = $tique->idInUse();

            if( !$isUsed ) {

                $delete = $tique->deleteTipo();

                if( $delete ) {
                    $_SESSION['success-message'] = 'El tipo de tique fue eliminada con éxito';
                }else {
                    $_SESSION['error-message'] = "El tipo de tique con id: $id no existe";
                }

            }else {
                $_SESSION['error-message'] = 'EL tipo de tique está asociado a algún tique, no puede ser eliminado';
            }

        }else {
            $_SESSION['error-message'] = 'Id de tique inválido.';
        }

        header("Location:".BASE_URL.'/admin-home/tipos-tique/');
        exit();
    }
}