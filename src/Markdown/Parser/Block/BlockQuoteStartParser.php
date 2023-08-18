<?php
declare(strict_types=1);

namespace App\Markdown\Parser\Block;

use League\CommonMark\Extension\CommonMark\Parser\Block\BlockQuoteParser;
use League\CommonMark\Parser\Block\{BlockStart, BlockStartParserInterface};
use League\CommonMark\Parser\{Cursor, MarkdownParserStateInterface};

/** Point.im breaks Markdown rule about quotation on next line */
class BlockQuoteStartParser implements BlockStartParserInterface
{
    public function tryStart(Cursor $cursor, MarkdownParserStateInterface $parserState): ?BlockStart
    {
        if ($cursor->isIndented()) {
            return BlockStart::none();
        }

        if ($cursor->getNextNonSpaceCharacter() !== '>') {
            return BlockStart::none();
        }

        $cursor->advanceToNextNonSpaceOrTab();
        $cursor->advanceBy(1);
        $cursor->advanceBySpaceOrTab();

        return BlockStart::of(new BlockQuoteParser())->at($cursor);
    }
}
