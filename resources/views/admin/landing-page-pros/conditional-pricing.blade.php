<details class="lp-combo-editor">
    <summary>Combo pricing (optional)</summary>
    <label class="d-block">
        <input type="hidden" name="section_settings[order_form][conditional_pricing_enabled]" value="0">
        <input type="checkbox" name="section_settings[order_form][conditional_pricing_enabled]" value="1"
            x-model="conditionalPricingEnabled"> Enable combo offers
    </label>
    <p class="text-muted">Offer prices for specific products, mixed combos, or any products together. All colors and sizes count. Otherwise regular catalog prices apply.</p>
    <div x-show="conditionalPricingEnabled" x-cloak>
        <details class="p-2 mb-2 lp-combo-help rounded">
            <summary>How combo pricing works</summary>
            <p class="mb-2"><strong>Any products:</strong> count pieces across every product on this page. The highest qualifying minimum sets the per-piece rate for the entire order. Example: 2 pieces for ৳1,690, or 3 pieces for ৳2,490. This takes priority over specific-product offers when eligible; otherwise those offers apply.</p>
            <p class="mb-2"><strong>Product offer:</strong> choose one product, its minimum pieces, and the total for that quantity. The highest qualifying minimum sets the per-piece rate.</p>
            <p class="mb-2"><strong>Mixed combo:</strong> add two or more product conditions. The total is for one complete set, which repeats as many times as possible.</p>
            <p class="mb-0">Larger mixed sets apply first; ties use the order below. Pieces are used once. Extras then use their product offer, or regular catalog prices if no product offer qualifies. Delivery is separate and the final subtotal is rounded to the nearest taka.</p>
        </details>
        <div class="d-flex justify-content-between align-items-center mb-2">
            <strong x-text="conditionalRules.length + (conditionalRules.length === 1 ? ' offer' : ' offers')"></strong>
            <button type="button" class="btn btn-sm btn-outline-primary" @click="addConditionalRule"
                :disabled="conditionalRules.length >= 30 || !campaignProducts.length">Add offer</button>
        </div>
        <p class="text-muted" x-show="!campaignProducts.length">Add products and enable “Show on page” before configuring offers.</p>
        <div class="lp-editor-rows">
            <template x-for="(rule, ruleIndex) in conditionalRules" :key="rule.key">
                <details class="lp-editor-row" data-conditional-rule>
                    <summary class="lp-combo-summary" :title="conditionalRuleDescription(rule)">
                        <span class="lp-combo-chevron" aria-hidden="true">▸</span>
                        <span class="lp-combo-summary-label" x-text="'Offer ' + (ruleIndex + 1) + ': ' + (rule.name || 'Untitled') + ' → ৳' + (rule.total_price || '…')"></span>
                    </summary>
                    <div class="lp-product-actions mb-2">
                        <button type="button" class="btn btn-sm btn-light lp-combo-icon" @click="moveRow(conditionalRules, ruleIndex, -1)" :disabled="ruleIndex === 0" aria-label="Move offer up">↑</button>
                        <button type="button" class="btn btn-sm btn-light lp-combo-icon" @click="moveRow(conditionalRules, ruleIndex, 1)" :disabled="ruleIndex === conditionalRules.length - 1" aria-label="Move offer down">↓</button>
                        <button type="button" class="btn btn-sm btn-outline-danger" @click="conditionalRules.splice(ruleIndex, 1)">Remove offer</button>
                    </div>
                    <div class="lp-combo-fields mb-2">
                        <div>
                            <label :for="'offer-scope-' + rule.key">Offer applies to</label>
                            <select :id="'offer-scope-' + rule.key" class="form-control" x-model="rule.scope"
                                @change="ensureConditionalRequirements(rule)"
                                :name="`section_settings[order_form][conditional_rules][${ruleIndex}][scope]`">
                                <option value="products">Specific products / mixed combo</option>
                                <option value="any">Any products (mix & match)</option>
                            </select>
                        </div>
                        <div x-show="rule.scope === 'any'">
                            <label :for="'offer-any-minimum-' + rule.key">Min. total pieces</label>
                            <input type="number" :id="'offer-any-minimum-' + rule.key" class="form-control" min="1" max="100" step="1"
                                x-model.number="rule.min_quantity" :disabled="rule.scope !== 'any'"
                                :name="`section_settings[order_form][conditional_rules][${ruleIndex}][min_quantity]`">
                        </div>
                    </div>
                    <div class="lp-combo-fields mb-2">
                        <div>
                            <label :for="'offer-name-' + rule.key">Offer name (optional)</label>
                            <input type="text" :id="'offer-name-' + rule.key" class="form-control" maxlength="100"
                                placeholder="e.g. Shirt + pant combo" x-model="rule.name"
                                :name="`section_settings[order_form][conditional_rules][${ruleIndex}][name]`">
                        </div>
                        <div>
                            <label :for="'offer-total-' + rule.key" x-text="rule.scope !== 'any' && rule.requirements.length > 1 ? 'Combo total (৳)' : 'Tier total (৳)'"></label>
                            <input type="number" :id="'offer-total-' + rule.key" class="form-control" :min="conditionalPricingEnabled ? 1 : null" :max="conditionalPricingEnabled ? 10000000 : null" step="1"
                                x-model.number="rule.total_price" placeholder="e.g. 1490"
                                :name="`section_settings[order_form][conditional_rules][${ruleIndex}][total_price]`">
                        </div>
                    </div>
                    <p class="text-muted mb-2" x-show="rule.scope === 'any'">No product selection needed. All products, colors and sizes on this page count toward the minimum. This total sets the per-piece rate until a higher tier applies. Totals are rounded to the nearest taka.</p>
                    <div x-show="rule.scope !== 'any'">
                    <template x-for="(requirement, conditionIndex) in rule.requirements" :key="requirement.key">
                        <div class="lp-combo-condition mb-2">
                            <div class="lp-combo-condition-product">
                                <label :for="'offer-product-' + requirement.key">Product</label>
                                <select :id="'offer-product-' + requirement.key" class="form-control" x-model.number="requirement.product_id" :disabled="rule.scope === 'any'"
                                    x-effect="syncConditionalProductSelect($el, requirement.product_id, campaignProducts)"
                                    :name="`section_settings[order_form][conditional_rules][${ruleIndex}][requirements][${conditionIndex}][product_id]`">
                                    <option value="">Choose product</option>
                                    <template x-for="product in campaignProducts" :key="product.id">
                                        <option :value="product.id" x-text="product.label"
                                            :disabled="rule.requirements.some(other => other.key !== requirement.key && Number(other.product_id) === Number(product.id))"></option>
                                    </template>
                                </select>
                                <small class="text-danger" x-show="requirement.product_id && !campaignProducts.some(product => Number(product.id) === Number(requirement.product_id))">This product is no longer shown on the page. Choose another product.</small>
                            </div>
                            <div class="lp-combo-condition-quantity">
                                <label :for="'offer-minimum-' + requirement.key">Min. pieces</label>
                                <input type="number" :id="'offer-minimum-' + requirement.key" class="form-control" :min="conditionalPricingEnabled ? 1 : null" :max="conditionalPricingEnabled ? 100 : null" step="1" :disabled="rule.scope === 'any'"
                                    x-model.number="requirement.min_quantity"
                                    :name="`section_settings[order_form][conditional_rules][${ruleIndex}][requirements][${conditionIndex}][min_quantity]`">
                            </div>
                            <div class="lp-combo-condition-remove">
                                <button type="button" class="btn btn-sm btn-outline-danger lp-combo-icon" @click="rule.requirements.splice(conditionIndex, 1)"
                                    :disabled="rule.requirements.length === 1" aria-label="Remove product condition" title="Remove product condition">×</button>
                            </div>
                        </div>
                    </template>
                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="addConditionalRequirement(rule)"
                        :disabled="rule.requirements.length >= 10 || rule.requirements.length >= campaignProducts.length">Add product</button>
                    </div>
                </details>
            </template>
        </div>
        <p class="text-muted mt-2" x-show="!conditionalRules.length">Add separate product offers and then any mixed combos you want to sell.</p>
    </div>
</details>
