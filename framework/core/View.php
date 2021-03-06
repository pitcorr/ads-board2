<?php

/**
 * Class View
 */
class View
{
    use ViewHelper;

    private $_tpl;
    private $_layout;
    private $_data;

    /**
     * Render page
     */
    public function render()
    {
        // data for page
        $data = $this->_data;

        ob_start();
        if (!@include(ROOT_PATH . '/application/views/' . $this->_tpl)) {
            include ROOT_PATH . '/application/views/error/error404.phtml';
        }
        $content = ob_get_clean();
        include ROOT_PATH . $this->_layout;
    }

    /**
     * Assign content page, data to show on page and layout
     *
     * @param $tpl
     * @param $data
     * @param $layout
     */
    public function assign($tpl, $data, $layout)
    {
        $tpl = strtolower(Tools::normalizeUrl($tpl, 'phtml'));

        $this->_tpl = file_exists(ROOT_PATH . '/application/views/' . $tpl) ? $tpl : null;

        $this->_data = $data;
        $this->_layout = $layout;
    }
}