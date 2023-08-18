<?php
declare(strict_types=1);

namespace App\Markdown\Extension;

use App\Markdown\Parser as PointParser;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node as LeagueNode;
use League\CommonMark\Extension\CommonMark\Parser as LeagueParser;
use League\CommonMark\Extension\CommonMark\Renderer as LeagueRenderer;
use League\CommonMark\Node as LeagueCoreNode;
use League\CommonMark\Renderer as LeagueCoreRenderer;
use Nette\Schema\Expect;

class PointMarkdownExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('commonmark', Expect::structure([
            'use_asterisk' => Expect::bool(true),
            'use_underscore' => Expect::bool(true),
            'enable_strong' => Expect::bool(true),
            'enable_em' => Expect::bool(true),
            'unordered_list_markers' => Expect::listOf('string')
                ->min(1)
                ->default(['*', '+', '-'])
                ->mergeDefaults(false),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new PointParser\Block\BlockQuoteStartParser(),     70)
            ->addBlockStartParser(new LeagueParser\Block\HeadingStartParser(),        60)
            ->addBlockStartParser(new LeagueParser\Block\FencedCodeStartParser(),     50)
            ->addBlockStartParser(new LeagueParser\Block\HtmlBlockStartParser(),      40)
            ->addBlockStartParser(new LeagueParser\Block\ThematicBreakStartParser(),  20)
            ->addBlockStartParser(new LeagueParser\Block\ListBlockStartParser(),      10)
            ->addBlockStartParser(new LeagueParser\Block\IndentedCodeStartParser(), -100)

            ->addInlineParser(new PointParser\Inline\NewLineParser(), 200)
            ->addInlineParser(new LeagueParser\Inline\BacktickParser(),    150)
            ->addInlineParser(new LeagueParser\Inline\EscapableParser(),    80)
            ->addInlineParser(new LeagueParser\Inline\EntityParser(),       70)
            ->addInlineParser(new LeagueParser\Inline\AutolinkParser(),     50)
            ->addInlineParser(new PointParser\Inline\ImageLinkParser(),     60)
            ->addInlineParser(new LeagueParser\Inline\HtmlInlineParser(),   40)
            ->addInlineParser(new LeagueParser\Inline\CloseBracketParser(), 30)
            ->addInlineParser(new LeagueParser\Inline\OpenBracketParser(),  20)
            ->addInlineParser(new LeagueParser\Inline\BangParser(),         10)

            ->addRenderer(LeagueNode\Block\BlockQuote::class,    new LeagueRenderer\Block\BlockQuoteRenderer(),    0)
            ->addRenderer(LeagueCoreNode\Block\Document::class,  new LeagueCoreRenderer\Block\DocumentRenderer(),  0)
            ->addRenderer(LeagueNode\Block\FencedCode::class,    new LeagueRenderer\Block\FencedCodeRenderer(),    0)
            ->addRenderer(LeagueNode\Block\Heading::class,       new LeagueRenderer\Block\HeadingRenderer(),       0)
            ->addRenderer(LeagueNode\Block\HtmlBlock::class,     new LeagueRenderer\Block\HtmlBlockRenderer(),     0)
            ->addRenderer(LeagueNode\Block\IndentedCode::class,  new LeagueRenderer\Block\IndentedCodeRenderer(),  0)
            ->addRenderer(LeagueNode\Block\ListBlock::class,     new LeagueRenderer\Block\ListBlockRenderer(),     0)
            ->addRenderer(LeagueNode\Block\ListItem::class,      new LeagueRenderer\Block\ListItemRenderer(),      0)
            ->addRenderer(LeagueCoreNode\Block\Paragraph::class, new LeagueCoreRenderer\Block\ParagraphRenderer(), 0)
            ->addRenderer(LeagueNode\Block\ThematicBreak::class, new LeagueRenderer\Block\ThematicBreakRenderer(), 0)

            ->addRenderer(LeagueNode\Inline\Code::class,        new LeagueRenderer\Inline\CodeRenderer(),        0)
            ->addRenderer(LeagueNode\Inline\Emphasis::class,    new LeagueRenderer\Inline\EmphasisRenderer(),    0)
            ->addRenderer(LeagueNode\Inline\HtmlInline::class,  new LeagueRenderer\Inline\HtmlInlineRenderer(),  0)
            ->addRenderer(LeagueNode\Inline\Image::class,       new LeagueRenderer\Inline\ImageRenderer(),       0)
            ->addRenderer(LeagueNode\Inline\Link::class,        new LeagueRenderer\Inline\LinkRenderer(),        0)
            ->addRenderer(LeagueCoreNode\Inline\Newline::class, new LeagueCoreRenderer\Inline\NewlineRenderer(), 0)
            ->addRenderer(LeagueNode\Inline\Strong::class,      new LeagueRenderer\Inline\StrongRenderer(),      0)
            ->addRenderer(LeagueCoreNode\Inline\Text::class,    new LeagueCoreRenderer\Inline\TextRenderer(),    0)
        ;

        if ($environment->getConfiguration()->get('commonmark/use_asterisk')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'));
        }

        if ($environment->getConfiguration()->get('commonmark/use_underscore')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
        }
    }
}
