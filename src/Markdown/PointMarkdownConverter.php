<?php
declare(strict_types=1);

namespace App\Markdown;

use App\Markdown\Extension\PointMarkdownExtension;
use League\CommonMark\Environment\Environment;
use League\CommonMark\MarkdownConverter;

class PointMarkdownConverter extends MarkdownConverter
{
    public function __construct(
    ) {
        $env = new Environment([
            'html_input' => 'strip',
        ]);
        $env->addExtension(new PointMarkdownExtension());

        parent::__construct($env);
    }
}
