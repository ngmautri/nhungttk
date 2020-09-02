<?php
namespace Application\Domain\Util;

/**
 *
 * @author Nguyen Mau Tri - ngmautri@gmail.com
 *        
 */
class JsonErrors
{

    static public function getErrorMessage($errorCode)
    {
        switch ($errorCode) {
            case JSON_ERROR_NONE:
                return null;
                break;
            case JSON_ERROR_DEPTH:
                return 'ERROR - Maximum stack depth exceeded';
                break;
            case JSON_ERROR_STATE_MISMATCH:
                return 'ERROR - Underflow or the modes mismatch';
                break;
            case JSON_ERROR_CTRL_CHAR:
                return 'ERROR - Unexpected control character found';
                break;
            case JSON_ERROR_SYNTAX:
                return 'ERROR - Syntax error, malformed JSON';
                break;
            case JSON_ERROR_UTF8:
                return 'ERROR - Malformed UTF-8 characters, possibly incorrectly encoded';
                break;
            case JSON_ERROR_INF_OR_NAN:
                return 'ERROR JSON_ERROR_INF_OR_NAN';
                break;

            case JSON_ERROR_INVALID_PROPERTY_NAME:
                return 'ERROR JSON_ERROR_INVALID_PROPERTY_NAME';
                break;
            default:
                return '- Unknown error';
                break;
        }
    }
}

