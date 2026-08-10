<?php

namespace App\Filament\Resources\NewsArticleResource\Pages;

use App\Filament\Resources\NewsArticleResource;
use Filament\Resources\Pages\ListRecords;

class ListNewsArticles extends ListRecords
{
    protected static string $resource = NewsArticleResource::class;
}
