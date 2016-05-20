<?php
(defined('BASEPATH')) or exit('No direct script access allowed');

/* load the HMVC_Loader class */
require APPPATH . 'third_party/HMVC/Loader.php';

class MY_Loader extends HMVC_Loader {
    /**
     * Instant Controller Pointer
     *
     * set a pointer linking to instant controller using get_instant() - codeigniter's function
     */
    protected $CI;

    /**
     * Layout name
     */
    protected $layout = '';

    /**
     * Layout data
     */
    protected $layout_data = array();

    /**
     * Content-Type requested
     */
    protected $contentType = 'html';

    // --------------------------------------------------------------------

    /**
     * Constructor
     *
     * Setup value for Properties
     */
    public function __construct() {
        parent::__construct();

        $this->CI = & get_instance();

        if(!empty($this->CI->layout)){
            $this->layout = $this->CI->layout;
        }

        if(!empty($this->CI->layout_data)) {
            $this->layout_data = $this->CI->layout_data;
        }

        $this->get_content_type();
    }

    // --------------------------------------------------------------------

    /**
     * Set Layout
     *
     * @param string $layout name of layout
     *
     * @return $this;
     */
    public function set_layout($layout) {
        $this->layout = $layout;
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Get Layout
     *
     * @return string layout name
     */
    public function get_layout_name() {
        return $this->layout;
    }

    // --------------------------------------------------------------------

    /**
     * Set property value
     *
     * @param mixed $property propeties need setup value
     * @param mixed $value value of property
     *
     * @return $this;
     */
    public function set($property, $data = '') {
        if(!is_array($property)) {
            $property = array($property => $data);
        }

        if(!empty($property)) {
            foreach ($property as $key => $value) {
                $this->{$key} = $value;
            }
        }
        
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Set Data Push Into Layout
     *
     * @param mixed $property propeties need setup value
     * @param mixed $value value of property
     *
     * @return $this;
     */
    public function set_layout_data($property, $data = '') {
        if(!is_array($property)) {
            $property = array($property => $data);
        }

        $this->layout_data = array_merge($this->layout_data, $property);
        return $this;
    }

    // --------------------------------------------------------------------

    /**
     * Get Data Pushed Into Layout
     *
     * @return array
     */
    public function get_layout_data() {
        return $this->layout_data;
    }

    // --------------------------------------------------------------------

    /**
     * Get HTTP Content Type
     *
     * @Description: Content Type will be used to resolved data type respond for client
     *
     * @todo get variable HTTP_CONTENT_TYPE frome global variable $_SERVER
     */
    public function get_content_type() {
        if(!($contentType = filter_input(INPUT_SERVER, 'HTTP_RESPOND_CONTENT_TYPE')) === false){
            switch (strtolower($contentType)) {
                case 'json':case 'application/json':
                    $this->contentType = 'json';
                    break;
                case 'xml':case 'application/xml':
                    $this->contentType = 'xml';
                    break;
                case 'htm':case 'html': case 'php':case 'text/html':
                    break;
                default:
                    $this->contentType = $contentType;
            }
        }

        return $this->contentType;
    }

    // --------------------------------------------------------------------

    /**
     * Get layout directory path
     *
     * Layout directory contain all of layout file
     *
     * @param string layout name
     *
     * @return string Layout directory path
     */
    private function _layoutPath($layout) {
        $layoutPath = 'layouts' . DIRECTORY_SEPARATOR . $layout;
        return $layoutPath;
    }

    // --------------------------------------------------------------------

    /**
     * Render View
     *
     * @todo Render View And Include Into The template layout
     *
     * @param string $view - name or path of view
     * @param array $data - data push into view
     * @param bool $return - if true: get result to string; Else: express instant
     *
     * @return string (if $return true)
     */
    public function render($view, $data = array(), $return = FALSE) {
        if(!empty($this->get_layout_name()) && $this->contentType == 'html') {
            $input['ViewContentHTML'] = $this->CI->load->view($view, $data, TRUE);
            $this->set_layout_data($input);
            return $this->CI->load->view($this->_layoutPath($this->layout), $this->layout_data, $return);
        }else {
            return $this->CI->load->view($view, $data, $return);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Render View Sector
     *
     * @todo Render View And Include Into The template layout don't use My_Loader's Properties
     *
     * @param string $view - name or path of view
     * @param array $data - data push into view
     * @param string $layout - using another layout
     * @param array $input - data push into layout
     * @param bool $return - if true: get result to string; Else: express instant
     *
     * @return string (if $return true)
     */
    public function renderSector($view, $data = array(), $layout = '', $return = false) {

        if(!empty($layout) && $this->contentType == 'html') {
            $input['ViewContentHTML'] = $this->CI->load->view($view, $data, TRUE);
            $this->set_layout_data($input);
            return $this->CI->load->view($this->_layoutPath($layout), $this->layout_data, $return);
        }else {
            return $this->CI->load->view($view, $data, $return);
        }
    }

    // --------------------------------------------------------------------

    /**
     * Load and Binding View
     *
     * @todo Check http header Content_type to resolved respond's data accord
     *
     * @param string $view - name or path of view
     * @param array $vars - data push into view
     * @param bool $return - if true: get result to string; Else: express instant
     *
     * @return string (if $return true)
     */
    public function view($view, $vars = array(), $return = FALSE) {
        switch (strtolower($this->contentType)) {
            case 'json':
                $this->CI->output->set_output(json_encode($vars, true));
                break;
            case 'xml':
                // creating object of SimpleXMLElement
                $xml_data = new SimpleXMLElement('<root/>');

                // function call to convert array to xml
                array_walk_recursive($vars, array($xml_data, 'addChild'));

                //saving generated xml file;
                $xml = $xml_data->asXML();

                $this->CI->output->set_output($xml);
                break;
            default:
                return parent::view($view, $vars, $return); // TODO: Change the autogenerated stub
        }
    }

    // --------------------------------------------------------------------

}