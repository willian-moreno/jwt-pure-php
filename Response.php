<?php
require_once('./HttpStatusCode.php');

class Response
{
    private static $aResponse = array(
        'success' => true,
        'response' => array(
            'statusCode' => 200,
            'statusText' => '',
            'message' => '',
            'previous' => '',
            'data' => array()
        ),
    );

    static function success($cMessage = '', $nStatusCode = 200, $aData = array(), $cPrevious = null)
    {
        static::setResponse(true, $cMessage, $nStatusCode, $aData, $cPrevious);
        echo json_encode(static::$aResponse);
        die();
    }

    static function error($cMessage = '', $nStatusCode = 400, $aData = array(), $cPrevious = null)
    {
        static::setResponse(false, $cMessage, $nStatusCode, $aData, $cPrevious);
        echo json_encode(static::$aResponse);
        die();
    }

    private static function setResponse($bSuccess, $cMessage, $nStatusCode, $aData, $cPrevious)
    {
        $statusText = HttpStatusCode::getMessage($nStatusCode, true);

        header("HTTP/1.0 $nStatusCode $statusText");
        static::$aResponse['success'] = $bSuccess;
        static::$aResponse['response'] = array(
            'statusCode' => $nStatusCode,
            'statusText' => $statusText,
            'message' => mb_convert_encoding($cMessage, 'utf-8', 'iso8859-1'),
            'previous' => mb_convert_encoding($cPrevious, 'utf-8', 'iso8859-1'),
            'data' => $aData
        );
    }
}
