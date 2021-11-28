<?php


class Model{
    protected static $tableName = '';
    protected static $columns = [];
    protected $values = [];
    
    function __construct($arr)
    {
       $this->loadFromArray($arr);
    }

    public function loadFromArray($arr) {
        if ($arr) {
            foreach($arr as $key => $value) {
                $this->$key = $value;
            }
        }
    }

    public function __get($key) {
        return $this->values[$key];
    }

    public function __set($key, $value) {
        $this->values[$key] = $value;
    }

    public static function getOne($filters = [], $columns = '*') {
        $class = get_called_class(); //esta função pega a classe que chamou a função.
        $result = static::getResultSetFromSelect($filters, $columns);
        return $result ? new $class($result->fetch_assoc()) : null ;
    }
    public static function get($filters = [], $columns = '*') {
        $objects = [];
        $result = static::getResultSetFromSelect($filters, $columns);
         if($result) {
            $class = get_called_class(); //esta função pega a classe que chamou a função.
            while($row = $result->fetch_assoc()) {
                array_push($objects, new $class($row)); //Neste caso, a função cria um objeto da classe dentro de $objects.
            }
         }
         return $objects;
    }

    public static function getResultSetFromSelect($filters = [], $columns = '*') {
        
        $sql = "SELECT ${columns} FROM " 
            . static::$tableName
            . static::getFilters($filters);
        $result = Database::getResultFromQuery($sql);
            if($result === 0) {
                return null;
            } else {
                return $result;
            }
                
    }

    public static function getFilters($filters) {
        $sql = '';

        if(count($filters) > 0) {
            $sql .= " WHERE 1 = 1";
            foreach($filters as $column => $value) {
                $sql .= " AND ${column} = " . static::getFormatedValue($value);
            }
        }
        return $sql;
    }

    private static function getFormatedValue($value) {
        if(is_null($value)) {
            return "null";
        } elseif(gettype($value) === 'string') {
            return "'${value}'";
        }else {
            return $value;
        }
    }
}   
