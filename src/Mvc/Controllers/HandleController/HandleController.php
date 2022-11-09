<?php

namespace SistemaTique\Mvc\Controllers\HandleController;

use SistemaTique\Mvc\Controllers\Roles\EjecutivoAreaController;
use SistemaTique\Mvc\Controllers\Roles\EjecutivoMesaController;
use SistemaTique\Mvc\Controllers\Roles\JefeMesaController;

/* This class is a controller that handles the user's request to see the profile page. */
class HandleController
{
    /**
     * If the user is logged in, then depending on the user's role, a different controller is
     * instantiated and the manageTiques() method is called
     */
    public function handleTique(): void
    {
        if( isset($_SESSION['user']) ){

            switch ($_SESSION['user']['id_tipo']){
                case 1:
                    $controller = new EjecutivoMesaController();
                    break;
                case 2:
                    $controller = new JefeMesaController();
                    break;
                case 3:
                    $controller = new EjecutivoAreaController();
                    break;
                default:
                    break;
            }

            $controller->manageTiques();
        }else {
            header('Location:'.BASE_URL);
            exit();
        }
    }

    /**
     * If the user is logged in, then depending on the user's role, a different controller is
     * instantiated and the showHome() method is called
     */
    public function manageHome(): void
    {
        if( isset($_SESSION['user']) ){

            switch ($_SESSION['user']['id_tipo']){
                case 1:
                    $controller = new EjecutivoMesaController();
                    break;
                case 2:
                    $controller = new JefeMesaController();
                    break;
                case 3:
                    $controller = new EjecutivoAreaController();
                    break;
                default:
                    break;
            }

            $controller->showHome();
        }else {
            header('Location:'.BASE_URL);
            exit();
        }
    }


    /**
     * If the user is logged in, then show the profile page.
     */
    public function handleProfile(): void
    {
        if( isset($_SESSION['user']) ) {


            switch ($_SESSION['user']['id_tipo']){
                case 1:
                    $controller = new EjecutivoMesaController();
                    break;
                case 2:
                    $controller = new JefeMesaController();
                    break;
                case 3:
                    $controller = new EjecutivoAreaController();
                    break;
                default:
                    break;
            }

            $controller->showProfile();


        }else {
            header('Location:'.BASE_URL);
            exit();
        }
    }
}
