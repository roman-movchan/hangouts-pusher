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
    public function index(Request $request, \Twig_Environment $twig)
    {
        $content = $request->getContent();
        $webHookUrl = 'https://chat.googleapis.com/v1/spaces/AAAARTJcHxs/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=d7ay9oT-T1C_P-Z1iMrVBUaU5bH30MPQNLuuLMlVT78%3D';
        if (empty($content)) {
            return $this->json([]);
        }

        $params = json_decode($content, true);

        $text = $twig->render('git_message.html.twig', $params);

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
