<?php
const displayErrors = true;

//DB credentials
const DB_HOST = "localhost";
const DB_USER = "root";
const DB_PASS = "hasło";
const DB_NAME = "nazwa_bazy_danych";

if (displayErrors) {
	ini_set( 'display_errors', 'On' );
	error_reporting( E_ALL );
}

date_default_timezone_set('Europe/Warsaw'); // Ustawienie godziny serwera