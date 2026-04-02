<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use Webklex\IMAP\Facades\Client;
// use App\Models\Client;

class EmailController extends Controller
{
    public function fetchEmails() {
        $client = Client::account('default');
        $client->connect();

        $folder = $client->getFolder('INBOX');
        $messages = $folder->messages()->all()->limit(5)->get();

        foreach ($messages as $message) {
            echo $message->getSubject();
            echo "<br>";
        }
    }
}

