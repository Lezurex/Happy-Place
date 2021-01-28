<?php


namespace php\objects;

use DatabaseAdapter;
use Key;

require_once  $_SERVER['DOCUMENT_ROOT'] . '/php/database/DatabaseAdapter.php';

class City {
    private string $name;
    private int $zip;
    private int $id;

    /**
     * City constructor.
     * @param int $id
     */
    public function __construct(int $id) {
        $this->id = $id;

        $db = new DatabaseAdapter();
        $data = $db->getStringsFromTable("cities", new Key("id", $id));

        $this->name = $data['name'];
        $this->zip = $data['plz'];
    }

    /**
     * @return string
     */
    public function getName(): mixed {
        return $this->name;
    }

    /**
     * @return int
     */
    public function getZip(): mixed {
        return $this->zip;
    }


}