<?php

namespace SistemaTique\Middleware;

use JetBrains\PhpStorm\NoReturn;

/* It renders the view and the data that is passed to it. */
class RenderView
{

    /**
     * It renders the view and the data that is passed to it.
     * </code>
     * 
     * @param view the name of the view to be rendered
     * @param array data is the data that is sent from the controller to the view.
     */
    public static function render($view, array $data = null): void
    {
        if( isset($_SESSION['user']) )$rolId = $_SESSION['user']['id_tipo'];
        if( isset($data['manageView']) ) $manageView = $data['manageView'];
        if( isset($data['selectsData']) ) $selectsData = $data['selectsData'];
        if( isset($data['data']) ) $data = $data['data']; // datos de la entidad en especifico
        if( isset($data['clientInfo']) ) $clientInfo = $data['clientInfo'];
        if( isset($data['tiqueStats']) ) $tiqueStats = $data['tiqueStats'];
        if( isset($data['profileView']) ) $profileView = $data['profileView'];
        if( isset($data['profileData']) ) $profileData = $data['profileData'];

        require_once __DIR__.'/../Mvc/Views/User/layouts/header.phtml';
        require_once __DIR__.'/../Mvc/Views/User/'.$view.'.phtml';
        require_once __DIR__.'/../Mvc/Views/User/layouts/footer.phtml';
        exit();
    }


}