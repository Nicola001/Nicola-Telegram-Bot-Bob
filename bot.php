<?php
///
///
/// Il Bot è online, cliccare il link sottostante per aprirlo 
/// http://t.me/bobbotnicola_bot
///
///
    date_default_timezone_set("Europe/Rome");
    //Webhook
    $content = file_get_contents("php://input");
    $update = json_decode($content, true);
    $chat_id = $update["message"]["chat"]["id"];
    $message = $update["message"]["text"];
    $message_id = $update["message"]["message_id"];
    $id = $update["message"]["from"]["id"];
    $username = $update["message"]["from"]["username"];
    $firstname = $update["message"]["from"]["first_name"];
    $chatname = $_ENV['CHAT']; 


    //Quando si digita /start esce questo messaggio in chat inviato dal bot
    if($message == "/start"){
        send_message($chat_id,$message_id, "Ciao 🤗 $firstname \nUsa /cmds per vedere i comandi disponibili");
    }

///Comando per vedere tutti i comandi
    ///if($message == "/cmds" || $message == "/CMDS")
    if($message == "/cmds" || $message == "/CMDS"){
        send_message($chat_id,$message_id, "
          /date (Data di Oggi)
          \n/time (Orario Attuale) 
          \n/dado (Dado)
          \n/info (Informazioni Utente)
          \n/isito (Sito Immobiliare)
          \n/oi (Offerte Immobiliare)
          \n/infon (Informazioni Importanti)
          \n
          Digitare /weather (Nome Città) 
          \n/weather (Meteo)
          ");
    }

///Data
    if($message == "/date" || $message == "/DATE"){
        $date = date("d/m/y");
        send_message($chat_id,$message_id, $date);
    }
    ///Comando Help
   if($message == "/help" || $message == "/HELP"){
        $help = "Digita /cmds per la lista dei comandi";
        send_message($chat_id,$message_id, $help);
    }
    ///Orario
   if($message == "/time" || $message == "/TIME"){
        $time = date("h:i a", time());
        send_message($chat_id,$message_id, "$time");
    }
    ///Immobiliare Sito
   if($message == "/isito" || $message == "/ISITO"){
    $immobiliare = "http://immobiliare.epizy.com/";
    send_message($chat_id,$message_id, "$immobiliare");
}
///Informazioni Utente
if($message == "/info"){
    send_message($chat_id,$message_id, "User Info \nName: $firstname\nID:$id \nUsername: @$username \nImmobile: #");
}

///Offerte
if($message == "/oi" || $message == "/OI"){
    $offerte = "👇Clicca il link per vedere le offerte Immobiliari👇 \nhttp://immobiliare.epizy.com/offerteimmobiliari/";
    send_message($chat_id,$message_id, "$offerte");
}

///Infon
if($message == "/infon" || $message == "/infon"){
    $infon = "👇Clicca il link per vedere le Informazioni👇 \nhttp://immobiliare.epizy.com/informazioni/";
    send_message($chat_id,$message_id, "$infon");
}


///Documentazione
if($message == "/documentazione" || $message == "/DOCUMENTAZIONE"){
    send_message($chat_id,$message_id, "
      date è la data
      \nhelp per sapere i comandi
      \ntime per sapere che ora è
      \nimmobiliare per andare al mio sito
      \nweatherToken per vedere il meteo preso da openweathermap.org
      ");
}

  
///Dado
if($message == "/dado" || $message == "/DADO"){
        sendDice($chat_id,$message_id, "🎲");
    }
    //Emoji dinamica per il dado
function sendDice($chat_id,$message_id, $message){
    $apiToken = $_ENV['API_TOKEN'];  
    file_get_contents("https://api.telegram.org/bot$apiToken/sendDice?chat_id=$chat_id&reply_to_message_id=$message_id&text=$message");
}

    //Previsioni del tempo per il giorno attuale
    //Non inerenti ma le ho volute implementare per provarle,
    //Non create da me
if(strpos($message, "/weather") === 0){
        $location = substr($message, 9);
        $weatherToken = "89ef8a05b6c964f4cab9e2f97f696c81"; ///Api del sito openweathermap.org

   $curl = curl_init();
   curl_setopt_array($curl, [
    CURLOPT_URL => "http://api.openweathermap.org/data/2.5/weather?q=$location&appid=$weatherToken",
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_FOLLOWLOCATION => true,
	CURLOPT_ENCODING => "",
	CURLOPT_MAXREDIRS => 10,
	CURLOPT_TIMEOUT => 50,
	CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST => "GET",
	CURLOPT_HTTPHEADER => [
		"Accept: */*",
        "Accept-Language: en-GB,en-US;q=0.9,en;q=0.8,hi;q=0.7",
        "Host: api.openweathermap.org",
        "sec-fetch-dest: empty",
		"sec-fetch-site: same-site"
  ],
]);


$content = curl_exec($curl);
curl_close($curl);
$resp = json_decode($content, true);

$weather = $resp['weather'][0]['main'];
$description = $resp['weather'][0]['description'];
$temp = $resp['main']['temp'];
$humidity = $resp['main']['humidity'];
$country = $resp['sys']['country'];
$name = $resp['name'];
$kelvin = 273;
$celcius = $temp - $kelvin;

if ($location = $name) {
        send_MDmessage($chat_id,$message_id, "***
Tempo Atmosferico a $location: $weather
Descrizione: $description
Temperatura : $celcius °C
Umidità: $humidity
Stato: $country ***");
}
else {
           send_message($chat_id,$message_id, "Nome Città non valido!");
}
    }


///Manda messaggi globali
    function send_message($chat_id,$message_id, $message){
        $text = urlencode($message);
        $apiToken = $_ENV['API_TOKEN'];  
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chat_id&reply_to_message_id=$message_id&text=$text");
    }
    
//Manda messaggi in grassetto
      function send_MDmessage($chat_id,$message_id, $message){
        $text = urlencode($message);
        $apiToken = $_ENV['API_TOKEN'];  
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$chat_id&reply_to_message_id=$message_id&text=$text&parse_mode=Markdown");
    }
///Manda messaggi nel canale
      function send_Cmessage($channel_id, $message){
        $text = urlencode($message);
        $apiToken = $_ENV['API_TOKEN'];
        file_get_contents("https://api.telegram.org/bot$apiToken/sendMessage?chat_id=$channel_id&text=$text");
    }
?>