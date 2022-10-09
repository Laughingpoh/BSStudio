<?php
namespace App\Classe;


use Mailjet\Client;
use Mailjet\Resources;

class Mail {
    private $api_key = '35cd746f9471aa9c2b7dbe770891a8ba';
    private $api_key_secret = '5d2e06f6a3db505014481ac0cff47d17';

    public function send($to_email,$to_name,$subject,$content,$corps) {
        $mj = new Client($this->api_key, $this->api_key_secret,true,['version' => 'v3.1']);
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "belgiansoundstudio@hotmail.com",
                        'Name' => "Belgian Sound Studio"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_name
                        ]
                    ],
                    'TemplateID' => 4226133,
                    'TemplateLanguage' => true,
                    'Subject' => $subject,
                    'Variables' => [
                        'content' => $content,
                        'corps' => $corps
                    ]
                ]
            ]
        ];
        $response = $mj->post(Resources::$Email, ['body' => $body]);
        $response->success();
    }
}

?>