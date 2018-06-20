<?php

namespace App\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GitBucketController extends Controller
{
    /**
     * @Route("/webhook/gitbucket", name="webhook_gitbucket")
     * @Method("POST")
     */
    public function index(Request $request)
    {
        $content = $request->getContent();
        $webHookUrl = 'https://chat.googleapis.com/v1/spaces/AAAARTJcHxs/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=d7ay9oT-T1C_P-Z1iMrVBUaU5bH30MPQNLuuLMlVT78%3D';
        if (empty($content)) {
            return $this->json([]);
        }

        $params = json_decode($content, true);
        $commitsCount = sizeof($params['commits']);

        $text = <<<TEXT
There is new *$commitsCount* commit(s) in repo *{$params["repository"]["name"]}* \n\n
TEXT;

        foreach ($params['commits'] as $commit) {
            $date = new \DateTime($commit['timestamp']);
            $date = $date->format('d.m.Y H:i');
            $url = $commit['url'];
            $files = implode(', ', $commit['added']);
            $message = trim(preg_replace('/\s\s+/', ' ', $commit['message']));
            $text .= <<<TEXT
_{$date}_ <{$url}|{$message}> 
Files: {$files}        
TEXT;
        }

        $body = [
            'text' =>
                $text
        ];

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $request = $this->container->get('httplug.message_factory')->createRequest('POST', $webHookUrl, $headers, json_encode($body));
        $response = $this->container->get('httplug.client')->sendRequest($request);

        return $this->json($response);
    }



}
