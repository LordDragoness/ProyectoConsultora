<?php

require_once __DIR__.'/../../config.php';

use \PHPUnit\Framework\TestCase;
use SistemaTique\Mvc\Models\Tique;

/* I'm trying to test a class that has a lot of dependencies, and I'm not sure how to mock them. */
class TiqueTest extends TestCase
{
    /** @test  */
    public function create()
    {
        $tique = new Tique();
        $fakeData = [
            'id_usuario_crea' => 1,
            'rut_cliente' => '13008100-2',
            'id_tipo' => 1,
            'id_estado' => 1,
            'id_area' => 2,
            'id_criticidad' => 1,
            'fecha_creacion' => '2022-06-12',
            'detalle_problema' => 'Cliente no tiene transformador de energía para su notebook',
            'detalle_servicio' => 'El servicio consta de proporcionar el transformador necesario y limpieza del aparato'

        ];
        $tique->storeFormValues($fakeData);
        $create = $tique->create();

        $this->assertEquals(true, $create);
    }


    /**
     * It gets the tique types
     */
    public function getTiqueTypes()
    {
        $tique = new Tique();
        $tiqueTypes = $tique->getTiqueTypes();

        $this->assertEquals('array', gettype($tiqueTypes));
    }


    /**
     * It creates a new type of ticket called "Emergencia" and then it checks if it was created
     */
    public function createTipoTique()
    {
        $tique = new Tique();
        $tique->setNombreTipoTique('Emergencia');
        $newTipoTique= $tique->createTipo();

        $this->assertEquals(true, $newTipoTique);
    }

    public function updateTipo()
    {
        $tique = new Tique();
        $tique->setIdTipoTique(1);
        $tique->setNombreTipoTique('Felicitación');

        $updateTipoTique =$tique->updateTipo();


        $this->assertEquals(true, $updateTipoTique);
    }



    /**
     * It tests if the id of a tique is in use
     */
    public function tipoTiqueIsUsed()
    {
        $tique = new Tique();
        $tique->setIdTipoTique(100);

        $isUsed = $tique->idInUse();

        $this->assertEquals(true, $isUsed);

    }


    /**
     * It deletes a record from the database if it's not being used
     */
    public function deleteTipo()
    {
        $tique = new Tique();
        $tique->setIdTipoTique(8);
        $result = false;
        $isUsed = $tique->idInUse();

        if( !$isUsed ) {
            $result = $tique->deleteTipo();
        }

        $this->assertEquals(true, $result);
    }


    /**
     * It tests that the getAll() function returns an array
     */
    public function getAll()
    {
        $tique = new Tique();
        $allTique = $tique->getAll();

        $this->assertEquals('array', gettype($allTique));
    }


    /**
     * It gets all the filtered data from the database
     */
    public function getAllFiltered()
    {
        $tique = new Tique();
        $allData = $tique->getAllFiltered([
            'fecha' => '2022-07-08',
            'id_criticidad' => '1',
            'id_area' => 2
        ]);

        $this->assertEquals('array', gettype($allData));
        $this->assertCount(3, $allData);
    }


    /**
     * It gets the states from the Tique class and asserts that the type of the returned value is an
     * array
     */
    public function getStates()
    {
        $tique = new Tique();
        $states = $tique->getStates();

        $this->assertEquals('array', gettype($states));
    }


    /**
     * It gets the creation stats by user
     */
    public function getCreationStatsByUser()
    {
        $logger = \SistemaTique\Helpers\NewLogger::newLogger('log');

        $tique = new Tique();
        $stats = $tique->getCreationStatsByUser(1);

        $logger->debug('Data collected', array('data'=>$stats));
        $this->assertCount(1, $stats);

    }
}