<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Works_model extends CI_Model {

    protected $table = "works";

    /**
     * Inserts data into the complaints table
     *
     * @param Array $data - the data to be inserted into the database (associative array)
     * @return Array that states whether the action was completed or not
     */
    public function save($data) {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Finds the rows that fulfill the condition
     *
     * @param Array/String $where - Name of field to compare (ex "id=4"), or associative array
     * @return Array containing the rows that match the constraint
     */
    public function find($where) {
        return $this->db->select('*')
                        ->from($this->table)
                        ->join('proposals', 'proposals.idproposal = ' . $this->table . '.proposalId')
                        ->where($where)
                        ->get()
                        ->result();
    }

    /**
     * Returns every row in the complaints table and their related households
     *
     * @return Array containing all the rows
     */
    public function getAll() {
        return $this->db->select('*')
                        ->from($this->table)
                        ->join('proposals', 'proposals.idproposal = ' . $this->table . '.proposalId')
                        ->get()
                        ->result();
    }

    /**
     * Get a quote for a particular proposal
     *
    * @param Array/String $where - Name of field to compare (ex "id=4"), or associative array
    * @return Array containing the quotation price
     */
    public function quote($where) {
        return $this->db->select_sum('amount')
                        ->from($this->table)
                        //->join('proposals', 'proposals.idproposal = ' . $this->table . '.proposalId')
                        ->join('quotations', 'quotation.proposalId = ' . $this->table . '.proposalId')
                        ->where($where)
                        ->get()
                        ->result();
    }

    /**
     * Shows a certain amount of rows (top down)
     *
     * @param Integer $nbre - number of rows to limit the results to
     * @param Integer $base - numer of rows to skip
     * @return Array containing the amount of rows you specified
     */
    public function show($nbre, $base = 0) {
        return $this->db->select('*')
                        ->from($this->table)
                        ->join('proposals', 'proposals.idproposal = ' . $this->table . '.proposalId')
                        ->limit($nbre, $base)
                        ->get()
                        ->result();
    }

    /**
     * Modifies existing records in the table
     *
     * @param Array $data - Array with data (field/value pairs)
     * @param Array/String $where - Name of field to compare (ex "id=4"), or associative array
     * @return Boolean - true on success, false on failure
     */
    public function update($data, $where) {
        return $this->db->where($where)
                        ->update($this->table, $data);
    }

    /**
     * Deletes rows form the table
     *
     * @param Array/String $where - Name of field to compare (ex "id=4"), or associative array, the rows you want to delete
     * @return BaseBuilder/Boolean - BaseBuilder instance (for method chaining) or FALSE on fail
     */
    public function delete($where) {
        return $this->db->where($where)
                        ->delete($this->table);
    }

    /**
     * Counts the number of rows in the entity with a particular value
     *
     * @param Array $data - what you want to match
     * @return Integer - number of rows
     */
    public function count($data = NULL) {
        return $this->db->where($data)
                        ->count_all($this->table);
    }

}
