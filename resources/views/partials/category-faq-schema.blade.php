@php
    $nmFaqEntities = [];

    if (
        isset($category)
        && $category instanceof \App\Models\Category
        && !empty($category->content)
    ) {

        $nmContent = $category->content;

        $nmStart = strpos(
            $nmContent,
            '<!-- NM FAQ START -->'
        );

        $nmEnd = strpos(
            $nmContent,
            '<!-- NM FAQ END -->'
        );

        if (
            $nmStart !== false
            && $nmEnd !== false
            && $nmEnd > $nmStart
        ) {

            $nmFaqHtml = substr(
                $nmContent,
                $nmStart,
                $nmEnd - $nmStart
            );

            preg_match_all(
                '/<h3\b[^>]*>(.*?)<\/h3>\s*<p\b[^>]*>(.*?)<\/p>/is',
                $nmFaqHtml,
                $nmMatches,
                PREG_SET_ORDER
            );

            foreach ($nmMatches as $nmMatch) {

                $nmQuestion = html_entity_decode(
                    strip_tags($nmMatch[1]),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $nmAnswer = html_entity_decode(
                    strip_tags($nmMatch[2]),
                    ENT_QUOTES | ENT_HTML5,
                    'UTF-8'
                );

                $nmQuestion = trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        $nmQuestion
                    )
                );

                $nmAnswer = trim(
                    preg_replace(
                        '/\s+/u',
                        ' ',
                        $nmAnswer
                    )
                );

                if (
                    $nmQuestion !== ''
                    && $nmAnswer !== ''
                ) {

                    $nmFaqEntities[] = [
                        '@type' => 'Question',
                        'name' => $nmQuestion,
                        'acceptedAnswer' => [
                            '@type' => 'Answer',
                            'text' => $nmAnswer,
                        ],
                    ];
                }
            }
        }
    }

    $nmFaqSchema = count($nmFaqEntities)
        ? [
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => $nmFaqEntities,
        ]
        : null;
@endphp

@if ($nmFaqSchema)
<script type="application/ld+json" id="nm-category-faq-schema">
{!! json_encode(
    $nmFaqSchema,
    JSON_UNESCAPED_UNICODE
    | JSON_UNESCAPED_SLASHES
    | JSON_HEX_TAG
    | JSON_HEX_AMP
    | JSON_HEX_APOS
    | JSON_HEX_QUOT
) !!}
</script>
@endif
