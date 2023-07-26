<?php

    if (!isset($_GET["path"])) {
        include("no_path.html");
        die();
    }

    $path = "/".$_GET["path"];


    // inform meeting service to update download counter

    $API_URL = getenv("SR_MEETING_URL");
    if ($API_URL) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $API_URL."/file/increment");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $path);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: SwimResults',
            'Content-Type: text/plain'
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);

        curl_close($ch);
    }





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