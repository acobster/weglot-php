<?php

namespace Weglot\Parser\Formatter;

/**
 * Class IgnoredNodes
 * @package Weglot\Parser\Formatter
 */
class IgnoredNodes
{
    /**
     * @var string
     */
    protected $source;

    /**
     * Nodes to ignore in DOM
     * @var array
     */
    protected $ignoredNodes = [
        'strong', 'b',
        'em', 'i',
        'small', 'big',
        'sub', 'sup',
        'abbr',
        'acronym',
        'bdo',
        'cite',
        'kbd',
        'q',
    ];

    protected $usualTags = [
        'span',
        'blockquote',
        'aside',
        'h1', 'h2', 'h3', 'h4', 'h5', 'h6',
        'section', 'article', 'nav',
        'div',
        'dd', 'dl', 'dt',
        'li', 'ul', 'ol',
        'p', 'pre',
    ];

    /**
     * IgnoredNodes constructor.
     * @param string $source
     */
    public function __construct($source)
    {
        $this
            ->setSource($source)
            ->handle();
    }

    /**
     * @param string $source
     * @return $this
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * @return string
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Convert < & > for some dom tags to let them able
     * to go through API calls.
     */
    public function handle()
    {
        // used to make clean single regex
        array_walk($this->ignoredNodes, function (&$value, $key) {
            $value = '(' .$value. ')';
        });
        array_walk($this->usualTags, function (&$value, $key) {
            $value = '(' .$value. ')';
        });

        // time for the BIG regex ...
        $pattern = '#\<(?<tag>' .implode('|', $this->ignoredNodes). ')(?<more>\s.*?)?\>(?<content>.*?)\<\/' .implode('|', $this->ignoredNodes). '\>#i';
        $matches = [];

        if ($matchesCount = preg_match_all($pattern, $this->getSource(), $matches)) {
            for ($i = 0; $i < $matchesCount; ++$i) {
                if ($matches['content'][$i] === '') {
                    continue;
                }

                $count = 0;
                $patterns = ['#\<' .implode('|', $this->usualTags). '(?<after>.*?)\>#', '#\</' .implode('|', $this->usualTags). '\>#'];
                foreach ($patterns as $current) {
                    $count += preg_match($current, $matches['content'][$i]);
                }

                if ($count === 0) {
                    $this->setSource(
                        str_replace(
                            $matches[0],
                            '&lt;' .$matches['tag'][$i].$matches['more'][$i]. '&gt;' .$matches['content'][$i]. '&lt;/' .$matches['tag'][$i]. '&gt;',
                            $this->getSource()
                        )
                    );
                }
            }
        }
    }
}
