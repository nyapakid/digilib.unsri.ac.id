<?php

namespace App\Support;

class ContentSanitizer
{
    public static function sanitizeRichHtml(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        $html = trim((string) $html);
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? '';
        $html = preg_replace('/<(script|style|object|embed|link|meta|form|input|button|textarea|select|svg|math|base)\b[^>]*>.*?<\/\1>/is', '', $html) ?? '';
        $html = preg_replace('/<(script|style|object|embed|link|meta|form|input|button|textarea|select|svg|math|base)\b[^>]*\/?>/is', '', $html) ?? '';

        $allowedTags = '<p><br><strong><b><em><i><u><s><ul><ol><li><blockquote><h1><h2><h3><h4><h5><h6><table><thead><tbody><tr><th><td><a>';
        $html = strip_tags($html, $allowedTags);

        $html = preg_replace_callback('/<([a-z0-9]+)\b([^>]*)>/i', function (array $matches): string {
            $tag = strtolower($matches[1]);
            $attributes = self::safeAttributes($tag, $matches[2]);

            return '<'.$tag.($attributes ? ' '.$attributes : '').'>';
        }, $html) ?? '';

        return blank($html) ? null : $html;
    }

    public static function sanitizeEmbedHtml(?string $html): ?string
    {
        if (blank($html)) {
            return null;
        }

        $html = trim((string) $html);
        $html = preg_replace('/<!--.*?-->/s', '', $html) ?? '';
        $html = preg_replace('/<(script|style|object|embed|link|meta|form|input|button|textarea|select|svg|math|base)\b[^>]*>.*?<\/\1>/is', '', $html) ?? '';
        $html = preg_replace('/<(script|style|object|embed|link|meta|form|input|button|textarea|select|svg|math|base)\b[^>]*\/?>/is', '', $html) ?? '';
        $html = strip_tags($html, '<iframe>');

        $sanitized = preg_replace_callback('/<iframe\b([^>]*)>(.*?)<\/iframe>/is', function (array $matches): string {
            preg_match_all('/([a-zA-Z0-9:-]+)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))/', $matches[1], $attributeMatches, PREG_SET_ORDER);

            $allowedAttributes = [
                'src',
                'title',
                'width',
                'height',
                'allow',
                'allowfullscreen',
                'loading',
                'referrerpolicy',
                'frameborder',
            ];
            $attributes = [];

            foreach ($attributeMatches as $attribute) {
                $name = strtolower($attribute[1]);
                $value = $attribute[3] ?? $attribute[4] ?? $attribute[5] ?? '';

                if (str_starts_with($name, 'on') || ! in_array($name, $allowedAttributes, true)) {
                    continue;
                }

                if ($name === 'src' && ! self::isSafeUrl($value, false)) {
                    continue;
                }

                $attributes[$name] = e($value);
            }

            if (empty($attributes['src'])) {
                return '';
            }

            $attributes['loading'] ??= 'lazy';
            $attributes['referrerpolicy'] ??= 'strict-origin-when-cross-origin';
            $attributeString = collect($attributes)
                ->map(fn (string $value, string $name) => $name.'="'.$value.'"')
                ->implode(' ');

            return '<iframe '.$attributeString.'></iframe>';
        }, $html) ?? '';

        return blank($sanitized) ? null : $sanitized;
    }

    public static function isSafeUrl(?string $url, bool $allowRelative = true): bool
    {
        $url = trim((string) $url);

        if ($url === '') {
            return false;
        }

        if (str_starts_with($url, '#')) {
            return true;
        }

        if ($allowRelative && str_starts_with($url, '/') && ! str_starts_with($url, '//')) {
            return true;
        }

        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto', 'tel'], true);
    }

    private static function safeAttributes(string $tag, string $rawAttributes): string
    {
        preg_match_all('/([a-zA-Z0-9:-]+)\s*=\s*("([^"]*)"|\'([^\']*)\'|([^\s"\'>]+))/', $rawAttributes, $matches, PREG_SET_ORDER);

        $attributes = [];

        foreach ($matches as $attribute) {
            $name = strtolower($attribute[1]);
            $value = $attribute[3] ?? $attribute[4] ?? $attribute[5] ?? '';

            if (str_starts_with($name, 'on') || $name === 'style') {
                continue;
            }

            if ($tag === 'a' && $name === 'href') {
                if (! self::isSafeUrl($value)) {
                    continue;
                }

                $attributes['href'] = e($value);
                continue;
            }

            if ($tag === 'a' && in_array($name, ['title', 'target', 'rel'], true)) {
                $attributes[$name] = e($value);
                continue;
            }

            if (in_array($tag, ['td', 'th'], true) && in_array($name, ['colspan', 'rowspan', 'scope'], true)) {
                $attributes[$name] = e($value);
            }
        }

        if ($tag === 'a' && ! empty($attributes['target']) && $attributes['target'] === '_blank') {
            $attributes['rel'] = 'noopener noreferrer';
        }

        return collect($attributes)
            ->map(fn (string $value, string $name) => $name.'="'.$value.'"')
            ->implode(' ');
    }
}
