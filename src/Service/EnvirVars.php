<?php
namespace App\Service;


class EnvirVars
{
    protected $varString;
    protected $varInt;


    public function __construct(string $varString, int $varInt)
    {
        $this->varString    = $varString;
        $this->varInt       = $varInt;
    }


    public function getVarString(): string
    {
        return $this->varString;
    }


    public function setVarString(string $varString): self
    {
        $this->varString = $varString;
        return $this;
    }


    public function getVarInt(): int
    {
        return $this->varInt;
    }


    public function setVarInt(int $varInt): self
    {
        $this->varInt = $varInt;
        return $this;
    }
}