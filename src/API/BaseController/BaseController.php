<?php

namespace SistemaTique\API\BaseController;

use SistemaTique\Helpers\Helpers;
use SistemaTique\Helpers\NewLogger;

/* It's a base class for all controllers. */
class BaseController
{

    /**
     * Send API output.
     *
     * @param mixed  $data
     * @param string $httpHeader
     */
    protected function sendOutput(string $data, $httpHeaders = array())
    {

        header_remove('Set-Cookie');

        if (is_array($httpHeaders) && count($httpHeaders)) {
            foreach ($httpHeaders as $httpHeader) {
                header($httpHeader);
            }
        }

        echo $data;
        exit;
    }

    /**
     * Get query string params.
     *
     * @return array
     */

    protected function getQueryStringParams()
    {
        return parse_str($_SERVER['QUERY_STRING'], $query);
    }



    /**
     * It reads the data from the php://input stream, decodes it, and returns an array of key/value
     * pairs.
     * 
     * @return array The data is being returned as an array.
     */
    protected function getPUTdata(): array
    {
        $logger = NewLogger::newLogger('HELPERS_GETPUTDATA');
        $putfp = fopen('php://input', 'r');
        $putdata = [];
        while($data = fread($putfp, 1024))
            // Incov converts string to requested character enconding
            // $decoded = iconv( 'ISO-8859-1', 'UTF-8', urldecode( $encoded ) );
            // In this case, when we use iconv produces an unexpected results, so we just use urldecode only
            $cleanData = urldecode($data);
            $logger->debug('Actual data recivied', array('data' => $data));
            $entities = explode('&', $cleanData);
            if( $entities ) {
            foreach ($entities as $entity) {
                $values = explode('=', $entity);

                $putdata[$values[0]] = $values[1] ;
            }
        }

        fclose($putfp);
        return $putdata;
    }

    /** Checks if all keys exist to continue with the request */
    public function verifyKeys( array $expectedKeys, $data ): bool
    {
        $result = true;
        foreach ( $expectedKeys as $key ){
            if( !array_key_exists($key, $data) ){
                $result = false;
                break;
            }
        }

        return $result;
    }



}