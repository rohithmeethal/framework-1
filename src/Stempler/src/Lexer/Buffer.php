<?php

/**
 * Spiral Framework.
 *
 * @license   MIT
 * @author    Anton Titov (Wolfy-J)
 */

declare(strict_types=1);

namespace Spiral\Stempler\Lexer;

/**
 * Creates local buffers over byte/token stream. Able to replay some tokens.
 */
final class Buffer implements \IteratorAggregate
{
    /** @var \Generator @internal */
    private $generator;

    /** @var Byte[]|Token[] */
    private $buffer = [];

    /** @var Byte[]|Token[] */
    private $replay = [];

    /** @var int */
    private $offset = 0;

    public function __construct(\Generator $generator, int $offset = 0)
    {
        $this->generator = $generator;
        $this->offset = $offset;
    }

    /**
     * Delegate generation to the nested generator and collect
     * generated token/char stream.
     *
     * @return \Generator
     */
    public function getIterator(): \Traversable
    {
        while ($n = $this->next()) {
            yield $n;
        }
    }

    public function getOffset(): int
    {
        return $this->offset;
    }

    /**
     * @return Byte|Token
     */
    public function next()
    {
        if ($this->replay !== []) {
            $n = array_shift($this->replay);
        } else {
            $n = $this->generator->current();
            if ($n === null) {
                return null;
            }
            $this->generator->next();
            $this->buffer[] = $n;
        }

        if ($n !== null) {
            $this->offset = $n->offset;
        }

        return $n;
    }

    /**
     * Get all the string content until first token.
     */
    public function nextBytes(): string
    {
        $result = '';
        while ($n = $this->next()) {
            if ($n instanceof Byte) {
                $result .= $n->char;
            } else {
                break;
            }
        }

        return $result;
    }

    /**
     * Get next generator value without advancing the position.
     */
    public function lookahead()
    {
        if ($this->replay !== []) {
            return $this->replay[0];
        }

        $n = $this->next();
        if ($n !== null) {
            array_unshift($this->replay, $n);
        }

        return $n;
    }

    /**
     * Get next byte(s) value if any.
     *
     * @param int $size Size of lookup string.
     */
    public function lookaheadByte(int $size = 1): ?string
    {
        $result = '';
        $replay = [];
        for ($i = 0; $i < $size; $i++) {
            $n = $this->next();
            if ($n !== null) {
                $replay[] = $n;
            }

            if (!$n instanceof Byte) {
                break;
            }

            $result .= $n->char;
        }

        foreach (array_reverse($replay) as $n) {
            array_unshift($this->replay, $n);
        }

        return $result;
    }

    /**
     * Replay all the byte and token stream after given offset.
     */
    public function replay(int $offset): void
    {
        foreach ($this->buffer as $n) {
            if ($n->offset > $offset) {
                $this->replay[] = $n;
            }
        }
    }
}
