<?php

namespace App\Controller\Contact;

use App\Contract\SendMessageInterface;
use App\Entity\Message;
use App\Form\MessageType;
use Doctrine\ORM\EntityManagerInterface;
use Karser\Recaptcha3Bundle\Validator\Constraints\Recaptcha3Validator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request, EntityManagerInterface $em,
                          SendMessageInterface $sendMessage,
                          Recaptcha3Validator $recaptcha3Validator): Response
    {
        $message = new Message();

        $form = $this->createForm(MessageType::class, $message);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $datas = $form->getData();
//            $datas->getText();
            $score = $recaptcha3Validator->getLastResponse()->getScore();
            $message = new Message();
            $message->setName($datas->getName());
//            $message->setPhone($datas->getPhone());
            $message->setEmail($datas->getEmail());
            $message->setText($datas->getText());
            $em->persist($message);
            $em->flush();
            $this->addFlash('success', 'Votre message a été envoyé.');

            $sendMessage($datas->getEmail(), null, $datas->getText(), $datas->getName()); //phone removed

            return $this->redirectToRoute('app_contact');
        }

        $response = $this->render('contact/index.html.twig', [
            'form' => $form->createView(),
        ]);

        // // cache publicly for 3600 seconds
        // $response->setPublic();
        // $response->setMaxAge(3600);
        // use this method to set several cache settings in one call
        // (this example lists all the available cache settings)
        $response->headers->set('Cache-Control', 'no-store');

        // (optional) set a custom Cache-Control directive
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }
}
