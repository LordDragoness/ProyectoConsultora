<?php

namespace SistemaTique\Mvc\Controllers\Roles;

use SistemaTique\Helpers\Helpers;
use SistemaTique\Middleware\RenderView;
use SistemaTique\Mvc\Models\Area;
use SistemaTique\Mvc\Models\Criticidad;
use SistemaTique\Mvc\Models\Tique;

/* It's a controller that manages the views of the admin panel */
class EjecutivoMesaController
{
    /**
     * It renders two views, one with a table and one with a form
     * 
     * @param string action The action to be performed.
     */
    public function manageTiques(string $action = null): void
    {
        Helpers::isAdmin(1);
        $action = Helpers::verifyAction($action);
        $selectsData = null;

        if( isset($_SESSION['clientInfo'])) {
            $selectsData = Helpers::retrieveSelectsData(
                [
                    [new Tique(), 'getTiqueTypes', 'tiposTique'],
                    [new Area(), 'getAll', 'tipoAreas'],
                    [new Criticidad(), 'getAll', 'criticidades']
                ]
            );
        }

        RenderView::render('admin-panel',[
            'manageView' => 'Tiques/ver',
            'selectsData' => $selectsData
        ]);

        RenderView::render('admin-panel',
            [
                'manageView' => 'Tiques/'.$action
            ]
        );
    }

    /**
     * It gets the number of tickets created by the user in each month of the year and then it renders
     * the view with the data
     */
    public function showHome(): void
    {

        $tique = new Tique();
        $stats = $tique->getCreationStatsByUser($_SESSION['user']['id_usuario']);

        $totalTiques = 0;
        $meses = [];
        $data = [];

        foreach ($stats as $stat) {
            $totalTiques += $stat['cantidad'];
            $meses[] = $stat['mes'];
            $data[] = $stat['cantidad'];
        }

        $tiqueStats = ['totalTiques' => $totalTiques, 'meses' => $meses, 'data' => $data];

        RenderView::render('admin-panel',[
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