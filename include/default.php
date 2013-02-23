<?php

include('config.php');

function __autoload( $class ) {
    include( __DIR__ . '/classes/' . $class . '.class.php' );
}

?>