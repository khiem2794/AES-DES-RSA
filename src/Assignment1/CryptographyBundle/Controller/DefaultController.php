<?php

namespace Assignment1\CryptographyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Assignment1\CryptographyBundle\Crypto\DES;
use Assignment1\CryptographyBundle\Crypto\AES;
use Assignment1\CryptographyBundle\Crypto\RSA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends Controller
{
    /**
     * @Route("/",name="crypto")
     */
    public function indexAction() {
        return $this->render("@Assignment1Cryptography/Crypto/index.html.twig");
    }


    /**
     * @Route("/des/encrypt",name="DES_ENCRYPT")
     */
    function desEnAction(){
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('DES_ENCRYPT'))
            ->add('DesEnKey', 'textarea')
            ->add('DesEnFile', 'file', array(
                'mapped' => false
            ))
            ->add('submit', 'submit')
            ->getForm();

        $request = $this->get('request');
        $form->handleRequest($request);

        if($request->isXmlHttpRequest()){
            $file = $form['DesEnFile']->getData();
            $key = $form['DesEnKey']->getData();
            $des = new DES($key, $file);
            $encrypted_file = $des->encrypt();
            return new JsonResponse(array('hash' => $encrypted_file->getHash(), 'path' => $encrypted_file->getPath()));
        }

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $file = $form['DesEnFile']->getData();
            $key = $form['DesEnKey']->getData();
            $des = new DES($key, $file);
            $encrypted_file = $des->encrypt();

            return $this->render('@Assignment1Cryptography/Crypto/DES/encrypt.html.twig', array(
                'form' => $form->createView(),
                'file_hash' => $encrypted_file->getHash(),
                'encrypted_file' => $encrypted_file->getPath()
            ));
        }

        return $this->render("@Assignment1Cryptography/Crypto/DES/encrypt.html.twig", array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/des/decrypt",name="DES_DECRYPT")
     */
    function desDeAction(){
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('DES_DECRYPT'))
            ->add('DesDeKey', 'textarea')
            ->add('DesDeFile', 'file', array(
                'mapped' => false
            ))
            ->add('submit', 'submit')
            ->getForm();

        $request = $this->get('request');
        $form->handleRequest($request);

        if($request->isXmlHttpRequest()){
            $file = $form['DesDeFile']->getData();
            $key = $form['DesDeKey']->getData();
            $des = new DES($key, $file);
            $decrypted_file = $des->decrypt();
            return new JsonResponse(array('hash' => $decrypted_file->getHash(), 'path' => $decrypted_file->getPath()));
        }

        if ($request->getMethod() == 'POST' && $form->isValid()) {
            $file = $form['DesDeFile']->getData();
            $key = $form['DesDeKey']->getData();
            //$file->move('/web/bundles/assignment1cryptography/upload/Files', $file->getClientOriginalName());
            $des = new DES($key, $file);
            $decrypted_file = $des->decrypt();
            return $this->render('@Assignment1Cryptography/Crypto/DES/decrypt.html.twig', array(
                'form' => $form->createView(),
                'file_hash' => $decrypted_file->getHash(),
                'decrypted_file' => $decrypted_file->getPath()
            ));
        }

        return $this->render("@Assignment1Cryptography/Crypto/DES/decrypt.html.twig", array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/rsa/create",name="RSA_CREATE_KEY")
     */
    function rsaCreateKeyAction(){
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('RSA_CREATE_KEY'))
            ->add('Length', 'number')
            ->add('submit', 'submit')
            ->getForm();

        $request = $this->get('request');
        $form->handleRequest($request);
        if ($request->isXmlHttpRequest()){
            $length = intval($form['Length']->getData());
            $rsa = new \phpseclib\Crypt\RSA();
            $key = $rsa->createKey($length);
            return new JsonResponse(array('public_key' => $key['publickey'], 'private_key' => $key['privatekey']));
        }
        if ($request->getMethod() == 'POST') {
            $length = intval($form['Length']->getData());

            $rsa = new \phpseclib\Crypt\RSA();
            $key = $rsa->createKey($length);

            return $this->render("@Assignment1Cryptography/Crypto/RSA/createkey.html.twig", array(
                'form' => $form->createView(),
                'key' => $key
            ));
        }
        return $this->render("@Assignment1Cryptography/Crypto/RSA/createkey.html.twig", array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/rsa/encrypt",name="RSA_ENCRYPT")
     */
    function rsaEnAction(){

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('RSA_ENCRYPT'))
            ->add('Public_key', 'textarea', array(
                'attr' => array(
                    'cols' => 75,
                    'rows' => 15
                )
            ))
            ->add('File', 'file')
            ->add('submit', 'submit')
            ->getForm();
        $request = $this->get('request');
        $form->handleRequest($request);

        if($request->isXmlHttpRequest()){
            $file = $form['File']->getData();
            $publickey = $form['Public_key']->getData();
            $rsa = new RSA($publickey, $file);
            $encrypted_file = $rsa->encrypt();
            return new JsonResponse(array('hash' => $encrypted_file->getHash(), 'path' => $encrypted_file->getPath()));
        }

        if ( $request->getMethod() == 'POST'){
            $file = $form['File']->getData();
            $publickey = $form['Public_key']->getData();
            $rsa = new RSA($publickey, $file);
            $encrypted_file = $rsa->encrypt();
            return $this->render("@Assignment1Cryptography/Crypto/RSA/encrypt.html.twig", array(
                'form' => $form->createView(),
                'file_hash' => $encrypted_file->getHash(),
                'encrypted_file' => $encrypted_file->getPath()
            ));
        }

        return $this->render("@Assignment1Cryptography/Crypto/RSA/encrypt.html.twig", array(
            'form' => $form->createView()
        ));
    }


    /**
     * @Route("/rsa/decrypt",name="RSA_DECRYPT")
     */
    function rsaDeAction(){

        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('RSA_DECRYPT'))
            ->add('Private_key', 'textarea', array(
                'attr' => array(
                    'cols' => 75,
                    'rows' => 15
                )
            ))
            ->add('File', 'file')
            ->add('submit', 'submit')
            ->getForm();
        $request = $this->get('request');
        $form->handleRequest($request);

        if($request->isXmlHttpRequest()){
            $file = $form['File']->getData();
            $privatekey = $form['Private_key']->getData();
            $rsa = new RSA($privatekey, $file);
            $decrypted_file = $rsa->decrypt();
            return new JsonResponse(array('hash' => $decrypted_file->getHash(), 'path' => $decrypted_file->getPath()));
        }

        if ( $request->getMethod() == 'POST'){
            $file = $form['File']->getData();
            $privatekey = $form['Private_key']->getData();
            $rsa = new RSA($privatekey, $file);
            $decrypted_file = $rsa->decrypt();
            return $this->render("@Assignment1Cryptography/Crypto/RSA/decrypt.html.twig", array(
                'form' => $form->createView(),
                'file_hash' => $decrypted_file->getHash(),
                'decrypted_file' => $decrypted_file->getPath()
            ));
        }
        return $this->render("@Assignment1Cryptography/Crypto/RSA/decrypt.html.twig", array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/aes/encrypt",name="AES_ENCRYPT")
     */
    function aesEnAction(){
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('AES_ENCRYPT'))
            ->add('AesEnKey', 'textarea')
            ->add('AesEnFile', 'file')
            ->add('submit', 'submit')
            ->getForm();
        $request = $this->get('request');
        $form->handleRequest($request);
        if($request->isXmlHttpRequest()){
            $key = $form['AesEnKey']->getData();
            $file = $form['AesEnFile']->getData();
            $aes = new AES($key, $file);
            $encrypted_file = $aes->encrypt();
            return new JsonResponse(array('hash' => $encrypted_file->getHash(), 'path' => $encrypted_file->getPath()));
        }
        if ( $request->getMethod() == 'POST') {
            $key = $form['AesEnKey']->getData();
            $file = $form['AesEnFile']->getData();

            $aes = new AES($key, $file);
            $encrypted_file = $aes->encrypt();

            return $this->render("@Assignment1Cryptography/Crypto/AES/encrypt.html.twig", array(
                'form' => $form->createView(),
                'hash' => $encrypted_file->getHash(),
                'encrypted_file' => $encrypted_file->getPath()
            ));
        }

        return $this->render("@Assignment1Cryptography/Crypto/AES/encrypt.html.twig", array(
            'form' => $form->createView()
        ));
    }

    /**
     * @Route("/aes/decrypt",name="AES_DECRYPT")
     */
    function aesDeAction(){
        $form = $this->createFormBuilder()
            ->setMethod('POST')
            ->setAction($this->generateUrl('AES_DECRYPT'))
            ->add('AesDeKey', 'textarea')
            ->add('AesDeFile', 'file')
            ->add('submit', 'submit')
            ->getForm();
        $request = $this->get('request');
        $form->handleRequest($request);
        if($request->isXmlHttpRequest()){
            $key = $form['AesDeKey']->getData();
            $file = $form['AesDeFile']->getData();
            $aes = new AES($key, $file);
            $decrypted_file = $aes->decrypt();
            return new JsonResponse(array('hash' => $decrypted_file->getHash(), 'path' => $decrypted_file->getPath()));
        }
        if ( $request->getMethod() == 'POST') {
            $key = $form['AesDeKey']->getData();
            $file = $form['AesDeFile']->getData();
            $aes = new AES($key, $file);
            $decrypted_file = $aes->decrypt();

            return $this->render("@Assignment1Cryptography/Crypto/AES/decrypt.html.twig", array(
                'form' => $form->createView(),
                'hash' => $decrypted_file->getHash(),
                'decrypted_file' => $decrypted_file->getPath()
            ));
        }

        return $this->render("@Assignment1Cryptography/Crypto/AES/decrypt.html.twig", array(
            'form' => $form->createView()
        ));
    }

}




