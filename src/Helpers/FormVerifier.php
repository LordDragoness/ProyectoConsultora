<?php

namespace SistemaTique\Helpers;

/* It verifies if the data is valid, if the keys are valid and if the keys are possible */
class FormVerifier
{

    /**
     * If the data is not an integer and is not empty, then return true
     * 
     * @param data The data to be verified.
     * 
     * @return bool A boolean value.
     */
    public static function verifyString( $data ): bool
    {
        $result = false;
        if ( !intval( $data ) && !empty( $data ) ) $result = true;
        return $result;
    }

    /**
     * If the data is an integer and not empty, return true, otherwise return false.
     * 
     * @param data The data to be verified.
     */
    public static function verifyInt( $data ): bool
    {
        $result = false;
        if (  intval( $data ) && !empty( $data ) ) $result = true;
        return $result;
    }

    /**
     * It checks if the date is empty, if it's not empty, it checks if it's in the format of dd/mm/yyyy
     * or dd-mm-yyyy. If it's not, it returns false.
     * 
     * @param data The data to be validated.
     */
    public static function verifyDate( $data ): bool
    {
        $result = true;
        if( empty( $data ) || !preg_match('/\d+\/\d+\/\d+/', $data ) || !preg_match('/\d+-\d+-\d+/', $data ) ) $result = false;
        return $result;

    }

    /**
     * If the data is a string that is either '0' or '1', then return true. Otherwise, return false.
     * 
     * @param data The data to be verified.
     * 
     * @return bool A boolean value.
     */
    public static function verifyBoolean( $data ):bool {
        $result = false;
        if( $data === '0' || $data === '1' ) $result = true;
        return $result;
    }

    // This method verifies if all inputs are valid
    public static function verifyInputs( array $data ): bool
    {
        $result = false;

        foreach ( $data as $input) {
            // We use all the methods above to verify the type of input, if any condition doesn't match that means that the input is invalid
            if( self::verifyString($input) || self::verifyInt( $input ) || self::verifyDate( $input ) || self::verifyBoolean($input) ) {
                $result = true;
            }else {
                $result = false;
                break;
            }
        }
        return $result;
    }


    /**
     * > This function takes an array of expected keys and an array of data and returns true if all the
     * expected keys are present in the data array.
     * 
     * This function is used in the `create` method of the `UserController` class
     * 
     * @param array expectedKeys An array of keys that are expected to be in the data array.
     * @param data The data to be validated.
     */
    public static function verifyKeys( array $expectedKeys, $data ): bool
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

    /**
     * It checks if the array has the keys that are passed in the array.
     * 
     * @param array keys an array of keys that you want to check for
     * @param data The array that you want to check for the keys
     */
    public static function verifyPossibleKeys( array $keys, $data ):bool
    {
        $result = false;
        foreach ( $keys as $key ){
            if( array_key_exists($key, $data) ){
                $result = true;
            }
        }

        return $result;

    }

}