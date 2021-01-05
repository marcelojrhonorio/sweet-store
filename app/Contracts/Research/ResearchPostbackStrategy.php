<?php

namespace App\Contracts\Research;

interface ResearchPostbackStrategy {

    public function runPixel();

    public function redirectUrl();

    public function researchValidator();

    public function findPixel();

}