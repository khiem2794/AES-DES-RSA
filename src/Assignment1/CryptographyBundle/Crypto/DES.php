<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/22/15
 * Time: 10:02 AM
 */

namespace Assignment1\CryptographyBundle\Crypto;

use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Assignment1\CryptographyBundle\CryptoFile\CryptoFile;

class DES
{
    private $key;
    private $file;
    private $file_name;
    private $save_name;

    /**
     * DES constructor.
     * @param $key
     * @param $file
     */
    public function __construct($key,UploadedFile $file)
    {
        $this->key = $key;
        $this->file_name = $file->getClientOriginalName();
        $this->file = $file->move($this->getFileUploadDir(), $this->file_name);
    }

    /**
     * @return mixed
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param mixed $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return File
     */
    public function getFile()
    {
        return $this->file;
    }

    /**
     * @param File $file
     */
    public function setFile($file)
    {
        $this->file = $file;
    }


    /**
     * @return mixed
     */
    public function getSaveName()
    {
        return $this->save_name;
    }

    /**
     * @param mixed $save_name
     */
    public function setSaveName($save_name)
    {
        $this->save_name = $save_name;
    }


    public function encrypt() {
        $des = new \phpseclib\Crypt\DES();
        $des->setKey($this->key);

        $plaintext = file_get_contents($this->getFileUploadDir().'/'.$this->file_name);
        $ciphertext = $des->encrypt($plaintext);

        $hash = md5($plaintext);
        $this->save_name = "EncryptedFile_".$hash;
        file_put_contents($this->getFileRootDir().'/'.$this->save_name,$ciphertext);
        unlink($this->file->getPathname());

        return new CryptoFile($hash, $this->getWebPath().'/'.$this->save_name);
    }

    public function decrypt() {
        $des = new \phpseclib\Crypt\DES();
        $des->setKey($this->key);

        $ciphertext = file_get_contents($this->getFileUploadDir().'/'.$this->file_name);
        $plaintext = $des->decrypt($ciphertext);

        $hash = md5($plaintext);
        $this->save_name = "DecryptedFile_".$hash;
        file_put_contents($this->getFileRootDir().'/'.$this->save_name,$plaintext);
        unlink($this->file->getPathname());

        return new CryptoFile($hash, $this->getWebPath().'/'.$this->save_name);
    }
    // return web path for downloading file
    public function getWebPath(){
        return '/bundles/assignment1cryptography/upload/DES'.'/'.$this->getSaveName();
    }
    // return relative file location
    public function getFileDir(){
        return '/web/bundles/assignment1cryptography/upload/DES';
    }
    // return absolute location
    public function getFileRootDir(){
        return '/root/PhpstormProjects/AssignmnetCrypto'. $this->getFileDir();
    }
    public function getFileUploadDir() {
        return '/web/bundles/assignment1cryptography/upload/Files';
    }
}