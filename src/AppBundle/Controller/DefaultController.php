<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use phpseclib\Crypt\DES;
use phpseclib\Crypt\RSA;

class DefaultController extends Controller
{
    /**
     * @Route("/asd", name="homepage")
     */
    public function indexAction(Request $request)
    {
        $request = $this->get('request');
        $defaultData = array('name' => 'Type your file name here');
        $form = $this->createFormBuilder($defaultData)
            ->add('name', 'text')
            ->add('file', 'file', array(
                'mapped' => false
            ))
            ->add('submit','submit')
            ->getForm();

        if ($request->getMethod() == 'POST') {
            $form->handleRequest($this->get('request'));

            if ($form->isValid()) {
                // perform some action, such as saving the task to the database
                $data = $form->getData();
                if ($form['file']->getData()){
                    $filename =  $form['file']->getData()->getClientOriginalName();
                    $uploadDir=dirname($this->container->getParameter('kernel.root_dir')) . '/web/bundles/framework/upload';
                    $form['file']->getData()->move($uploadDir, $filename);

                    $link = '/web/bundles/framework/upload'.'/'.$filename;
                }

            }
            $inputFile = $request->files->get('cache.xml');

            return $this->render('default/index.html.twig', array(
                'cipher' => "",
                'plain' => "",
                'rsacipher' => "",
                'rsaplain' => "",
                'rsapk' => "",
                'form' => $form->createView(),
                'link' => $link
            ));
        } else {

            $des = new DES();
            echo gettype($des);
            $des->setKey('This is my secret key');
            $plaintext = 'asda sda sdas dasd asdasdada sd';
            $cipher = $des->encrypt($plaintext);
            $plain = $des->decrypt($cipher);

            $rsa = new RSA();
            $rsa->createKey(1024);
            $rsaplain = "encrypt using RSA";
            $key = $rsa->createKey(1024);
            $rsa->loadKey($key['publickey']);
            $rsacipher = $rsa->encrypt($rsaplain);
            $rsa->loadKey($key['privatekey']);
            $rsadec = $rsa->decrypt($rsacipher);
            // replace this example code with whatever you need
            return $this->render('default/index.html.twig', array(
                'base_dir' => realpath($this->container->getParameter('kernel.root_dir') . '/..'),
                'cipher' => $cipher,
                'plain' => $plain,
                'rsacipher' => $key['publickey'],
                'rsaplain' => $key['privatekey'],
                'rsapk' => $rsa->getPublicKey(),
                'form' => $form->createView()
            ));
        }
    }
}
