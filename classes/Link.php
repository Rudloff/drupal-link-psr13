<?php

namespace DrupalLinkPsr;

use Drupal\Component\Utility\UrlHelper;
use Drupal\Core\Url;
use Psr\Link\EvolvableLinkInterface;
use Stringable;

class Link implements EvolvableLinkInterface
{

    /**
     * @param Url $url
     */
    function __construct(private Url $url)
    {

    }

    /**
     * @return string
     */
    public function getHref(): string
    {
        return $this->url->toString();
    }

    /**
     * @return bool
     */
    public function isTemplated(): bool
    {
        return FALSE;
    }

    /**
     * @return array
     */
    public function getRels(): array
    {
        $attributes = $this->getAttributes();
        if (isset($attributes['rel'])) {
            if (is_array($attributes['rel'])) {
                return $attributes['rel'];
            } else {
                return [$attributes['rel']];
            }
        }

        return [];
    }

    /**
     * @return array
     */
    public function getAttributes(): array
    {
        if ($attributes = $this->url->getOption('attributes')) {
            return $attributes;
        }

        return [];
    }

    /**
     * @param Stringable|string $href
     * @return $this
     */
    public function withHref(Stringable|string $href): static
    {
        $that = clone $this;
        $options = $that->url->getOptions();

        if (UrlHelper::isValid($href, true)) {
            $that->url = Url::fromUri($href);
        } else {
            $that->url = Url::fromUserInput($href);
        }

        $that->url->setOptions($options);

        return $that;
    }

    /**
     * @param string $rel
     * @return $this
     */
    public function withRel(string $rel): static
    {
        $rels = $this->getRels();
        $rels[] = $rel;

        return $this->withAttribute('rel', $rels);
    }

    /**
     * @param string $rel
     * @return $this
     */
    public function withoutRel(string $rel): static
    {
        $rels = $this->getRels();
        foreach ($rels as $i => $curRel) {
            if ($curRel == $rel) {
                unset($rels[$i]);
            }
        }

        return $this->withAttribute('rel', $rels);
    }

    /**
     * @param string $attribute
     * @param float|array|bool|Stringable|int|string $value
     * @return $this
     */
    public function withAttribute(string $attribute, float|array|bool|Stringable|int|string $value): static
    {
        $that = clone $this;

        $attributes = $that->getAttributes();
        $attributes[$attribute] = $value;
        $that->url->setOption('attributes', $attributes);

        return $that;
    }

    /**
     * @param string $attribute
     * @return $this
     */
    public function withoutAttribute(string $attribute): static
    {
        $that = clone $this;

        $attributes = $that->getAttributes();
        unset($attributes[$attribute]);
        $that->url->setOption('attributes', $attributes);

        return $that;
    }

    /**
     * @return void
     */
    function __clone()
    {
        $this->url = clone $this->url;
    }
}
