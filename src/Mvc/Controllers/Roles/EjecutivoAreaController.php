<?php

namespace SistemaTique\Mvc\Controllers\Roles;

use SistemaTique\Helpers\FormVerifier;
use SistemaTique\Helpers\Helpers;
use SistemaTique\Middleware\RenderView;
use SistemaTique\Mvc\Models\Area;
use SistemaTique\Mvc\Models\Criticidad;
use SistemaTique\Mvc\Models\Tique;

/* It's a controller that manages the views and data for the admin panel. */
class EjecutivoAreaController
{
    /**
     * It renders a view with a table of tiques, and the table is populated with data from the
     * database.
     * 
     * @param string action The action to be performed.
     */
    public function manageTiques( string $action = null): void
    {

        Helpers::isAdmin(3);
        $action = Helpers::verifyAction($action);
        $data = Helpers::retrieveObjectData($action, [new Tique(), 'getAllFiltered'], ['filterByAreaAndState' => ['id_area' => $_SESSION['user']['id_area'], 'id_estado' => 1], 'includeClientInfo' => true]);
        $selectsData = Helpers::retrieveSelectsData(
            [
                [new Tique(), 'getStates', 'estadosTique'],
            ]
        );


        RenderView::render('admin-panel',
            [
                'manageView' => 'Tiques/'.$action,
                'selectsData' => $selectsData,
                'data' => $data
            ]
        );
    }


    /**
     * It gets the number of tickets in each status and then calculates the total number of tickets and
     * the number of tickets in the "A resolución" status
     */
    public function showHome(): void
    {
        $tique = new Tique();
        $tique->setIdArea($_SESSION['user']['id_area']);
        $stats = $tique->getAvailableStats();

        $totalTiques = 0;
        $totalResolucion = 0;
        foreach ($stats as $stat) {
            $totalTiques += $stat['cantidad'];
            if( $stat['nombre'] == 'A resolución' ) {
                $totalResolucion =$stat['cantidad'];
            }
        }

        $finishedTiques = $totalTiques - $totalResolucion;

        $tiqueStats = ['totalTiques' => $totalTiques, 'availableTiques' => $totalResolucion, 'finishedTiques' => $finishedTiques];

        RenderView::render('admin-panel', [
            'tiqueStats' => $tiqueStats
        ]);
    }

    /**
     * It renders the admin-panel view, and passes the user's session data and the profile view to it.
     */
    public function showProfile(): void
    {
        RenderView::render('admin-panel',[
            'profileData' => $_SESSION['user'],
            'profileView' => 'profile'
        ]);
    }



}