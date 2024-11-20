const client = algoliasearch("KFDUEMFZ33", "c4599a85058ade29b55be79b61f777a0");
const products = client.initIndex("products");

let enterPressed = false;

autocomplete(".aa-input-search", {}, [
    {
        source: autocomplete.sources.hits(products, { hitsPerPage: 5 }),
        displayKey: "name",
        templates: {
            header: '<div class="aa-suggestions-category">Products</div>',
            suggestion({
                base_image,
                slug,
                should_track,
                stock_count,
                price,
                selling_price,
                _highlightResult
            }) {
                return `<div class="product-suggestion d-flex">
                <div class="product-image">
                    <a href="${window.location.origin +
                        "/shop/" +
                        slug}"><img src="${base_image}" alt="" width="80" height="80"></a>
                </div>
                <div class="product-name"><a href="${window.location.origin +
                    "/shop/" +
                    slug}">${_highlightResult.name.value}</a></div>
                <div class="product-meta ml-auto d-flex flex-column">
                    <div class="product-actions mr-auto" style="width: 110px;">
                        <div class="product-availability" style="white-space: nowrap;">Availability:
                            ${
                                should_track
                                    ? stock_count
                                        ? '<span class="text-success">YES [' +
                                          stock_count +
                                          "]</span>"
                                        : '<span class="text-danger">NO</span>'
                                    : '<span class="text-success">YES</span>'
                            }
                        </div>
                        <div class="product-card__prices ${price == selling_price ? '' : 'has-special'}" style="display:flex;flex-direction:column;">
                            <span class="product-card__new-price" style="font-size:13px;">TK ${price}</span>
                            <span class="product-card__old-price" style="font-size:13px;">TK ${selling_price}</span>
                        </div>
                    </div>
                </div>
            </div>`;
            },
            empty(result) {
                return `<div class="p-2">No Result Found.</div>`;
            }
        }
    }
]).on('autocomplete:selected', function (event, {slug}, dataset) {
    window.location = window.location.origin + '/shop/' + slug;
    enterPressed = true;
})
.on('keyup', function (event) {
    if (event.keyCode == 13 && !enterPressed) {
        window.location = window.location.origin + '/shop?search=' + this.value;
    }
});
