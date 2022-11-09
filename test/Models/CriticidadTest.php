<?php


require_once __DIR__.'/../../config.php';

use \PHPUnit\Framework\TestCase;
use SistemaTique\Mvc\Models\Criticidad;
use SistemaTique\Helpers\NewLogger;

/* It's a test class for the Criticidad class. */
class CriticidadTest extends TestCase
{

    /**
     * It gets all the criticidades from the database and asserts that the data is an array
     */
    public function getAll()
    {
        $logger = NewLogger::newLogger('CRTICIDAD_TEST_GET_ALL');
        $criticidad = new Criticidad();
        $criticidades = $criticidad->getAll();

        $this->assertEquals('array', gettype($criticidades));
        $logger->debug('Data collected', array('data'=>$criticidades));
    }


    /**
     * It creates a new criticidad object, sets the name and value, and then creates it
     */
    public function create()
    {
        $criticiad = new Criticidad();
        $criticiad->setNombre( 'Normal' );
        $criticiad->setValor( 10 );

        $create = $criticiad->create();

        $this->assertEquals( true, $create );
    }


    /**
     * It updates the criticidad with id 1, setting its name to 'Urgente' and its value to 15
     */
    public function update()
    {
        $criticidad = new Criticidad();
        $criticidad->setIdCriticidad(1);
        $criticidad->setNombre('Urgente');
        $criticidad->setValor(15);

        $update = $criticidad->update();

        $this->assertEquals(true, $update);
    }

    /** @test */
    public function verifyUsage()
    {
        $criticidad = new Criticidad();
        $criticidad->setIdCriticidad(1);
        $verification = $criticidad->idInUse();

        $this->assertEquals(false, $verification);
        //$this->assertEquals(true, $verification);
    }

}