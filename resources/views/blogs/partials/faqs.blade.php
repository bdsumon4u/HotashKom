@php
    $visibleFaqs = collect($blog->faqs ?? [])
        ->filter(fn ($faq): bool => is_array($faq))
        ->map(function (array $faq): array {
            return [
                'question' => trim(
                    (string) ($faq['question'] ?? '')
                ),
                'answer' => trim(
                    (string) ($faq['answer'] ?? '')
                ),
            ];
        })
        ->filter(
            fn (array $faq): bool =>
                $faq['question'] !== ''
                && $faq['answer'] !== ''
        )
        ->values();
@endphp

@if($visibleFaqs->isNotEmpty())
    <section
        id="frequently-asked-questions"
        class="blog-faq"
        aria-labelledby="blog-faq-heading">

        <header class="blog-faq__header">
            <h2 id="blog-faq-heading">
                Frequently Asked Questions
            </h2>

            <p>
                Find clear answers to common questions about this topic.
            </p>
        </header>

        <div class="blog-faq__items">
            @foreach($visibleFaqs as $faq)
                <details
                    class="blog-faq__item"
                    @if($loop->first) open @endif>

                    <summary class="blog-faq__question">
                        <span>{{ $faq['question'] }}</span>

                        <span
                            class="blog-faq__icon"
                            aria-hidden="true">
                            +
                        </span>
                    </summary>

                    <div class="blog-faq__answer">
                        <p>{!! nl2br(e($faq['answer'])) !!}</p>
                    </div>
                </details>
            @endforeach
        </div>
    </section>

    <style>
        .blog-faq {
            margin-top: 3rem;
            padding-top: 2.5rem;
            border-top: 1px solid #e8e8e8;
        }

        .blog-faq__header {
            margin-bottom: 1.5rem;
        }

        .blog-faq__header h2 {
            margin-bottom: 0.5rem;
            color: #222;
            font-size: 1.8rem;
            font-weight: 800;
            line-height: 1.3;
        }

        .blog-faq__header p {
            margin: 0;
            color: #666;
            font-size: 1rem;
            line-height: 1.6;
        }

        .blog-faq__item {
            margin-bottom: 0.8rem;
            overflow: hidden;
            border: 1px solid #e2e2e2;
            border-radius: 8px;
            background: #fff;
        }

        .blog-faq__item[open] {
            border-color: #1DBF72;
            box-shadow: 0 4px 14px rgba(29, 191, 114, 0.08);
        }

        .blog-faq__question {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            padding: 1rem 1.15rem;
            color: #222;
            font-size: 1.05rem;
            font-weight: 700;
            line-height: 1.5;
            cursor: pointer;
            list-style: none;
        }

        .blog-faq__question::-webkit-details-marker {
            display: none;
        }

        .blog-faq__question:focus-visible {
            outline: 3px solid rgba(29, 191, 114, 0.3);
            outline-offset: -3px;
        }

        .blog-faq__icon {
            flex: 0 0 auto;
            color: #1DBF72;
            font-size: 1.6rem;
            font-weight: 400;
            line-height: 1;
            transition: transform 0.2s ease;
        }

        .blog-faq__item[open] .blog-faq__icon {
            transform: rotate(45deg);
        }

        .blog-faq__answer {
            padding: 0 1.15rem 1.15rem;
            color: #444;
            font-size: 1rem;
            line-height: 1.75;
        }

        .blog-faq__answer p {
            margin: 0;
        }

        @media (max-width: 767px) {
            .blog-faq {
                margin-top: 2rem;
                padding-top: 2rem;
            }

            .blog-faq__header h2 {
                font-size: 1.45rem;
            }

            .blog-faq__question {
                padding: 0.9rem 1rem;
                font-size: 1rem;
            }

            .blog-faq__answer {
                padding: 0 1rem 1rem;
            }
        }
    </style>
@endif