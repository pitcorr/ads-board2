<?php

class Auth
{
    static function authentication()
    {
        session_start();
        if (empty($_SESSION['userRole'])) {
            $_SESSION['userRole'] = 'guest';
        }
    }
} 