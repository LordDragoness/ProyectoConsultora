<?php

namespace SistemaTique\Mvc\Controllers\Roles;

use SistemaTique\Helpers\FormVerifier;
use SistemaTique\Helpers\Helpers;
use SistemaTique\Middleware\RenderView;
use SistemaTique\Mvc\Models\Area;
use SistemaTique\Mvc\Models\Criticidad;
use SistemaTique\Mvc\Models\Tique;
use SistemaTique\Mvc\Models\User;

/* It's a controller that manages the views of the admin panel. */
class JefeMesaController
{
    /**
     * It renders a view with a table of users, and a form to add/edit/delete users
     * 
     * @param string action The action to be performed.
     */
    public function manageUsuarios(string $action = null): void
    {
        Helpers::isAdmin(2);
        $action = Helpers::verifyAction($action);
        $needsSelects = Helpers::verifySelects($action);
        $selectsData = null;

        if( $needsSelects ){
            $selectsData = Helpers::retrieveSelectsData(
                [
                    [new User(), 'getUserTypes', 'tiposUsuarios'],
                    [new Area(), 'getAll', 'tipoAreas']
                ]
            );
        }

        $data = Helpers::retrieveObjectData( $action, [new User(), 'getAll'] );

        RenderView::render('admin-panel',
            [
                'manageView' => 'Usuarios/'.$action,
                'selectsData' => $selectsData,
                'data' => $data
            ]
        );
    }

    /**
     * It renders a view with a table of data
     * 
     * @param string action The action to be performed.
     */
    public function manageCriticidad( string $action = null ): void
    {
        Helpers::isAdmin(2);
        $action = Helpers::verifyAction($action);

        $data = Helpers::retrieveObjectData( $action, [new Criticidad(), 'getAll'] );

        RenderView::render('admin-panel',
            [
                'manageView' => 'Criticidad/'.$action,
                'data' => $data
            ]
        );
    }


    /**
     * It's a function that renders a view for the admin panel, and it's called from a route.
     * 
     * @param string action The action to be performed.
     */
    public function manageAreas( string $action = null ): void
    {

        Helpers::isAdmin(2);
        $action = Helpers::verifyAction($action);

        $data = Helpers::retrieveObjectData( $action, [new Area(), 'getAll'] );

        RenderView::render('admin-panel',
            [
                'manageView' => 'Areas/'.$action,
                'data' => $data
            ]
        );

    }

    /**
     * It renders a view with a form to create, update or delete a tique type
     * 
     * @param string action The action to be performed.
     */
    public function manageTiposTique( string $action = null ): void
    {
        Helpers::isAdmin(2);
        $action = Helpers::verifyAction($action);

        $data = Helpers::retrieveObjectData($action, [new Tique(), 'getTiqueTypes']);

        RenderView::render('admin-panel',
            [
                'manageView' => 'TiposTique/'.$action,
                'data' => $data
            ]
        );


    }

    /**
     * It retrieves data from the database and renders a view
     * 
     * @param string action
     */
    public function manageTiques( string $action = null ): void
    {
        Helpers::isAdmin(2);
        $action = Helpers::verifyAction($action);
        if( isset($_GET) && !empty($_GET) && FormVerifier::verifyPossibleKeys(['fecha', 'id_criticidad', 'id_tipo', 'id_area', 'rut_usuario_crea', 'rut_usuario_cierra', 'page', 'orderByFecha', 'orderByCriticidad'], $_GET) && FormVerifier::verifyInputs($_GET)) {

            $tique = new Tique();
            $data = $tique->getAllFiltered($_GET);


        }else {
            $data = Helpers::retrieveObjectData($action, [new Tique(), 'getAll']);
        }


        $selectsData = Helpers::retrieveSelectsData(
            [
                [new Tique(), 'getTiqueTypes', 'tiposTique'],
                [new Area(), 'getAll', 'tipoAreas'],
                [new Criticidad(), 'getAll', 'criticidades']
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
     * It gets the stats of the tickets and then it renders the view
     */
    public function showHome(): void
    {
        $tique = new Tique();
        $tiqueStateStats = $tique->getStatsByState();
        $tiqueTypeStats = $tique->getStatsByType();

        $state = [];
        $amount = [];

        $types = [];
        $amountType = [];
        foreach ($tiqueStateStats as $stat){
            $state[] = $stat['nombre'];
            $amount[] = $stat['cantidad'];
        }

        foreach ($tiqueTypeStats as $stat){
            $types[] = $stat['nombre'];
            $amountType[] = $stat['cantidad'];
        }



        $tiqueStats = ['estados' => $state,'cantidades' => $amount, 'tipos' => $types, 'cantidadesTipos' => $amountType, 'allTiqueTypesData' => $tiqueTypeStats];

        RenderView::render('admin-panel',[
            'tiqueStats' => $tiqueStats
        ]);
    }

    /**
     * It renders the admin-panel view, and passes the user's session data and the profile view to it
     */
    public function showProfile(): void
    {
        RenderView::render('admin-panel',[
            'profileData' => $_SESSION['user'],
            'profileView' => 'profile'
        ]);
    }



}