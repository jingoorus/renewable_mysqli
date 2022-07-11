<?php
/**
 * Библиотека "Менеджер соединения"
 * Позволяет обновлять соединение в случае его потери
 * является "прокси классом", можно использовать как Extendmysqli || mysqli
 */
require_once 'extendmysqli.class.php';

class ConnectionManager
{
    private $data = [];

    private $connection = null;

    public function __construct(string $hostname, string $username, string $password, string $database)
    {
        $this->data = [

            $hostname,
            
            $username,
            
            $password,
            
            $database
        ];
    }

    public function query(string $query)
    {
        if ( is_null( @$this->connection ) || //Если первый запуск
             false === @$this->connection->stat() || //Если коннект напрочь отвалился
             is_null( @$this->connection->stat() ) ) { //Если коннект отвалился но процесс еще не выгружен

            $this->connection = new Extendmysqli($this->data[0], $this->data[1], $this->data[2], $this->data[3]);
            //$this->connection = new \mysqli($this->data[0], $this->data[1], $this->data[2], $this->data[3]);
            
            if ( $this->connection->connect_errno ) {

                trigger_error('<br><b>DB Connection in ConnectionManager.class error</b>: ' . $this->connection->connect_error, E_USER_ERROR);

                return null;
            }
        }

        return $this->connection->query($query);
    }

    public function __get(string $property_name)
    {
        /**
         * Перехватывает геттер и проверяет внутри соединения $connection наличие запрошенного свойства
         * 
         */
        if ( property_exists( $this->connection, $property_name ) ) {

            return $this->connection->$property_name;
        }

        return null;
    }
}
