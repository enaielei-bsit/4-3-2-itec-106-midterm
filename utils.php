<?php
    function render($file, $params=[]) {
        foreach($params as $key => $value) {
            global ${$key};
            ${$key} = $value;
            // $GLOBALS[$key] = $value;
        }

        include($file);
    }

    function stitch(...$args) : string {
        return join(
            " ",
            array_filter($args,
                fn($e) => is_string($e) && strlen(trim($e)) > 0));
    }

    function check($array, $key, $value) {
        if(!isset($array[$key])) return false;
        if(is_callable($value)) return $value($array[$key]);
        return $array[$key] == $value;
    }

    function pop(&$array, ...$keys) {
        $vals = [];
        foreach($keys as $key) {
            if(isset($array[$key])) {
                array_push($vals, $array[$key]);
                unset($array[$key]);
            }
        }
        $c = count($vals);
        return $c == 0 ? null : ($c == 1 ? $vals[0] : $vals);
    }

    function keep(&$array, ...$keys) {
        $rkeys = array_filter($keys, fn($k) => !in_array($k, $keys));
        return pop($array, ...$rkeys);
    }

    function apply(&$array, $callback) {
        if(!is_callable($callback)) return;
        foreach($array as $key => $val) {
            $array[$key] = $callback($key, $val);
        }
    }

    // Source: https://stackoverflow.com/a/30021074/14733693
    function getFullHost() {
        $protocole = $_SERVER['REQUEST_SCHEME'].'://';
        $host = $_SERVER['HTTP_HOST'] . '/';
        $project = explode('/', $_SERVER['REQUEST_URI'])[1];
        return $protocole . $host . $project;
    }
?>