<?php


namespace php\objects;

use DatabaseAdapter;
use JetBrains\PhpStorm\Pure;
use Key;

require_once  $_SERVER['DOCUMENT_ROOT'] . '/php/database/DatabaseAdapter.php';

class Student {
    private $firstname;
    private $lastname;
    private $id;

    /**
     * Student constructor.
     * @param $id
     */
    public function __construct($id) {
        $this->id = $id;
        $db = new DatabaseAdapter();
        $studentData = $db->getStringsFromTable("students", new Key("id", $id));

        $this->firstname = $studentData['firstname'];
        $this->lastname = $studentData['lastname'];
    }

    /**
     * @return mixed
     */
    public function getFirstname(): mixed {
        return $this->censorString($this->firstname);
    }

    /**
     * @return mixed
     */
    public function getLastname(): mixed {
        return $this->censorString($this->lastname);
    }

    public function toArray() {
        return array(
            "firstname" => $this->censorString($this->firstname),
            "lastname" => $this->censorString($this->lastname)
        );
    }

    /**
     * Censors every letter of a string, except for the first letter
     * @param string $string
     * @return string
     */
    #[Pure] function censorString($string): string {
        $chars = preg_split('//', $string, -1, PREG_SPLIT_NO_EMPTY);
        $return_string = "";
        $count = 0;
        foreach ($chars as $char) {
            if ($count == 0) {
                $return_string .= $char;
            } else {
                $return_string .= "*";
            }
            $count++;
        }
        return $return_string;
    }

}