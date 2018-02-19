<?php

$db = new mysqli("localhost", "root", "root", "pagination");

if($db->connect_error){
    die("Connect failed:".$db->connect_error);
}