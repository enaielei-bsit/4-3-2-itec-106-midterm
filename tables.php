<?php
    require_once(__DIR__ . "/database.php");

    $queries = [
        // users
        "create table if not exists users(
                id serial,
                email character varying not null,
                name character varying,
                password character varying,
                PRIMARY KEY (id)
        );",

        // employees
        "CREATE TABLE employees
        (
            id serial,
            employee_id character varying NOT NULL,
            family_name character varying NOT NULL,
            given_name character varying NOT NULL,
            middle_name character varying,
            age bigint NOT NULL,
            sex boolean NOT NULL,
            mobile_number character varying NOT NULL,
            address character varying NOT NULL,
            PRIMARY KEY (id)
        );",

        // employees_archive
        "CREATE TABLE employees_archive
        (
            id serial,
            employee_id serial NOT NULL,
            PRIMARY KEY (id)
        );"
    ];

    foreach($queries as $query) {
        try {
            query($query);
        } catch(Throwable $th) {
            // die("Something went wrong while setting up the Database.<br>" . $th->getMessage());
        }
    }
?>