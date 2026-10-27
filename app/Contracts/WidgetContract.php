<?php

namespace App\Contracts;

interface WidgetContract
{
    public function render(): string;

    public function getData(): array;
}
