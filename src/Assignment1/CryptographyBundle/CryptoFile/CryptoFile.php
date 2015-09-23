<?php

/**
 * Created by PhpStorm.
 * User: root
 * Date: 9/22/15
 * Time: 10:50 AM
 */

namespace Assignment1\CryptographyBundle\CryptoFile;

class CryptoFile
{
    private $hash;
    private $path;

    /**
     * @return mixed
     */
    public function getHash()
    {
        return $this->hash;
    }

    /**
     * @param mixed $hash
     */
    public function setHash($hash)
    {
        $this->hash = $hash;
    }

    /**
     * @return mixed
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param mixed $path
     */
    public function setPath($path)
    {
        $this->path = $path;
    }

    /**
     * CryptoFile constructor.
     * @param $hash
     * @param $path
     */
    public function __construct($hash, $path)
    {
        $this->hash = $hash;
        $this->path = $path;
    }

}