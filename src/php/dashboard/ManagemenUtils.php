<?php


namespace php\dashboard;


use DatabaseAdapter;
use Key;

class ManagemenUtils {

    private DatabaseAdapter $db;

    /**
     * ManagemenUtils constructor.
     * @param DatabaseAdapter $db
     */
    public function __construct($db) {
        $this->db = $db;
    }

    /**
     * @param $plz
     * @return int id of marker if exists, -1 if nonexistent
     */
    public function markerExistsAndGetId($plz): int {
        $cityId = $this->db->getStringFromTable("cities", "id", new Key("plz", $plz)); // ZIP Codes with multiple entries will return the first result
        if ($this->db->containsEntry("markers", new Key("city_id", $cityId))) {
            $markerId = $this->db->getStringFromTable("markers", "id", new Key("city_id", $cityId));
            return $markerId;
        } else {
            return -1;
        }

    }

}