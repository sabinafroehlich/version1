<?php

/**
 * Description of SemanticWebApiCall
 *
 * @author sabinafrohlich
 */
class Tx_Semantlink_Service_SemanticWebApiCall {

    /**
     * @var string 
     */
    protected $url = "http://api.zemanta.com/services/rest/0.0/"; //Should be in a conf file
    protected $format = 'xml'; // May depend of your application context
    protected $text = ""; // May depend of your application context
    protected $key; //Should be in a conf file
    protected $method = "zemanta.suggest"; // May depend of your application context
    
    public function __construct($key) {
        $this->key = $key;
    }

    /**
     * 
     * @return array
     */

    public function callZemantaApi($userInput) {
        $url = 'http://api.zemanta.com/services/rest/0.0/'; //Should be in a conf file
        $format = 'xml'; // May depend of your application context
        $text = $userInput; // May depend of your application context
        $key = "7fgytz3vkiakiqjijvjsdjmq"; //Should be in a conf file
        $method = "zemanta.suggest"; // May depend of your application context

        /* It is esayer to deal with arrays */
        $args = array(
            'method' => $method,
            'api_key' => $key,
            'text' => $text,
            'format' => $format,
            'return_categories' => 1,
            'return_images' => 0,
            'return_keywords' => 1,
            'articles_limit' => 0,
        );

        /* Here we build the data we want to POST to Zementa */
        $data = "";
        foreach ($args as $key => $value) {
            $data .= ($data != "") ? "&" : "";
            $data .= urlencode($key) . "=" . urlencode($value);
        }

        /* Here we build the POST request */
        $params = array('http' => array(
                'method' => 'POST',
                'Content-type' => 'application/x-www-form-urlencoded',
                'Content-length' => strlen($data),
                'content' => $data,
                ));

        /* Here we send the post request */
        $ctx = stream_context_create($params); // We build the POST context of the  * request
        $fp = @fopen($url, 'rb', false, $ctx); // We open a stream and send the request
        if ($fp) {
            /* Finaly, herewe get the response of Zementa */
            $response = @stream_get_contents($fp);
            if ($response === false) {
                $response = "1Problem reading data from " . $url . ", " . $php_errormsg;
            }
            fclose($fp); // We close the stream
        } else {
            $response = "2Problem reading data from " . $url . ", " . $php_errormsg;
        }

        /*todo: ab hier umschreiben*/
        $simpleXml = new SimpleXMLElement($response);

        $tempKeywords = (array) $simpleXml->xpath("//keyword");
        $keywords = array();
        foreach ($tempKeywords as $item) {
            array_push($keywords, (array) $item);
        }


        $responseArray['keywords'] = $keywords;

        $tmpCats = (array) $simpleXml->xpath("//categories");
        $categories = array();
        foreach ($tmpCats as $item) {
            array_push($categories, (array) $item);
        }
        $responseArray['categories'] = $categories;




        return $keywords;
    }

}

?>
