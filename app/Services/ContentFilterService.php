<?php

namespace App\Services;

class ContentFilterService
{
    public function filter(string $content): bool
    {

        $harmfulWords = ['hate', 'bully', 'harassment','stupid','dumb'];

        foreach ($harmfulWords as $word) {
            if (stripos($content, $word) !== false) {
                return true; // Content is harmful
            }
        }

        return false; 
    }
}
