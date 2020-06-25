<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use Twilio\Exceptions\ConfigurationException;
use Twilio\Exceptions\TwilioException;
use Twilio\Rest\Client;

class TwilioController extends AbstractController
{
    /**
     * @Route("/twilio", name="twilio")
     * @throws ConfigurationException
     * @throws TwilioException
     */
    public function index()
    {
        $sid = 'AC650d86e24b4d0dcd5e9b4cad1c26a770';
        $token = '97311b09fe3ea2b8c5acf028e99ca07a';

        $twilio = new Client($sid, $token);

        $call = $twilio->calls
            ->create("+528111774732", // to
                "+18312469114", // from
                array(
                    "twiml" => "<Response>  <Say language='es-mx'>Hola Arturo, Tu cita con clinimed es hoy a las 4:30 PM</Say></Response>"
                )
            );

        return $this->render('twilio/index.html.twig', [
            'controller_name' => 'TwilioController',
        ]);
    }
}
