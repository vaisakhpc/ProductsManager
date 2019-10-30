<?php

namespace App\Model;

interface ErrorInterface
{
    public function getCode() : string;

    public function getMessage() : string;

    public function jsonSerialize() : array;
}
