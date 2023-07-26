<?php

    $path = "/".$_GET["path"];

    $filename = substr($path, strrpos($path, "/") + 1);


    $attachment_location = $_SERVER["DOCUMENT_ROOT"];
    if ($_SERVER["SERVER_NAME"] == "localhost")
        $attachment_location .= "/download/src";

    $attachment_location .= "/file_mount";
    $attachment_location .= $path;


    if (file_exists($attachment_location)) {

        header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
        header("Cache-Control: public"); // needed for internet explorer
        header("Content-Type: application/octet-stream");
        header("Content-Transfer-Encoding: Binary");
        header("Content-Length:".filesize($attachment_location));
        header("Content-Disposition: attachment; filename=".$filename);
        readfile($attachment_location);
        die();
    } else {
        die("Error: File not found.");
    }