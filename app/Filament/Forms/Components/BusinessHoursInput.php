<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Field;

class BusinessHoursInput extends Field
{
    protected string $view = 'filament.forms.components.business-hours-input';

    public function getDefaultState(): mixed
    {
        return [
            'monday'    => ['open' => '', 'close' => '', 'closed' => false],
            'tuesday'   => ['open' => '', 'close' => '', 'closed' => false],
            'wednesday' => ['open' => '', 'close' => '', 'closed' => false],
            'thursday'  => ['open' => '', 'close' => '', 'closed' => false],
            'friday'    => ['open' => '', 'close' => '', 'closed' => false],
            'saturday'  => ['open' => '', 'close' => '', 'closed' => false],
            'sunday'    => ['open' => '', 'close' => '', 'closed' => false],
        ];
    }
}
