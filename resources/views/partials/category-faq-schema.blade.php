@php
    $bbFaqEntities = [];

    if (
        isset($category)
        && $category instanceof \App\Models\Category
        && !empty($category->content)
    ) {

        $bbContent = $category->content;

        $bbStart = strpos(
            $bbContent,
            '<!-- NM FAQ START -->'
        );

        $bbEnd = strpos(
            $bbContent,
            '<!-- NM FAQ END -->'
        );

        if (
            $bbStart !== false
            && $bbEnd !== false
            && $bbEnd > $bbStart
        ) {

            $bbFaqHtml = substr(
                $bbContent,
                $bbStart,
                $bbEnd - $bbStart
            );

            preg_match_all(
                '/<h3\b[^>]*>(.*?)<\/h3>\s*<p\b[^>]*>(.*?)<\/p>/is',
                $bbFaqHtml,
                $bbMatches,
                PREG_SET_ORDER
            );

            foreach ($bbMatches as $bbMatch) {

                $bbQuestion = html_entity_decode(
                    strip_tags($bbMatch[1]),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $bbAnswer = html_entity_decode(
                    strip_tags($bbMatch[2]),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $bbQuestion = trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        $bbQuestion
                    )
                );

                $bbAnswer = trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        $bbAnswer
                    )
                );

                if (
                    $bbQuestion !== ''
                    && $bbAnswer !== ''
                ) {

                    $bbFaqEntities[] = [
                        '@type' => 'Question',
                        'name' => $bbQuestion,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $bbAnswer,
                        ],
                    ];
                }
            }
        }
    }

    $bbFaqSchema = count($bbFaqEntities)
        ? [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $bbFaqEntities,
        ]
        : null;
@endphp

@if ($bbFaqSchema)
<script type="application/ld+json" id="bb-category-faq-schema">
{!! json_encode(
    $bbFaqSchema,
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}
</script>
@endif
