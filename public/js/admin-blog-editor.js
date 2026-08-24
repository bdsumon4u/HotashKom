(function () {
    function bootBlogEditor() {
        const config = document.querySelector('[data-blog-inline-image-editor]');
        const textarea = document.querySelector('textarea[name="content"]');

        if (!config || !textarea || !window.tinymce || tinymce.get(textarea.id)) return;

        const uploadUrl = config.dataset.uploadUrl;
        const csrfToken =
            document.querySelector('meta[name="csrf-token"]')?.content ||
            document.querySelector('input[name="_token"]')?.value;

        const escapeHtml = (value) => {
            const element = document.createElement('div');
            element.textContent = value;
            return element.innerHTML;
        };

        function openImageModal(editor) {
            const bookmark = editor.selection.getBookmark(2, true);
            const overlay = document.createElement('div');

            overlay.style.cssText =
                'position:fixed;inset:0;z-index:999999;display:flex;align-items:center;' +
                'justify-content:center;padding:20px;background:rgba(0,0,0,.55)';

            overlay.innerHTML = `
                <div style="width:100%;max-width:540px;padding:24px;border-radius:8px;background:#fff;box-shadow:0 16px 48px rgba(0,0,0,.25)">
                    <h3 style="margin:0 0 18px;font-size:20px">Upload & Insert Image</h3>

                    <label style="display:block;margin-bottom:14px">
                        <strong>Image file</strong>
                        <input name="file" type="file" required accept="image/jpeg,image/png,image/webp,image/gif"
                            style="display:block;width:100%;margin-top:6px">
                    </label>

                    <label style="display:block;margin-bottom:14px">
                        <strong>Alt Text</strong>
                        <input name="alt_text" type="text" required maxlength="255"
                            placeholder="Describe the image accurately"
                            style="display:block;width:100%;box-sizing:border-box;margin-top:6px;padding:9px">
                    </label>

                    <label style="display:block;margin-bottom:20px">
                        <strong>Caption <span style="font-weight:normal">(optional)</span></strong>
                        <input name="caption" type="text" maxlength="500"
                            placeholder="Optional visible image caption"
                            style="display:block;width:100%;box-sizing:border-box;margin-top:6px;padding:9px">
                    </label>

                    <div style="display:flex;justify-content:flex-end;gap:10px">
                        <button type="button" data-cancel style="padding:9px 14px">Cancel</button>
                        <button type="button" data-submit style="padding:9px 14px;border:0;border-radius:4px;background:#1DBF73;color:#fff">
                            Upload & Insert
                        </button>
                    </div>

                    <p data-error style="display:none;margin:14px 0 0;color:#c62828"></p>
                </div>
            `;

            document.body.appendChild(overlay);

            const fileInput = overlay.querySelector('[name="file"]');
            const altInput = overlay.querySelector('[name="alt_text"]');
            const captionInput = overlay.querySelector('[name="caption"]');
            const submitButton = overlay.querySelector('[data-submit]');
            const errorBox = overlay.querySelector('[data-error]');

            const close = () => {
                overlay.remove();
                editor.focus();
            };

            overlay.querySelector('[data-cancel]').addEventListener('click', close);

            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) close();
            });

            submitButton.addEventListener('click', async () => {
                const file = fileInput.files[0];
                const altText = altInput.value.trim();
                const caption = captionInput.value.trim();

                errorBox.style.display = 'none';

                if (!file || !altText) {
                    errorBox.textContent = !file
                        ? 'Please choose an image file.'
                        : 'Alt Text is required.';
                    errorBox.style.display = 'block';
                    return;
                }

                submitButton.disabled = true;
                submitButton.textContent = 'Uploading...';

                try {
                    const formData = new FormData();
                    formData.append('file', file);
                    formData.append('alt_text', altText);

                    const response = await fetch(uploadUrl, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: formData
                    });

                    const result = await response.json();

                    if (!response.ok || !result.url) {
                        throw new Error(
                            result.message ||
                            result.errors?.file?.[0] ||
                            result.errors?.alt_text?.[0] ||
                            'Image upload failed.'
                        );
                    }

                    editor.selection.moveToBookmark(bookmark);

                    const image = `<img src="${escapeHtml(result.url)}" alt="${escapeHtml(altText)}" loading="lazy" class="blog-inline-image">`;

                    const html = caption
                        ? `<figure class="blog-inline-figure">${image}<figcaption>${escapeHtml(caption)}</figcaption></figure><p><br></p>`
                        : `<p>${image}</p><p><br></p>`;

                    editor.insertContent(html);
                    editor.save();
                    close();
                } catch (error) {
                    errorBox.textContent = error.message || 'Image upload failed. Please try again.';
                    errorBox.style.display = 'block';
                    submitButton.disabled = false;
                    submitButton.textContent = 'Upload & Insert';
                }
            });

            fileInput.focus();
        }

        tinymce.init({
            target: textarea,
            height: 520,
            menubar: false,
            promotion: false,
            statusbar: true,
            plugins: 'lists link image table code autoresize',
            toolbar: 'undo redo | blocks | bold italic underline | bullist numlist | link uploadinlineimage | table | code',
            content_style: `
                body { font-family: Arial, sans-serif; font-size: 16px; line-height: 1.7; }
                .blog-inline-image { max-width: 100%; height: auto; }
                .blog-inline-figure { margin: 24px 0; }
                .blog-inline-figure figcaption { margin-top: 8px; color: #666; font-size: 14px; }
            `,
            setup: function (editor) {
                editor.ui.registry.addButton('uploadinlineimage', {
                    icon: 'image',
                    tooltip: 'Upload & Insert Image',
                    onAction: function () {
                        openImageModal(editor);
                    }
                });

                editor.on('change input undo redo', function () {
                    editor.save();
                });
            }
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', bootBlogEditor);
    } else {
        bootBlogEditor();
    }
})();