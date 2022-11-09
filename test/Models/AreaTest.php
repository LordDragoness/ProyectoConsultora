<?php

require_once __DIR__.'/../../config.php';

use \PHPUnit\Framework\TestCase;
use SistemaTique\Mvc\Models\Area;

/* It tests the Area model */
class AreaTest extends TestCase
{

    /**
     * It tests that the getAll() function returns an array with at least one element
     */
    public function getAll()
    {
        $area = new Area();
        $areas = $area->getAll();

        $this->assertEquals('array', gettype($areas));
        $this->assertEquals(true, count($areas) > 0);
    }


    /**
     * It creates a new area in the database
     */
    public function create()
    {
        $logger = \SistemaTique\Helpers\NewLogger::newLogger('AREA_MODEL_TEST');
        $area = new Area();
        $area->setNombre('Bases de Datos');
        $create = $area->create();

        $logger->debug('Query result', array('result' => $create));

        $this->assertEquals(true, $create);
    }

    /**
     * It creates a new Area object, sets the id to 1, sets the name to 'Servicio Tecnológico', and
     * then calls the update() method on the Area object
     */
    public function update()
    {
        $area = new Area();
        $area->setId_area(1);
        $area->setNombre('Servicio Tecnológico');

        $update = $area->update();


        $this->assertEquals(true, $update);
    }



    /**
     * This function checks if the area id is in use.
     */
    public function isInUse()
    {
        $area = new Area();
        $area->setId_area(11);
        $checkUsage = $area->idInUse();

        $this->assertEquals(true, $checkUsage);

    }

    /** @test */
    public function delete()
    {
        $area = new Area();
        $area->setId_area(1);

        $delete = $area->delete();

        $this->assertEquals(true, $delete);
    }
}