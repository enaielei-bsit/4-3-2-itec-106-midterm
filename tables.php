<?php
    require_once("./database.php");

    $queries = [
        // users
        "create table if not exists users(
                id serial,
                email character varying not null,
                name character varying,
                password character varying,
                PRIMARY KEY (id)
        );"
    ];

    foreach($queries as $query) {
        try {
            query($query);
        } catch(Throwable $th) {
            die("Something went wrong while setting up the Database");
        }
    }
?>