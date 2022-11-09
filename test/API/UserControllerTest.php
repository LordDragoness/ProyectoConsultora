<?php

// TEST FOR THE USER'S API
require_once __DIR__.'/../../config.php';
use \PHPUnit\Framework\TestCase;
use SistemaTique\API\UserController;

/* It's a test class that tests the UserController class. */
class UserControllerTest extends TestCase
{

    /**
     * If the string is valid JSON, it will be decoded into a PHP array. If it's not valid JSON, it
     * will be returned as NULL.
     * 
     * @param string The string to be decoded.
     */
    public function isValidJson($string) {

        json_decode($string);
        return json_last_error() === JSON_ERROR_NONE;
    }


    /**
     * I'm trying to get all the users from the API.
     * </code>
     */
    public function getAll()
    {
        // Create curl resource
        $client = curl_init();

        // Set curl options
        curl_setopt_array($client, [
            CURLOPT_URL => BASE_URL.'/api/users/',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_CUSTOMREQUEST => 'GET'
        ]);

        // $output contains the output string
        $output = curl_exec($client);
        curl_close($client);

        $httpcode = curl_getinfo($client, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($client, CURLINFO_CONTENT_TYPE);

        // Check if the response is a valid JSON
        $this->assertEquals(200, $httpcode);
        $this->assertEquals('application/json', $contentType);
        $this->assertEquals(true, $this->isValidJson($output));

    }



    /**
     * It creates a new user in the database
     */
    public function create()
    {

        $client = curl_init();
        curl_setopt($client, CURLOPT_URL, BASE_URL."/api/users");
        curl_setopt($client, CURLOPT_POST, 1);
        curl_setopt($client, CURLOPT_POSTFIELDS, http_build_query(
            array(
                'rut' => '1300100-2',
                'correo' => 'example@example.com',
                'nombre' => 'Kay',
                'apellido' => 'Bauer',
                'tipo_usuario' => 1,
                'area' => 1
            )
        ));

        curl_setopt($client, CURLOPT_RETURNTRANSFER, true);

        $output = curl_exec($client);
        $httpcode = curl_getinfo($client, CURLINFO_HTTP_CODE);
        $contentType = curl_getinfo($client, CURLINFO_CONTENT_TYPE);

        curl_close($client);

        $this->assertEquals(200, $httpcode);

    }

    /** @test */
    public function updatePATCH()
    {
        $logger = \SistemaTique\Helpers\NewLogger::newLogger('API_USER_TEST_PATCH');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, BASE_URL."/api/users/1");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'PUT');
        curl_setopt($curl, CURLOPT_POSTFIELDS, "nombre=benjamineses");
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
        $response = curl_exec($curl);
        $httpcode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        $this->assertEquals( 200 , $httpcode);
        $logger->debug('Transfer data', array('data'=>$response));


    }


}