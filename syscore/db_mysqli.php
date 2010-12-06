<?php
class DB_Exception extends mysqli_sql_exception {}

class DB_MySQLi extends DB
{
    private $mysqli;

    function __construct($db_host, $db_username, $db_password, $db_name) {
        $this->mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);
    }

    public function query($query) {
        return $this->mysqli->query($query);
    }

    public function update($query) {
        if (!$this->mysqli->query($query)) {
            printf("Error: %s\n", $this->mysqli->error);
            die();
        }
    }

    public function prepare($query) {
        return $this->mysqli->prepare($query);
    }

    public function bind_parameters() {
        $args = func_get_args();
        $stmt = array_shift($args);

        call_user_func_array($stmt, 'bind_param', $args);
    }

    public function escape($val) {
        return $this->mysqli->real_escape_string($val);
    }

    public function get_insert_id() {
        return $this->mysqli->insert_id;
    }

    public function get_row_count() {
        return $this->mysqli->affected_rows;
    }

    public function get_rows($res) {
        $rows = array();
        while( $o = $res->fetch_object() ) {
            $rows[] = $o;
        }
        $res->close();
        return $rows;
    }

    public function get_row($res) {
        $row = $res->fetch_object();
        $res->close();
        return $row;
    }

    public function close() {
        return $this->mysqli->close();
    }
}
?>