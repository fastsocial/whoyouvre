<?php

class DbObject {
    private $collection;
    private $fields;
    private $tableName;
    private $data;
    private $affectedFields = array();
    private $primaryKey;
    private $db;
    private $isNew;

    public function __construct($data, $fields, $tableName, $primaryKey, $db, $isNew = false, $collection = null) {
        $this->data = $data;
        $this->fields = $fields;
        $this->tableName = $db->escape($tableName);
        $this->primaryKey = $db->escape($primaryKey);
        $this->db = $db;
        $this->isNew = $isNew;
        $this->collection = $collection;
    }

    public function id() {
        $primaryKey = $this->primaryKey;
        return $this->$primaryKey;
    }

    public function __get($fieldName) {
        if ( in_array($fieldName, $this->fields) && isset($this->data[$fieldName])) {
            return $this->data[$fieldName];
        }

        return false;
    }

    public function __set($field, $value) {
        if ( in_array($field, $this->fields) && isset($this->data[$field])) {
            if ($this->data[$field] != $value) {
                $this->affectedFields[$field] = true;
                $this->data[$field] = $value;
            }
        }
    }

    public function toArray() {
        return $this->data;
    }

    public function delete() {
        $primaryKey = $this->primaryKey;
        $id = $this->$primaryKey;
        $query = "delete from `$this->tableName` where ".$primaryKey." = '".$this->db->escape($id)."'";
        $this->db->query($query);
        if ($this->collection) {
           $this->collection->deleteFromCollection($id);
        }
    }

    public function save() {
        if (!$this->isNew) {
            if (count($this->affectedFields)) {
                $values = array();
                foreach ($this->affectedFields as $field => $val) {
                    $values[] = "`".$this->db->escape($field)."` = '".$this->db->escape($this->data[$field])."'";
                }

                $primaryKey = $this->primaryKey;
                $query = "update `$this->tableName` set ".implode(" ", $values)." where ".$primaryKey." = '".$this->db->escape($this->$primaryKey)."'";
                $this->db->query($query);

                return $this;
             }
        } else {
            $values = array();
            $fields = array();
            $primaryKey = $this->primaryKey;

            foreach ($this->fields as $field) {
                if ($field == $primaryKey || !isset($this->data[$field])) continue;
                $fields[] = "`".$this->db->escape($field)."`";
                $values[] = "'".$this->db->escape($this->data[$field])."'";
            }

            $query = "insert into ".$this->tableName."(".implode(",", $fields).") values(".implode($values).")";
            $res = $this->db->query($query);
            $this->$primaryKey = $this->db->last_insert_id();

            return $this;
        }

        return false;
    }
};

class DbCollection {
    protected $tableName;
    protected $fields;
    protected $primaryKey = "id";
    private $db;
    private $res;
    private $collection;

    public function __construct($db, $fields, $tableName) {
        $this->db = $db;
        $this->fields = $fields;
        $this->tableName = $tableName;
        $this->collection = array();
        $this->init();
    }

    public function create($data) {
        $allFields = array_merge(array($this->primaryKey), $this->fields);
        $obj  = new DbObject($data, $allFields, $this->tableName, $this->primaryKey, $this->db, true);
        $obj = $obj->save();
        $this->collection[$obj->id()] = $obj;
        return $obj;
    }

    private function init() {
        $allFields = array_merge(array($this->primaryKey), $this->fields);
        $fields = '';
        foreach ($allFields as $i => $field) {
            $allFields[$i] = "`$field`";
        }
        $fields = implode(',', $allFields);
        try {
            $this->res = $this->db->query("select ".$this->db->escape($fields)."from `".$this->db->escape($this->tableName)."`");
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }

    public function deleteFromCollection($id) {
        unset($this->collection[$id]);
    }

    public function getNext() {
        $allFields = array_merge(array($this->primaryKey), $this->fields);
        $arr = $this->res->fetch_array(MYSQLI_ASSOC);
        if ($arr) {
            $this->collection[$arr[$this->primaryKey]] = new DbObject($arr, $allFields, $this->tableName, $this->primaryKey, $this->db, false, $this);
            return $arr;
        }

        return false;
    }

    public function getAll() {
        while (is_array($this->getNext())) {}
        return $this->collection;
    }

}

class DbCollectionInterface extends DbCollection {
    function __construct ($db, $fields) {
        $tableName = "object_".mb_strtolower(get_class($this));
        parent::__construct($db, $fields, $tableName);
    }
}