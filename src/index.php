<?php

    if (!isset($_GET["path"])) {
        include("no_path.html");
        die();
    }


    // Resolve the download root, mirroring the previous localhost handling.
    $download_root = $_SERVER["DOCUMENT_ROOT"];
    if ($_SERVER["SERVER_NAME"] == "localhost")
        $download_root .= "/download/src";
    $download_root .= "/file_mount";

    // Containment: resolve both the base directory and the requested target and
    // make sure the target stays strictly inside file_mount. realpath() collapses
    // "../" and resolves symlinks, so traversal outside the mount yields a path
    // that fails the prefix check below (or realpath() returns false).
    $base_real = realpath($download_root);
    $target_real = ($base_real === false) ? false : realpath($download_root . "/" . $_GET["path"]);

    if ($base_real === false
        || $target_real === false
        || strpos($target_real, $base_real . DIRECTORY_SEPARATOR) !== 0
        || !is_file($target_real)) {
        header($_SERVER["SERVER_PROTOCOL"] . " 404 Not Found");
        die("Error: File not found.");
    }

    // Path relative to file_mount (leading slash kept for backwards compatibility
    // with the meeting service). Derived from the validated path, not user input.
    $relative_path = substr($target_real, strlen($base_real));
    $filename = basename($target_real);


    // inform meeting service to update download counter

    $API_URL = getenv("SR_MEETING_URL");
    $API_KEY = getenv("SR_SERVICE_KEY");
    if ($API_URL) {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $API_URL."/file/increment");
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $relative_path);
        curl_setopt($ch, CURLOPT_TIMEOUT, 3); //timeout in seconds

        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'User-Agent: SwimResults',
            'Content-Type: text/plain',
            'X-Swimresults-Service: '.$API_KEY
        ]);

        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $content = curl_exec($ch);

        curl_close($ch);
    }

    header($_SERVER["SERVER_PROTOCOL"] . " 200 OK");
    header("Cache-Control: public"); // needed for internet explorer

    function getContentType($filename) {
        $finfo = new finfo(FILEINFO_MIME);
        return $finfo->file($filename);
    }
    header("Content-Type: " . getContentType($target_real));

    //header("Content-Type: application/octet-stream");
    header("Content-Transfer-Encoding: Binary");
    header("Content-Length:".filesize($target_real));
    //header("Content-Disposition: attachment; filename=".$filename); forces browser to download
    readfile($target_real);
    die();
