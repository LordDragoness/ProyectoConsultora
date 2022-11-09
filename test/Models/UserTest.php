<?php

require_once __DIR__.'/../../config.php';

use \PHPUnit\Framework\TestCase;
use SistemaTique\Mvc\Models\User;


/* It's a class that tests the User class */
class UserTest extends TestCase
{
    /** @test  */
    public function getAll()
    {
        // Setup
        $user = new User();

        // Action
        $users = $user->getAll();

        // Assertions
        $this->assertEquals("array", gettype($users));

    }


    /**
     * It tests if the getUserTypes() function returns an array
     */
    public function getUserTypes()
    {
        $logger = \SistemaTique\Helpers\NewLogger::newLogger('USER_TEST');
        $user = new User();
        $userTypes = $user->getUserTypes();

        $this->assertEquals("array", gettype($userTypes));
    }


    /**
     * I'm trying to update a user with a fake data array, and I'm expecting the function to return
     * true
     */
    public function update()
    {
        $user = new User();
        $user->setId(1);
        $fakeData = array(
            'nombre' => 'Hasiom',
            'apellido' => 'Sorevir',
            'telefono' => '+56967977241',
            'login_habilitado' => 1,
            'correo' => 'hasiom@sorevir.com',
            'rut' => '20217260-1',
            'fecha_nacimiento' => '2022-06-02',

        );
        $update = $user->update($fakeData);
        $this->assertEquals(true, $update);

    }


    /**
     * It gets a user by rut
     */
    public function getOneByRut()
    {
        $user = new User();
        $user->setRut('20217260-1');
        $userData = $user->getOneByRut();

        $this->assertEquals('object', gettype($userData));
    }
}