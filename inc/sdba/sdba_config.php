<?php

class Sdba_config
{
    public static $dbname = 'discipulado_db'; // Your database name
    public static $dbuser = 'root'; // Your database username
    public static $dbpass = ''; // Your database password
    public static $dbhost = 'localhost'; // Your database host, 'localhost' is default.
    public static $dbencoding = 'utf8mb4'; // Your database encoding
    
    public static $autoreset = true; // Auto-resets conditions when you try to set new (after some db action, true is recommended);
}