<!-- Accounting Guide & Tutorial Modal (Bilingual: English + বাংলা) -->
<div class="modal fade" id="accountingGuideModal" tabindex="-1" role="dialog" aria-labelledby="accountingGuideModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable" role="document">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-primary text-white p-3">
                <h5 class="modal-title font-weight-bold text-white" id="accountingGuideModalLabel">
                    <i class="fa fa-graduation-cap mr-2"></i> Accounting Guide & Chart of Accounts (হিসাব নির্দেশিকা ও নিয়মাবলী)
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
                            <h5 class="font-weight-bold text-dark mb-1">
                                Double-Entry Accounting System (দ্বৈত-দাখিলা হিসাব পদ্ধতি)
                            </h5>
                            <p class="text-secondary mb-1">
                                Every business transaction affects at least two accounts (one <strong>Debit</strong> and one <strong>Credit</strong>) to keep your financial balance exact:
                            </p>
                            <p class="text-dark small mb-0 font-weight-bold">
                                প্রতিটি ব্যবসায়িক লেনদেন সবসময় ২টি অ্যাকাউন্টে প্রভাব ফেলে (একটি <strong>ডেবিট (+)</strong> এবং একটি <strong>ক্রেডিট (-)</strong>):
                                <span class="badge badge-light-primary text-primary font-weight-bold ml-1 px-2 py-1" style="font-size: 13px;">
                                    Assets (সম্পদ) = Liabilities (দায়) + Equity (মালিকানাস্বত্ব/মূলধন)
                                </span>
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 1. The 5 Types of Accounts -->
                <div class="card shadow-sm border mb-4 bg-white">
                    <div class="card-header bg-light border-bottom p-3">
                        <h6 class="mb-0 font-weight-bold text-dark">
                            <i class="fa fa-th-large text-primary mr-2"></i> 1. The 5 Core Account Types (মূল ৫ ধরণের অ্যাকাউন্ট)
                        </h6>
                    </div>
                    <div class="card-body p-3">
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th style="width: 140px;">Account Type</th>
                                        <th>What It Represents (বিবরণ)</th>
                                        <th>Examples (উদাহরণ)</th>
                                        <th style="width: 150px;" class="text-center">Increases With (বাড়লে কী হবে)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary font-weight-bold px-2 py-1">ASSET</span>
                                            <small class="d-block text-muted font-weight-bold mt-1">সম্পদ</small>
                                        </td>
                                        <td>
                                            <strong>Valuable resources owned by your business.</strong><br>
                                            <span class="text-muted small">ব্যবসার নিজস্ব নগদ টাকা, ব্যাংক ব্যালেন্স ও বিক্রির জন্য রাখা পণ্য।</span>
                                        </td>
                                        <td>
                                            Cash in Hand (#1001), Bank Account (#1002), bKash/Nagad (#1003), <strong>Inventory / Stock (#1004)</strong>, Accounts Receivable (#1005)
                                        </td>
                                        <td class="text-center font-weight-bold text-danger">
                                            DEBIT (Dr) / ডেবিট (+)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge badge-danger font-weight-bold px-2 py-1">LIABILITY</span>
                                            <small class="d-block text-muted font-weight-bold mt-1">দায় / দেনা</small>
                                        </td>
                                        <td>
                                            <strong>Money or obligations owed to third parties/suppliers.</strong><br>
                                            <span class="text-muted small">সাপ্লায়ার বা অন্যদের কাছে ব্যবসার যে টাকা পরিশোধ করা বাকি আছে (বকেয়া)।</span>
                                        </td>
                                        <td>
                                            Accounts Payable / Supplier Dues (#2001)
                                        </td>
                                        <td class="text-center font-weight-bold text-success">
                                            CREDIT (Cr) / ক্রেডিট (+)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge badge-info font-weight-bold px-2 py-1">EQUITY</span>
                                            <small class="d-block text-muted font-weight-bold mt-1">মালিকানাস্বত্ব / পুঁজি</small>
                                        </td>
                                        <td>
                                            <strong>Owner's net investment or capital in the business.</strong><br>
                                            <span class="text-muted small">ব্যবসায় মালিকের ব্যক্তিগত টাকা বিনিয়োগ বা ব্যবসা থেকে ব্যক্তিগত কাজে টাকা উত্তোলন।</span>
                                        </td>
                                        <td>
                                            Owner's Capital / Investment (#3001), Owner's Drawings / Withdrawals (#3002)
                                        </td>
                                        <td class="text-center font-weight-bold text-success">
                                            CREDIT (Cr) / ক্রেডিট (+)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge badge-success font-weight-bold px-2 py-1">INCOME</span>
                                            <small class="d-block text-muted font-weight-bold mt-1">আয় / রাজস্ব</small>
                                        </td>
                                        <td>
                                            <strong>Gross earnings and revenue generated from sales.</strong><br>
                                            <span class="text-muted small">পণ্য বিক্রয়, ডেলিভারি চার্জ বা অন্যান্য মাধ্যমে ব্যবসায় উপার্জিত অর্থ।</span>
                                        </td>
                                        <td>
                                            Product Sales Revenue (#4001), Delivery Charges Income (#4002), Other Income (#4003)
                                        </td>
                                        <td class="text-center font-weight-bold text-success">
                                            CREDIT (Cr) / ক্রেডিট (+)
                                        </td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <span class="badge badge-warning font-weight-bold px-2 py-1">EXPENSE</span>
                                            <small class="d-block text-muted font-weight-bold mt-1">ব্যয় / খরচ</small>
                                        </td>
                                        <td>
                                            <strong>Costs incurred to run day-to-day operations and deliver orders.</strong><br>
                                            <span class="text-muted small">ব্যবসা পরিচালনা, বিক্রয় এবং ডেলিভারির জন্য নিয়মিত যে ব্যয়গুলো হয়।</span>
                                        </td>
                                        <td>
                                            <strong>Purchase Cost / COGS (#5001)</strong>, Advertising & Marketing (#5002), Dollar Cost (#5003), Salaries (#5004), Utility Bills (#5005), Office Rent (#5008)
                                        </td>
                                        <td class="text-center font-weight-bold text-danger">
                                            DEBIT (Dr) / ডেবিট (+)
                                        </td>
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
                            <i class="fa fa-question-circle text-primary mr-2"></i> 2. Common Question: Inventory Asset (#1004) vs. Product Purchase Cost Expense (#5001) <br>
                            <small class="text-muted font-weight-normal">পণ্য কেনার সময় ইনভেন্টরি স্টক ও পণ্য বিক্রির পর খরচ কিভাবে সমন্বয় হয়?</small>
                        </h6>
                    </div>
                    <div class="card-body p-4 text-dark">
                        <div class="row">
                            <div class="col-lg-6 mb-3">
                                <div class="p-3 border rounded h-100" style="background: #eff6ff;">
                                    <h6 class="font-weight-bold text-primary mb-1">
                                        <i class="fa fa-shopping-cart mr-1"></i> Phase 1: When You Buy Stock (পণ্য কেনার সময়)
                                    </h6>
                                    <p class="small text-secondary mb-2">
                                        পণ্য কেনার সাথে সাথেই তা "খরচ" হয় না। আপনার নগদ টাকা কেবল ফিজিক্যাল প্রোডাক্ট হিসেবে গুদামে জমা হলো (Asset Exchange):
                                    </p>
                                    <div class="bg-white p-2 rounded border small">
                                        <div class="d-flex justify-content-between text-dark">
                                            <span><strong>Debit (+)</strong>: ASSET Inventory / Stock #1004 (স্টক বাড়লো)</span>
                                            <span class="text-danger font-weight-bold font-roboto">৳600</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark mt-1">
                                            <span><strong>Credit (-)</strong>: ASSET Cash in Hand #1001 (ক্যাশ কমলো)</span>
                                            <span class="text-success font-weight-bold font-roboto">৳600</span>
                                        </div>
                                    </div>
                                    <small class="text-muted d-block mt-2 font-italic">
                                        ফলাফল: আপনার মোট সম্পদের পরিমাণ সমান রইলো। খরচ হিসেবে কাউন্ট হলো = <strong>৳০</strong>।
                                    </small>
                                </div>
                            </div>

                            <div class="col-lg-6 mb-3">
                                <div class="p-3 border rounded h-100" style="background: #f0fdf4;">
                                    <h6 class="font-weight-bold text-success mb-1">
                                        <i class="fa fa-truck mr-1"></i> Phase 2: When Sold & Delivered (পণ্য বিক্রির সময়)
                                    </h6>
                                    <p class="small text-secondary mb-2">
                                        যখন ওই ৳৬০০ দামের পণ্যটি <strong>৳১,০০০</strong> টাকায় বিক্রি হলো, তখন আয় ও বিক্রিত পণ্যের আসল খরচ (COGS) রিকগনাইজ হবে:
                                    </p>
                                    <div class="bg-white p-2 rounded border small">
                                        <div class="d-flex justify-content-between text-dark">
                                            <span><strong>Credit (+)</strong>: INCOME Sales Revenue #4001 (বিক্রয় আয়)</span>
                                            <span class="text-success font-weight-bold font-roboto">৳1,000</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark mt-1">
                                            <span><strong>Debit (+)</strong>: EXPENSE Purchase Cost / COGS #5001 (পণ্যের খরচ)</span>
                                            <span class="text-danger font-weight-bold font-roboto">৳600</span>
                                        </div>
                                        <div class="d-flex justify-content-between text-dark mt-1">
                                            <span><strong>Credit (-)</strong>: ASSET Inventory #1004 (গুদাম থেকে স্টক কমলো)</span>
                                            <span class="text-muted font-weight-bold font-roboto">৳600</span>
                                        </div>
                                    </div>
                                    <div class="alert alert-success p-2 mt-2 mb-0 small text-center font-weight-bold">
                                        মোট লাভ (Gross Profit) = ৳১,০০০ (আয়) - ৳৬০০ (পণ্যের ক্রয়মূল্য) = ৳৪০০ লাভ
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
                            <i class="fa fa-list-alt text-primary mr-2"></i> 3. Everyday Transactions Cheat Sheet (দৈনন্দিন লেনদেনের সহজ নির্দেশিকা)
                        </h6>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover table-bordered table-sm mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th>Real-World Event (বাস্তব লেনদেন)</th>
                                        <th>Debit Account / গ্রহণকারী (+)</th>
                                        <th>Credit Account / প্রদানকারী (-)</th>
                                        <th>Category Example (ক্যাটাগরি)</th>
                                    </tr>
                                </thead>
                                <tbody class="small text-dark">
                                    <tr>
                                        <td>
                                            <strong>Office Snack / Tea Bill</strong><br>
                                            <span class="text-muted">অফিসের চা/নাস্তা ও খাবার খরচ</span>
                                        </td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Office Snacks (#5006)</td>
                                        <td><span class="badge badge-primary">ASSET</span> Cash in Hand (#1001)</td>
                                        <td><span class="badge badge-light border">Tea, Coffee & Snacks</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Facebook / Google Ad Spend</strong><br>
                                            <span class="text-muted">বিজ্ঞাপন ও মার্কেটিং খরচ</span>
                                        </td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Advertising Cost (#5002)</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank Account / Card (#1002)</td>
                                        <td><span class="badge badge-light border">Facebook & Meta Ads</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Staff Salary Paid</strong><br>
                                            <span class="text-muted">কর্মচারীর মাসিক বেতন বা ভাতা প্রদান</span>
                                        </td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Staff Salaries (#5004)</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / bKash (#1002/#1003)</td>
                                        <td><span class="badge badge-light border">Staff Festival Bonus</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Owner Adding Personal Cash (Investment)</strong><br>
                                            <span class="text-muted">ব্যবসায় মালিকের ব্যক্তিগত টাকা বিনিয়োগ</span>
                                        </td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / Cash (#1001/#1002)</td>
                                        <td><span class="badge badge-info">EQUITY</span> Owner's Capital (#3001)</td>
                                        <td><span class="text-muted">-- No Category --</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Owner Taking Money for Personal Use (Drawings)</strong><br>
                                            <span class="text-muted">ব্যক্তিগত খরচের জন্য ব্যবসার টাকা উত্তোলন</span>
                                        </td>
                                        <td><span class="badge badge-info">EQUITY</span> Owner's Drawings (#3002)</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / Cash (#1001/#1002)</td>
                                        <td><span class="text-muted">-- No Category --</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Cash Transfer (Bank to Cash / Wallet)</strong><br>
                                            <span class="text-muted">ব্যাংক থেকে ক্যাশ বা বিকাশে টাকা স্থানান্তর</span>
                                        </td>
                                        <td><span class="badge badge-primary">ASSET</span> Cash in Hand (#1001)</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank Account (#1002)</td>
                                        <td><span class="text-muted">-- No Category --</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Supplier Due Payment</strong><br>
                                            <span class="text-muted">সাপ্লায়ারকে বকেয়া বিল পরিশোধ</span>
                                        </td>
                                        <td><span class="badge badge-danger">LIABILITY</span> Accounts Payable (#2001)</td>
                                        <td><span class="badge badge-primary">ASSET</span> Bank / Cash (#1001/#1002)</td>
                                        <td><span class="text-muted">-- No Category --</span></td>
                                    </tr>
                                    <tr>
                                        <td>
                                            <strong>Customer Return Restock</strong><br>
                                            <span class="text-muted">কাস্টমার রিটার্ন প্রোডাক্ট পুনরায় ইনভেন্টরি স্টকে ফেরত নেওয়া</span>
                                        </td>
                                        <td><span class="badge badge-primary">ASSET</span> Inventory / Stock (#1004)</td>
                                        <td><span class="badge badge-warning">EXPENSE</span> Purchase Cost / COGS (#5001)</td>
                                        <td><span class="text-muted">-- No Category --</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
            <div class="modal-footer bg-light p-3">
                <button type="button" class="btn btn-primary btn-sm px-4 font-weight-bold" data-dismiss="modal">
                    <i class="fa fa-check mr-1"></i> বুঝেছি / Close Guide
                </button>
            </div>
        </div>
    </div>
</div>
