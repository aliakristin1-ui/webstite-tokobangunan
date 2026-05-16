<?php
include_once "koneksi.php";

class Dml extends Koneksi {

    private $fields = [];
    private $table;
    private $where;
    private $order;
    private $limit;
    private $data;

    public function select()
    {
        $this->fields = func_get_args();
        return $this;
    }

    public function from_into($table)
    {
        $this->table = $table;
        return $this;
    }

    public function where($where)
    {
        $this->where = $where;
        return $this;
    }

    public function order($order)
    {
        $this->order = $order;
        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    public function get()
    {
        $query = "SELECT ";

        if (empty($this->fields)) {

            $query .= "* ";

        } else {

            $query .= join(', ', $this->fields);
        }

        $query .= " FROM " . $this->table;

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->where;
        }

        if (!empty($this->order)) {
            $query .= " ORDER BY " . $this->order;
        }

        if (!empty($this->limit)) {
            $query .= " LIMIT " . $this->limit;
        }

        $q = $this->db->prepare($query);

        $q->execute();

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function insert($data)
    {
        $this->data = $data;
        return $this;
    }

    private function getFields($data)
    {
        return implode(',', array_keys($data));
    }

    private function getEntries($data)
    {
        return implode(',', array_fill(0, count($data), '?'));
    }

    public function create()
    {
        $fields = $this->getFields($this->data);

        $placeholders = $this->getEntries($this->data);

        $query = "
            INSERT INTO {$this->table}
            ($fields)
            VALUES
            ($placeholders)
        ";

        $q = $this->db->prepare($query);

        $q->execute(array_values($this->data));

        return $this->db->lastInsertId();
    }

    public function updateData($data)
    {
        $this->data = $data;
        return $this;
    }

    public function set()
    {
        $setParts = [];

        $values = [];

        foreach ($this->data as $k => $v) {

            $setParts[] = "$k=?";

            $values[] = $v;
        }

        $query = "
            UPDATE {$this->table}
            SET " . implode(',', $setParts) . "
            WHERE {$this->where}
        ";

        $q = $this->db->prepare($query);

        $q->execute($values);
    }

    public function deleteData()
    {
        return $this;
    }

    public function del()
    {
        $query = "
            DELETE FROM {$this->table}
            WHERE {$this->where}
        ";

        $q = $this->db->prepare($query);

        $q->execute();
    }

    public function query($sql, $params = [])
    {
        $q = $this->db->prepare($sql);

        $q->execute($params);

        return $q->fetchAll(PDO::FETCH_ASSOC);
    }

    public function queryOne($sql, $params = [])
    {
        $q = $this->db->prepare($sql);

        $q->execute($params);

        return $q->fetch(PDO::FETCH_ASSOC);
    }

    public function execute($sql, $params = [])
    {
        $q = $this->db->prepare($sql);

        return $q->execute($params);
    }

    public function getInsertId()
    {
        return $this->db->lastInsertId();
    }
}
?>