<?php
/**
 * A tasks list data model class that use csv file as persistance.
 */
class Tasks extends CSV_Model {
    /**
     * constructor
     */
    public function __construct()
    {
        parent::__construct(APPPATH . '../data/tasks.csv', 'id');
    }

}
