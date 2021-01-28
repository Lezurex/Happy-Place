<?php


namespace php\objects;

require_once  $_SERVER['DOCUMENT_ROOT'] . '/php/database/DatabaseAdapter.php';
require_once 'City.php';
require_once 'Student.php';

class Marker {
    private float $lat;
    private float $lng;
    private array $students;
    private int $id;
    private City $city;

    /**
     * Marker constructor.
     * @param int $id
     * @param $lat float Latitude
     * @param $lng float Longitude
     * @param int $cityId
     * @param array $studentIds
     */
    public function __construct(int $id, float $lat, float $lng, int $cityId, array $studentIds) {
        $this->lat = $lat;
        $this->lng = $lng;
        $this->id = $id;
        $this->students = array();
        foreach ($studentIds as $studentId) {
            array_push($this->students, new Student($studentId));
        }

        $this->city = new City($cityId);
    }

    /**
     * Turn a marker into a JSON string
     * @return string
     */
    public function toJSON(): string {
        return json_encode([
                'lat' => $this->lat,
                'lng' => $this->lng,
                'students' => $this->studentsToArray(),
                'name' => $this->city->getName(),
                'zip' => $this->city->getZip()
            ]) . ",";
    }

    /**
     * Turns students into json_encode-readable arrays
     * @return array
     */
    private function studentsToArray(): array {
        $students = array();
        foreach ($this->students as $student) {
            array_push($students, $student->toArray());
        }
        return $students;
    }
}