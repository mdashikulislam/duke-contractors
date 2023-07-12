<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

class DocController extends Controller
{
    public function index()
    {
        $headers = array(
            "Content-type"=>"text/html",
            "Content-Disposition"=>"attachment;Filename=myGeneratefile.doc"
        );
        $content = '<html>

            <head><meta charset="utf-8"></head>

            <body>

                <p>My Blog Laravel 7 generate word document from html Example - Nicesnippets.com</p>

                <ul><li>Php</li><li>Laravel</li><li>Html</li></ul>

            </body>

            </html>';



        return \Response::make($content,200, $headers);

    }
}
