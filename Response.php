<?php
require_once('./HttpStatusCode.php');

/**
 * Default return structure for HTTP requests.
 */
class Response
{
    /**
     * Default return structure.
     *
     * @var array
     */
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

    /**
     * Returns success for the request.
     *
     * @param string $cMessage Message of any content.
     * @param integer $nStatusCode Requisition status code.
     * @param array|string|number|boolean|object $aData Return information, which can be any data type.
     * @param string|null $cPrevious Information related to any errors that have occurred.
     * @return void
     */
    static function success($cMessage = '', $nStatusCode = 200, $aData = array(), $cPrevious = null)
    {
        static::setResponse($cMessage, $nStatusCode, $aData, $cPrevious);
        echo json_encode(static::$aResponse);
        die();
    }

    /**
     * Returns an error for the request.
     *
     * @param string $cMessage Message of any content.
     * @param integer $nStatusCode Requisition status code.
     * @param array|string|number|boolean|object $aData return information, which can be any data type.
     * @param string|null $cPrevious Information related to any errors that have occurred.
     * @return void
     */
    static function error($cMessage = '', $nStatusCode = 400, $aData = array(), $cPrevious = null)
    {
        static::setResponse($cMessage, $nStatusCode, $aData, $cPrevious);
        echo json_encode(static::$aResponse);
        die();
    }

    /**
     * Configures the return structure, as well as the status code and message in the response header.
     *
     * @param string $cMessage Message of any content.
     * @param integer $nStatusCode Requisition status code.
     * @param array|string|number|boolean|object $aData return information, which can be any data type.
     * @param string|null $cPrevious Information related to any errors that have occurred.
     * @return void
     */
    private static function setResponse($cMessage, $nStatusCode, $aData, $cPrevious)
    {
        $bSuccess = $nStatusCode < 400 ? true : false;
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
