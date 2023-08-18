<?php
declare(strict_types=1);

namespace App\Markdown\Parser\Inline;

use League\CommonMark\Node\Inline\{Newline, Text};
use League\CommonMark\Parser\Inline\{InlineParserInterface, InlineParserMatch};
use League\CommonMark\Parser\InlineParserContext;

/** Point.im breaks Markdown double line wrap rule */
class NewLineParser implements InlineParserInterface
{
    public function getMatchDefinition(): InlineParserMatch
    {
        return InlineParserMatch::regex('\\n');
    }

    public function parse(InlineParserContext $inlineContext): bool
    {
        $inlineContext->getCursor()->advanceBy(1);

        // Check previous inline for trailing spaces
        $spaces = 0;
        $lastInline = $inlineContext->getContainer()->lastChild();
        if ($lastInline instanceof Text) {
            $trimmed = \rtrim($lastInline->getLiteral(), ' ');
            $spaces = \strlen($lastInline->getLiteral()) - \strlen($trimmed);
            if ($spaces) {
                $lastInline->setLiteral($trimmed);
            }
        }

        if ($spaces >= 2) {
            $inlineContext->getContainer()->appendChild(new Newline(Newline::HARDBREAK));
        } else {
            $inlineContext->getContainer()->appendChild(new Newline(Newline::SOFTBREAK));
        }

        return true;
    }
}
