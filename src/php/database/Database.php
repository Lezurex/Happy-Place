<?php


class Database {
    public static $host = "db";
    public static $username = "root";
    public static $password = "root";
    public static $port = 3306;
    public static $database = "happy-place";
    public static $con;

    /*
     * Connects to the database with credentials from database.json
     */
    public static function connect() {
        self::$con = mysqli_connect(self::$host, self::$username, self::$password, self::$database);
        self::$con->set_charset("utf8");
    }

    public static function disconnect() {
        if(self::isConnected()) {
            mysqli_close(self::$con);
        }
    }

    public static function isConnected() {
        return self::$con != null;
    }

    /**
     * @return mysqli connection
     */
    public static function getConnection() {
        return self::$con;
    }

}