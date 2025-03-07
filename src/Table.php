<?php

declare(strict_types=1);

/*
 * This is part of the webuni/commonmark-table-extension package.
 *
 * (c) Martin Hasoň <martin.hason@gmail.com>
 * (c) Webuni s.r.o. <info@webuni.cz>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Webuni\CommonMark\TableExtension;

use League\CommonMark\Block\Element\AbstractBlock;
use League\CommonMark\ContextInterface;
use League\CommonMark\Cursor;
use League\CommonMark\Node\Node;

class Table extends AbstractBlock
{
    private $caption;
    private $head;
    private $body;
    private $parser;

    public function __construct(\Closure $parser)
    {
        parent::__construct();
        $this->appendChild($this->head = new TableRows(TableRows::TYPE_HEAD));
        $this->appendChild($this->body = new TableRows(TableRows::TYPE_BODY));
        $this->parser = $parser;
    }

    public function canContain(AbstractBlock $block): bool
    {
        return $block instanceof TableRows || $block instanceof TableCaption;
    }

    public function acceptsLines(): bool
    {
        return true;
    }

    public function isCode(): bool
    {
        return false;
    }

    public function setCaption(TableCaption $caption = null): void
    {
        $node = $this->getCaption();
        if ($node instanceof TableCaption) {
            $node->detach();
        }

        $this->caption = $caption;
        if (null !== $caption) {
            $this->prependChild($caption);
        }
    }

    public function getCaption(): ?TableCaption
    {
        return $this->caption;
    }

    public function getHead(): TableRows
    {
        return $this->head;
    }

    public function getBody(): TableRows
    {
        return $this->body;
    }

    public function matchesNextLine(Cursor $cursor): bool
    {
        return call_user_func($this->parser, $cursor, $this);
    }

    public function handleRemainingContents(ContextInterface $context, Cursor $cursor): void
    {
    }

    /**
     * @return AbstractBlock[]
     */
    public function children(): array
    {
        return array_filter(parent::children(), function (Node $child): bool { return $child instanceof AbstractBlock; });
    }
}
