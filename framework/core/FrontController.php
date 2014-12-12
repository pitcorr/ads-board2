<?php

include CLS . 'Router.php';
include CLS . 'Requests.php';
include CLS . 'Tools.php';
include CLS . 'ChromePhp.php';
include CLS . 'ViewHelper.php';
include CLS . 'Config.php';
include CLS . 'Registry.php';

include DB . 'Database.php';
include ADB . 'UserConnection.php';

include MDL . 'UserModel.php';

include 'Controller.php';
include 'Model.php';
include 'View.php';

include 'Dispatcher.php';

class FrontController
{

    public static function run()
    {
        $dispatcher = new Dispatcher();
        $controller = $dispatcher->getController();
        $controller->action();

        ChromePhp::log(Registry::get('2'));
    }
} 