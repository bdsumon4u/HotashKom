@php
    $faqItems = old(
        'faqs',
        isset($blog) && is_array($blog->faqs)
            ? $blog->faqs
            : []
    );

    if (! is_array($faqItems) || count($faqItems) === 0) {
        $faqItems = [
            [
                'question' => '',
                'answer' => '',
            ],
        ];
    }
@endphp

<div id="blog-faq-editor" class="mt-4">
    <h4 class="border-bottom pb-2">Frequently Asked Questions</h4>

    <p class="text-muted">
        Add only useful questions that are answered on this page.
        Completed FAQs will appear below the blog content and in FAQ schema.
        Maximum 20 FAQs.
    </p>

    <div id="blog-faq-items">
        @foreach($faqItems as $index => $faq)
            <div
                class="faq-admin-item border rounded p-3 mb-3"
                data-faq-item
                data-index="{{ $index }}">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <strong>
                        FAQ <span data-faq-number>{{ $loop->iteration }}</span>
                    </strong>

                    <button
                        type="button"
                        class="btn btn-sm btn-outline-danger"
                        data-remove-faq>
                        Remove
                    </button>
                </div>

                <div class="form-group">
                    <label for="faq-question-{{ $index }}">
                        Question
                    </label>

                    <input
                        type="text"
                        id="faq-question-{{ $index }}"
                        name="faqs[{{ $index }}][question]"
                        value="{{ old("faqs.$index.question", $faq['question'] ?? '') }}"
                        maxlength="500"
                        class="form-control @error("faqs.$index.question") is-invalid @enderror"
                        placeholder="Example: How long does delivery take?">

                    @error("faqs.$index.question")
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group mb-0">
                    <label for="faq-answer-{{ $index }}">
                        Answer
                    </label>

                    <textarea
                        id="faq-answer-{{ $index }}"
                        name="faqs[{{ $index }}][answer]"
                        rows="4"
                        maxlength="5000"
                        class="form-control @error("faqs.$index.answer") is-invalid @enderror"
                        placeholder="Write a clear and complete answer.">{{ old("faqs.$index.answer", $faq['answer'] ?? '') }}</textarea>

                    @error("faqs.$index.answer")
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endforeach
    </div>

    <button
        type="button"
        id="add-blog-faq"
        class="btn btn-outline-primary">
        Add FAQ
    </button>
</div>

<template id="blog-faq-template">
    <div
        class="faq-admin-item border rounded p-3 mb-3"
        data-faq-item
        data-index="__INDEX__">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <strong>
                FAQ <span data-faq-number></span>
            </strong>

            <button
                type="button"
                class="btn btn-sm btn-outline-danger"
                data-remove-faq>
                Remove
            </button>
        </div>

        <div class="form-group">
            <label for="faq-question-__INDEX__">
                Question
            </label>

            <input
                type="text"
                id="faq-question-__INDEX__"
                name="faqs[__INDEX__][question]"
                maxlength="500"
                class="form-control"
                placeholder="Example: How long does delivery take?">
        </div>

        <div class="form-group mb-0">
            <label for="faq-answer-__INDEX__">
                Answer
            </label>

            <textarea
                id="faq-answer-__INDEX__"
                name="faqs[__INDEX__][answer]"
                rows="4"
                maxlength="5000"
                class="form-control"
                placeholder="Write a clear and complete answer."></textarea>
        </div>
    </div>
</template>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const editor = document.getElementById('blog-faq-editor');

    if (!editor || editor.dataset.initialized === 'true') {
        return;
    }

    editor.dataset.initialized = 'true';

    const list = document.getElementById('blog-faq-items');
    const addButton = document.getElementById('add-blog-faq');
    const template = document.getElementById('blog-faq-template');

    let nextIndex = Array.from(
        list.querySelectorAll('[data-faq-item]')
    ).reduce(function (highest, item) {
        return Math.max(highest, Number(item.dataset.index) || 0);
    }, -1) + 1;

    function updateFaqNumbers() {
        list.querySelectorAll('[data-faq-item]').forEach(function (item, index) {
            const number = item.querySelector('[data-faq-number]');

            if (number) {
                number.textContent = index + 1;
            }
        });
    }

    addButton.addEventListener('click', function () {
        const currentCount = list.querySelectorAll('[data-faq-item]').length;

        if (currentCount >= 20) {
            window.alert('A maximum of 20 FAQs is allowed.');
            return;
        }

        const html = template.innerHTML.replace(/__INDEX__/g, nextIndex);

        list.insertAdjacentHTML('beforeend', html);
        nextIndex++;
        updateFaqNumbers();
    });

    list.addEventListener('click', function (event) {
        const removeButton = event.target.closest('[data-remove-faq]');

        if (!removeButton) {
            return;
        }

        const item = removeButton.closest('[data-faq-item]');

        if (item) {
            item.remove();
            updateFaqNumbers();
        }
    });

    updateFaqNumbers();
});
</script>
@endpush
