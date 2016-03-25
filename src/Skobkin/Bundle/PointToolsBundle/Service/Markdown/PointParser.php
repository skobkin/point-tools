<?php

namespace Skobkin\Bundle\PointToolsBundle\Service\Markdown;

use Knp\Bundle\MarkdownBundle\Parser\MarkdownParser;
use Symfony\Component\Routing\RouterInterface;

class PointParser extends MarkdownParser
{
    /**
     * @var RouterInterface
     */
    protected $router;

    /**
     * {@inheritdoc}
     */
    protected $features = array(
        'header' => true,
        'list' => true,
        'horizontal_rule' => true,
        'table' => false,
        'foot_note' => true,
        'fenced_code_block' => true,
        'abbreviation' => true,
        'definition_list' => true,
        'inline_link' => true, // [link text](url "optional title")
        'reference_link' => true, // [link text] [id]
        'shortcut_link' => true, // [link text]
        'images' => true,
        'block_quote' => true,
        'code_block' => true,
        'html_block' => false,
        'auto_link' => true,
        'auto_mailto' => true,
        'entities' => true,
        'no_html' => true,
        'point_usernames' => true, // @user
        'point_posts' => true, // #post
        'point_tags' => false, // @todo implement
        'point_inline_images' => true, // https://i.skobk.in/i/facepalm.jpg
    );

    public function __construct(array $features, RouterInterface $router)
    {
        $this->router = $router;

        parent::__construct($features);
    }

    /**
     * Point.im breaks Markdown rule about quotation on next line
     *
     * @param $text
     *
     * @return mixed
     */
    protected function doBlockQuotes($text) {
        $text = preg_replace_callback('/
              (               # Wrap whole match in $1
                (?>
                  ^[ ]*>[ ]?  # ">" at the start of a line
                    .+\n      # rest of the first line
                  \n*         # blanks
                )+
              )
            /xm',
            array($this, '_doBlockQuotes_callback'), $text
        );

        return $text;
    }

    /**
     * Point.im breaks Markdown double line wrap rule
     *
     * @param $text
     *
     * @return mixed
     */
    protected function formParagraphs($text) {
        #
        #	Params:
        #		$text - string to process with html <p> tags
        #
        # Strip leading and trailing lines:
        $text = preg_replace('/\A\n+|\n+\z/', '', $text);

        $grafs = preg_split('/\n+/', $text, -1, PREG_SPLIT_NO_EMPTY);

        #
        # Wrap <p> tags and unhashify HTML blocks
        #
        foreach ($grafs as $key => $value) {
            if (!preg_match('/^B\x1A[0-9]+B$/', $value)) {
                # Is a paragraph.
                $value = $this->runSpanGamut($value);
                $value = preg_replace('/^([ ]*)/', "<p>", $value);
                $value .= "</p>";
                $grafs[$key] = $this->unhash($value);
            }
            else {
                # Is a block.
                # Modify elements of @grafs in-place...
                $graf = $value;
                $block = $this->html_hashes[$graf];
                $graf = $block;
                $grafs[$key] = $graf;
            }
        }

        return implode("\n\n", $grafs);
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function doAutoLinks($text)
    {
        if ($this->features['point_inline_images']) {
            return preg_replace_callback('{((https?):\/\/[^\'">\s]+\.(jpg|jpeg|png|gif))}i', array(&$this, 'doAutoLinksInlineImageCallback'), $text);
        }

        return parent::doAutoLinks($text);
    }

    public function doAnchors($text)
    {
        $text = parent::doAnchors($text);

        #
        # Turn Markdown link shortcuts into XHTML <a> tags.
        #
        if ($this->in_anchor) {
            return $text;
        }
        $this->in_anchor = true;

        #
        # Handling username links: @some-point-username
        #
        if ($this->features['point_usernames']) {
            $text = preg_replace_callback('{
                (               # wrap whole match in $1
                  @
                    (
                        [a-zA-Z0-9\-]+? # username = $2
                    )
                )
                ([^a-zA-Z0-9\-]|$) # Tail of username
                }xs',
                array(&$this, 'doAnchorsPointUsernameCallback'), $text);
        }

        #
        # Handling post links: #somepost
        #
        if ($this->features['point_posts']) {
            $text = preg_replace_callback('{
                (               # wrap whole match in $1
                  \#
                    (
                        [a-zA-Z]+? # post id = $2
                    )
                )
                ([^a-zA-Z]|$) # Tail of post
                }xs',
                array(&$this, 'doAnchorsPointPostCallback'), $text);
        }

        $this->in_anchor = false;

        return $text;
    }

    protected function doAutoLinksInlineImageCallback($matches)
    {
        $url = trim($matches[1]);
        $ext = $matches[2];

        return $this->hashPart('<a href="'.$url.'" class="post-image '
            .('gif' === $ext ? ' img-gif':'').'"><img src="'.$url.'" class="img-thumbnail" alt="Inline image"></a>');
    }

    protected function doAnchorsPointUsernameCallback($matches)
    {
        //$wholeMatch = $matches[1];
        $username = $matches[2];
        $href = $this->router->generate('user_show', ['login' => $username]);
        $tail = htmlspecialchars($matches[3]);
        
        return $this->hashPart('<a href="'.$href.'" class="user">@'.$username.'</a>'.$tail);
    }

    protected function doAnchorsPointPostCallback($matches)
    {
        //$wholeMatch = $matches[1];
        $postId = $matches[2];
        $href = $this->router->generate('post_show', ['id' => $postId]);
        $tail = htmlspecialchars($matches[3]);

        return $this->hashPart('<a href="'.$href.'" class="post">#'.$postId.'</a>'.$tail);
    }
}