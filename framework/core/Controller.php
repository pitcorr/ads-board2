<?php

class Controller
{
    protected $_view;
    public $model;
    protected $_name;

    public function __construct($name)
    {
        $this->acl = new Acl();
        $this->_name = $name;
        $this->_view = new View();
        if (Registry::has('model')) {
            $this->model = Registry::get('model');
        }
    }

    public function view($tpl, $data = [], $layout = '/application/views/layout/layout.phtml')
    {
        $this->_view->assign($tpl, $data, $layout);
        $this->_view->render();
    }

    public function redirect($host)
    {
        header('Location:' . $host);
    }
}