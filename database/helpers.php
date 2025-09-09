<?php 
    function get_server_url(){
        //grabbing protocol
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== "off" || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";

        //adding the domain name
        $domain_name = $_SERVER['HTTP_HOST'];

        //$url = $protocol.$domain_name;
        $url = $protocol.$domain_name;

        return $url;
    }