<?php

/* PHP OBJECT TEMPLATE
 * 
 * NAMING SCHEME: _ before private variables/functions, and not public.
 * 
 * @category    Template
 * @package     
 * @author      Roy Ronalds <roy.ronalds@gmail.com>
 * @author      
 * @link        http://someLinkToExampleUsage.com/ 
*/

/* Requires and require_onces.
*/

/*
 * Constant defines.
*/

/**
 * Class to use as a template when creating a new class.
 *
 * Simple example (used to write fetches yahoo.com and displays it):
 * <code>
 * example use of the code here
 * </code>
 *
 * @category    Template
 * @package     
 * @author      Roy Ronalds <roy.ronalds@gmail.com>
 */
class ObjectTemplate
{
   /**#@+
    * @access private
    */
    /**
    * Private access level variable
    * @var boolean
    */
    var $_privVar;
   /**#@-*/
   
    /**
    * Constructor
    *
    * Sets up the object
    * @param    string  The url to fetch/access
    * @param    array   Associative array of parameters which can have the following keys:
    * <ul>
    *   <li>method         - Method to use, GET, POST etc (string)</li>
    *   <li>http           - HTTP Version to use, 1.0 or 1.1 (string)</li>
    *   <li>user           - Basic Auth username (string)</li>
    *   <li>pass           - Basic Auth password (string)</li>
    *   <li>proxy_host     - Proxy server host (string)</li>
    *   <li>proxy_port     - Proxy server port (integer)</li>
    *   <li>proxy_user     - Proxy auth username (string)</li>
    *   <li>proxy_pass     - Proxy auth password (string)</li>
    *   <li>timeout        - Connection timeout in seconds (float)</li>
    *   <li>allowRedirects - Whether to follow redirects or not (bool)</li>
    *   <li>maxRedirects   - Max number of redirects to follow (integer)</li>
    *   <li>useBrackets    - Whether to append [] to array variable names (bool)</li>
    *   <li>saveBody       - Whether to save response body in response object property (bool)</li>
    *   <li>readTimeout    - Timeout for reading / writing data over the socket (array (seconds, microseconds))</li>
    *   <li>socketOptions  - Options to pass to Net_Socket object (array)</li>
    * </ul>
    * @access public
    */
    function HTTP_Request($url = '', $params = array())
    {
       $this->_method         =  HTTP_REQUEST_METHOD_GET;
        $this->_http           =  HTTP_REQUEST_HTTP_VER_1_1;
        $this->_requestHeaders = array();
        $this->_postData       = array();
        $this->_body           = null;

        $this->_user = null;
        $this->_pass = null;

        $this->_proxy_host = null;
        $this->_proxy_port = null;
        $this->_proxy_user = null;
        $this->_proxy_pass = null;

        $this->_allowRedirects = false;
        $this->_maxRedirects   = 3;
        $this->_redirects      = 0;

        $this->_timeout  = null;
        $this->_response = null;

        foreach ($params as $key => $value) {
            $this->{'_' . $key} = $value;
        }

		// *** Set default states for the variables, if any.

        if (!empty($url)) {
            $this->setURL($url);
        }
    }
    
    
    
    /* SPECIFIC SETS, IF ANY NEEDED BEYOND CONSTRUCTOR
    */
    
    
    /**
    * Generates a Host header for HTTP/1.1 requests
    *
    * @access private
    * @return string
    */
    function _generateHostHeader()
    {
        
        
        
    }
    
    
   /**
    * Returns the current request URL  
    *
    * @return   string  Current request URL
    * @access   public
    */
    function getUrl()
    {
        return empty($this->_url)? '': $this->_url->getUrl();
    }
    
} // End Class ObjectTemplate


// *** Put any internal classes or other classes for this file's library here.


?>
