<?php
/**
 * @author Tomáš Blatný
 */

$database = new Database('user', 'heslo', 'test');
$database->query('SELECT * FROM user');
