<!-- Accounting Guide & Tutorial Modal -->
<div class="modal fade" id="accountingGuideModal" tabindex="-1" role="dialog" aria-labelledby="accountingGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-3">
                <h5 class="modal-title font-weight-bold text-white" id="accountingGuideModalLabel">
                    <i class="fa fa-graduation-cap mr-2"></i> Accounting Guide & Chart of Accounts Explained
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-4 text-dark" style="background: #f8fafc;">

                <!-- Core Concept Banner -->
                <div class="alert alert-light border shadow-sm mb-4" style="background: #ffffff;">
                    <div class="d-flex align-items-start">
                        <div class="p-3 bg-light-primary text-primary rounded mr-3">
                            <i class="fa fa-balance-scale fa-2x"></i>
                        </div>
                        <div>
                            <h5 class="font-weight-bold text-dark mb-1">Double-Entry Accounting in HotashKom</h5>
                            <p class="text-secondary mb-0">
                                Every business transaction affects at least two accounts (one <strong>Debit</strong> and one <strong>Credit</strong>) to keep your financial equation balanced:
                                <span class="badge badge-light-primary text-primary font-weight-bold ml-1 px-2 py-1" style="font-size: 13px;">
                                    Assets = Liabilities + Equity
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 1. The 5 Types of Accounts -->
                <div class="card shadow-sm border mb-4 bg-white">
                    <div class="card-header bg-light border-bottom p-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-th-large text-primary mr-2"></i> 1. The 5 Core Account Types
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 130px;">Account Type</th>
                                        <th>What It Represents</th>
                                        <th>Examples in Your System</th>
                                        <th style="width: 140px;" class="text-center">Increases With</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><span class="badge badge-primary font-weight-bold px-2 py-1">ASSET</span></td>
                                        <td>Valuable resources owned by your business that hold future economic value.</td>
                                        <td>Cash in Hand (#1001), Bank Account (#1002), bKash/Nagad (#1003), <strong>Inventory / Product Stock (#1004)</strong></td>
                                        <td class="text-center font-weight-bold text-danger">DEBIT (Dr)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-danger font-weight-bold px-2 py-1">LIABILITY</span></td>
                                        <td>Obligations and money owed by your business to external suppliers or creditors.</td>
                                        <td>Accounts Payable / Supplier Dues (#2001)</td>
                                        <td class="text-center font-weight-bold text-success">CREDIT (Cr)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-info font-weight-bold px-2 py-1">EQUITY</span></td>
                                        <td>The owner's residual interest and net capital invested in the company.</td>
                                        <td>Owner Capital / Investment (#3001), Owner Drawings / Withdrawals (#3002)</td>
                                        <td class="text-center font-weight-bold text-success">CREDIT (Cr)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-success font-weight-bold px-2 py-1">INCOME</span></td>
                                        <td>Gross earnings generated from normal business sales operations.</td>
                                        <td>Sales Revenue (#4001), Courier Delivery Charge Income</td>
                                        <td class="text-center font-weight-bold text-success">CREDIT (Cr)</td>
                                    </tr>
                                    <tr>
                                        <td><span class="badge badge-warning font-weight-bold px-2 py-1">EXPENSE</span></td>
                                        <td>Costs incurred and consumed in running day-to-day operations and selling goods.</td>
                                        <td><strong>Product Purchase Cost / COGS (#5001)</strong>, Office Rent (#5002), Electricity/AC Bills (#5003), Staff Salaries (#5004), Ad Spend (#5005), Snacks/Tea (#5006)</td>
                                        <td class="text-center font-weight-bold text-danger">DEBIT (Dr)</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- 2. Deep Dive: Inventory Asset vs Purchase Cost Expense -->
                <div class="card shadow-sm border mb-4 bg-white" style="border-left: 4px solid #4f46e5 !important;">
                    <div class="card-header bg-light border-bottom p-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-question-circle text-primary mr-2"></i> 2. Common Confusion: Inventory Asset (#1004) vs. Product Purchase Cost Expense (#5001)
                        </h6>
                    </div>
                    <div class="card-body p-4 text-dark">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="p-3 border rounded h-100" style="background: #eff6ff;">
                                    <h6 class="font-weight-bold text-primary mb-2">
                                        <i class="fa fa-shopping-cart mr-1"></i> Phase 1: When You Buy Products
                                    </h6>
                                    <p class="small text-secondary mb-2">
                                        When you purchase products, you haven't "spent/lost" that money yet. You converted cash into physical goods waiting in your warehouse.
                                    </p>
                                    <div class="bg-white p-2 rounded border small">
                                        <div class="d-flex justify-content-between text-dark">
                                            <span><strong>Debit (+)</strong>: ASSET Inventory #1004</span>
                                            <span class="text-danger font-weight-bold font-roboto">৳600</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark mt-1">
                                            <span><strong>Credit (-)</strong>: ASSET Cash in Hand #1001</span>
                                            <span class="text-success font-weight-bold font-roboto">৳600</span>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2 font-italic">
                                        Result: Your net worth is unchanged. Expense recognized = <strong>৳0</strong>.
                                    </small>
                                </div>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <div class="p-3 border rounded h-100" style="background: #f0fdf4;">
                                    <h6 class="font-weight-bold text-success mb-2">
                                        <i class="fa fa-truck mr-1"></i> Phase 2: When the Product is Sold & Delivered
                                    </h6>
                                    <p class="small text-secondary mb-2">
                                        When that product sells for <strong>৳1,000</strong>, the cost is recognized under the <em>Matching Principle</em>:
                                    </p>
                                    <div class="bg-white p-2 rounded border small">
                                        <div class="d-flex justify-content-between text-dark">
                                            <span><strong>Credit (+)</strong>: INCOME Sales Revenue #4001</span>
                                            <span class="text-success font-weight-bold font-roboto">৳1,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark mt-1">
                                            <span><strong>Debit (+)</strong>: EXPENSE Purchase Cost / COGS #5001</span>
                                            <span class="text-danger font-weight-bold font-roboto">৳600</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark mt-1">
                                            <span><strong>Credit (-)</strong>: ASSET Inventory #1004</span>
                                            <span class="text-muted font-weight-bold font-roboto">৳600</span>
                                        </div>
                                    </div>
                                    <div class="alert alert-success p-2 mt-2 mb-0 small text-center font-weight-bold">
                                        Net Gross Profit = ৳1,000 (Revenue) - ৳600 (COGS) = ৳400 Profit
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- 3. Practical Cheat Sheet for Everyday Operations -->
                <div class="card shadow-sm border mb-0 bg-white">
                    <div class="card-header bg-light border-bottom p-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-list-alt text-primary mr-2"></i> 3. Everyday Transactions Cheat Sheet
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Real-World Event</th>
                                        <th>Debit Account (Dr)</th>
                                        <th>Credit Account (Cr)</th>
                                        <th>Category Example</th>
                                    </tr>
                                </thead>
                                <tbody class="small text-dark">
                                    <tr>
                                        <td><strong>Office Snack / Tea bill</strong></td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Office Expenses</td>
                                        <td><span class="badge badge-primary">ASSET</span> Cash in Hand</td>
                                        <td><span class="badge badge-light border">#Snacks & Refreshment</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Facebook / Google Ad Spend</strong></td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Advertising & Marketing</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank Account / Card</td>
                                        <td><span class="badge badge-light border">#Facebook Ads / Dollar Cost</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Staff Salary Paid</strong></td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Salaries & Wages</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / bKash</td>
                                        <td><span class="badge badge-light border">#Staff Salary</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Owner adding personal cash (Investment)</strong></td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / Cash in Hand</td>
                                        <td><span class="badge badge-info">EQUITY</span> Owner Capital</td>
                                        <td><span class="badge badge-light border">#Capital Investment</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Owner taking money for personal use</strong></td>
                                        <td><span class="badge badge-info">EQUITY</span> Owner Withdrawals</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / Cash in Hand</td>
                                        <td><span class="badge badge-light border">#Personal Withdrawal</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cash transfer from Bank to Cash in Hand</strong></td>
                                        <td><span class="badge badge-primary">ASSET</span> Cash in Hand</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank Account</td>
                                        <td><span class="badge badge-light border">#Internal Transfer</span></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Paying Supplier Due Balance</strong></td>
                                        <td><span class="badge badge-danger">LIABILITY</span> Accounts Payable</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / Cash in Hand</td>
                                        <td><span class="badge badge-light border">#Supplier Payment</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-primary btn-sm px-4 font-weight-bold" data-dismiss="modal">
                    <i class="fa fa-check mr-1"></i> Got It, Thanks!
                </button>
            </div>
        </div>
    </div>
</div>
