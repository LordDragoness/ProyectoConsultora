<?php

namespace SistemaTique\Mvc\Controllers\Entities;

use SistemaTique\Helpers\FormVerifier;
use SistemaTique\Helpers\Helpers;
use SistemaTique\Mvc\Models\Area;

/* It's a controller that handles the CRUD operations of an Area model */
class AreaController
{
    /**
     * It creates a new area and saves it to the database.
     */
    public function create(): void
    {
        Helpers::isAdmin(2);

        if( isset($_POST) && !empty($_POST) ) {
            $validData = FormVerifier::verifyInputs($_POST);

            if( $validData && FormVerifier::verifyKeys(['nombre'], $_POST) ){

                $area = new  Area();
                $area->setNombre($_POST['nombre']);

                $create = $area->create();

                if( $create ) {
                    $_SESSION['success-message'] = 'Área creda con éxito';
                }else{
                    $_SESSION['success-message'] = 'No se pudo crear el área, asegúrese de que no exista';
                }

            }else {
                $_SESSION['error-message'] = 'No se pudo crear el área, faltan campos';
            }
        }else {
            $_SESSION['error-message'] = 'Los datos enviados no son válidos';
        }

        header('Location:'.BASE_URL.'/admin-home/areas/');
        exit();
    }

    /**
     * It updates the area name in the database
     */
    public function update(): void
    {
        Helpers::isAdmin(2);
        if( isset($_POST) && !empty($_POST) ) {

            $validData = FormVerifier::verifyInputs($_POST);

            if( $validData && FormVerifier::verifyKeys(['id_area', 'nombre'],$_POST) ) {

                $area = new Area();
                $area->setId_area($_POST['id_area']);
                $area->setNombre($_POST['nombre']);

                $update = $area->update();

                if( $update ) {
                    $_SESSION['success-message'] = 'Area actualizada con éxito';
                }else {
                    $_SESSION['error-message'] = 'No se pudo actualizar el area';
                }
            }else{
                $_SESSION['error-message'] = 'No se pudo actualizar, faltan campos por rellenar';
            }

        }else {
            $_SESSION['error-message'] = 'Datos enviados no válidos';
        }

        header("Location:".BASE_URL.'/admin-home/areas/');
        exit();
    }

    /**
     * It deletes an area from the database if it's not in use
     * 
     * @param string id The id of the area to be deleted.
     */
    public function delete( string $id ): void
    {
        Helpers::isAdmin(2);
        if( is_numeric($id) ){

            $area = new Area();
            $area->setId_area($id);
            $isUsed = $area->idInUse();

            if( !$isUsed ) {

                $delete = $area->delete();

                if( $delete ) {
                    $_SESSION['success-message'] = 'El área fue eliminada con éxito';
                }else {
                    $_SESSION['error-message'] = "El área con id: $id no existe";
                }

            }else {
                $_SESSION['error-message'] = 'El area está asociada a algún tique o usuario no puede ser eliminada';
            }

        }else {
            $_SESSION['error-message'] = 'Id de área inválido.';
        }

        header("Location:".BASE_URL.'/admin-home/areas/');
        exit();
    }
}