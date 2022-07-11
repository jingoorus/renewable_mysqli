<?php
/**
 * Расширение стандартного mysqli
 * 
 */
final class Extendmysqli extends \mysqli
{
    public function query($query, $p = NULL) 
    {
        $query_result = parent::query($query, $p);
    
        if ( !$this->errno ) {

            if ( $query_result instanceof \mysqli_result ) {
                /**
                 * В большинстве случаев нам нужен массив с данными
                 * после чего мы высвобождаем память от результатов запроса
                 */
                $data = $query_result->fetch_all(MYSQLI_ASSOC);

                $result = new ExtendmysqliResult;

                $result->num_rows = $query_result->num_rows;

                $result->row = isset($data[0]) ? $data[0] : [];

                $result->rows = $data;

                $query_result->free();

                return $result;

            } else {
                /**
                 * Или же это будет true || false или ошибка
                 */
                return $query_result;
            }

        } else {
            /**
             * или ошибка
             */
            trigger_error('<br><b>Query</b>: ' . $query . "<br>error: " . $this->error, E_USER_ERROR);

            return null;
        }
        
    }
    
    public function getLastId()
    {
        return $this->insert_id;
    }
    
    public function escape($value) 
    {
	    return $this->real_escape_string($value);
    }
    
    public function clean($value) 
    {
		return $this->escape($value);
	}
}

final class ExtendmysqliResult
{
    public $num_rows = null;

    public $row      = [];

    public $rows     = [];
}
