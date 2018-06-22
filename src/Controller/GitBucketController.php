<?php

namespace App\Controller;

use Psr\Log\LoggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class GitBucketController extends Controller
{
    /**
     * @Route("/webhook/{key}", name="webhook_gitbucket")
     * @Method("POST")
     *
     * @param Request $request
     * @param string $key
     * @return JsonResponse
     */
    public function index(Request $request, LoggerInterface $logger, string $key)
    {
        $webHookUrls = [
            '9982ad58bdcbd6732604d451053ca5cd21f09da8' => 'https://chat.googleapis.com/v1/spaces/AAAARTJcHxs/messages?key=AIzaSyDdI0hCZtE6vySjMm-WEfRq3CPzqKqqsHI&token=d7ay9oT-T1C_P-Z1iMrVBUaU5bH30MPQNLuuLMlVT78%3D'
        ];

        if(!array_key_exists($key, $webHookUrls)) {
           throw new HttpException(Response::HTTP_BAD_REQUEST, 'No key founded');
        }

        $url = $webHookUrls[$key];

        $content = $request->getContent();

        if (empty($content)) {
            return $this->json([]);
        }

        $logger->info($content);

        $params = json_decode($content, true);
        //dump($params);
        $text = $this->container->get('twig')->render('git_message.html.twig', $params);

        $body = [
            'text' =>
                $text
        ];

        $headers = [
            'Content-Type' => 'application/json'
        ];

        $request = $this->container->get('httplug.message_factory')->createRequest('POST', $url, $headers, json_encode($body));
        $response = $this->container->get('httplug.client')->sendRequest($request);

        return $this->json($response);
    }



}
