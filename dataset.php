<?php

class WPPlayerlist
{

    private $prefixedTableName;

    public function __construct($tableName)
    {
        
        global $wpdb;
        $this->prefixedTableName = $wpdb->prefix . $tableName;
    }



    public function initialise()
    {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();
        $sqlCreateTable = "CREATE TABLE IF NOT EXISTS $this->prefixedTableName (
        `id` mediumint(9) NOT NULL AUTO_INCREMENT,
        `list_player` text NOT NULL,
    UNIQUE (`id`)
    ) $charset_collate;";
        dbDelta($sqlCreateTable);
    }

    public function select($limit)
    {
        global $wpdb;

        $sqlSelect = $wpdb->prepare(
            "SELECT * FROM $this->prefixedTableName as T ORDER BY T.id DESC LIMIT %d",
            $limit);

        return $wpdb->get_results($sqlSelect, OBJECT);
    }

    public function insert($player)
    {
        global $wpdb;

        
        return $wpdb->insert(
            $this->prefixedTableName,
            array(
                'list_player' => $player
            )
        );
    }
}