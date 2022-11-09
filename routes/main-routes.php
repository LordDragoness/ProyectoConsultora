<?php

use Pecee\Http\Request;
use Pecee\SimpleRouter\SimpleRouter;


SimpleRouter::get('/', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'index']);


SimpleRouter::get('/admins-login/', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'login']);
SimpleRouter::get('/admin-home/cerrar/', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'logout']);
SimpleRouter::post('/admins-login/', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'loginVerify']);
SimpleRouter::get('/admin-home/', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'adminHome']);
SimpleRouter::get('/admin-home/perfil/', [\SistemaTique\Mvc\Controllers\HandleController\HandleController::class, 'handleProfile']);

SimpleRouter::post('/actualizar-password/', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'changePassword']);

// Usuarios
SimpleRouter::get('/admin-home/usuarios/{action?}', [\SistemaTique\Mvc\Controllers\Roles\JefeMesaController::class, 'manageUsuarios']);


SimpleRouter::post('/usuarios/crear',[\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'create']);
SimpleRouter::get('/usuarios/deshabilitar/{rut}', [\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'restrictAccess']);
SimpleRouter::get('/usuarios/habilitar/{rut}',[\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'allowAccess']);
SimpleRouter::get('/usuarios/resetear/{rut}',[\SistemaTique\Mvc\Controllers\Entities\UserController::class, 'resetPassword']);

//Criticidad
SimpleRouter::get('/admin-home/criticidad/{action?}', [\SistemaTique\Mvc\Controllers\Roles\JefeMesaController::class, 'manageCriticidad']);

SimpleRouter::get('/criticidad/eliminar/{id}', [\SistemaTique\Mvc\Controllers\Entities\CriticidadController::class, 'delete']);
SimpleRouter::post('/criticidad/actualizar/', [\SistemaTique\Mvc\Controllers\Entities\CriticidadController::class, 'update']);
SimpleRouter::post('/criticidad/crear', [\SistemaTique\Mvc\Controllers\Entities\CriticidadController::class, 'create']);


//Areas
SimpleRouter::get('/admin-home/areas/{action?}',[\SistemaTique\Mvc\Controllers\Roles\JefeMesaController::class, 'manageAreas']);

SimpleRouter::post('/areas/crear/', [\SistemaTique\Mvc\Controllers\Entities\AreaController::class, 'create']);
SimpleRouter::post('/areas/actualizar/', [\SistemaTique\Mvc\Controllers\Entities\AreaController::class, 'update']);
SimpleRouter::get('/areas/eliminar/{id}', [\SistemaTique\Mvc\Controllers\Entities\AreaController::class, 'delete']);


//Tipo Tique
SimpleRouter::get('/admin-home/tipos-tique/{action?}', [\SistemaTique\Mvc\Controllers\Roles\JefeMesaController::class, 'manageTiposTique']);

SimpleRouter::post('/tipos-tique/crear', [\SistemaTique\Mvc\Controllers\Entities\TiqueController::class, 'createTipo']);
SimpleRouter::post('/tipos-tique/actualizar', [\SistemaTique\Mvc\Controllers\Entities\TiqueController::class, 'updateTipo']);
SimpleRouter::get('/tipos-tique/eliminar/{id}', [\SistemaTique\Mvc\Controllers\Entities\TiqueController::class, 'deleteTipo']);

// Tiques
SimpleRouter::get('/admin-home/tiques/', [\SistemaTique\Mvc\Controllers\HandleController\HandleController::class, 'handleTique']);



//Clientes
SimpleRouter::post('/clientes/verificar/', [\SistemaTique\Mvc\Controllers\Entities\ClienteController::class, 'verifyClient']);


SimpleRouter::post('/tiques/crear/', [\SistemaTique\Mvc\Controllers\Entities\TiqueController::class, 'create']);
SimpleRouter::post('/tiques/cerrar/', [\SistemaTique\Mvc\Controllers\Entities\TiqueController::class, 'closeTique']);

SimpleRouter::error(function (Request $request, \Exception $exception){

    switch ($exception->getCode()){
        // Page not found
        case 403:
        case 404:
            if( isset($_SESSION['user']) ) {
                response()->redirect('/admin-home/');
            }else {
                response()->redirect('/');
            }
            break;
        // Forbidden
        default:
            response()->redirect('/');
            break;
    }
});