<?php $__env->startSection('title', 'Payment Settings — Super Admin'); ?>
<?php $__env->startSection('page-title', 'Payment Settings'); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid py-4" style="max-width: 900px;">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="mb-0"><i class="fas fa-landmark text-warning me-2"></i>Payment Settings</h2>
        <a href="<?php echo e(route('admin.subscriptions.index')); ?>" class="btn btn-sm btn-outline-secondary"><i class="fas fa-credit-card me-1"></i>Subscriptions</a>
    </div>

    <?php if(session('success')): ?>
        <div class="alert alert-success"><?php echo e(session('success')); ?></div>
    
    <?php if(session('error')): ?>
        <div class="alert alert-danger"><?php echo e(session('error')); ?></div>
    
    <?php if($errors->any()): ?>
        <div class="alert alert-danger"><?php echo e($errors->first()); ?></div>
    

    <div class="row g-4">
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="mb-1">Where subscription money goes</h5>
                    <p class="text-muted small">
                        Plan upgrades and extra-business purchases are charged via M-Pesa STK push.
                        Choose whether they settle to <strong>your own bank account</strong> (via the bank's
                        public M-Pesa deposit paybill, the same mechanism businesses use) or Paystack.
                    </p>

                    <form method="POST" action="<?php echo e(route('admin.payment-settings.update')); ?>">
                        <?php echo csrf_field(); ?>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Gateway</label>
                            <select name="gateway" id="gateway" class="form-select" onchange="toggleDaraja(this.value)">
                                <option value="paystack" <?php echo e(($dest['gateway'] ?? 'paystack') === 'paystack' ? 'selected' : ''); ?>>Paystack (external)</option>
                                <option value="daraja" <?php echo e(($dest['gateway'] ?? 'paystack') === 'daraja' ? 'selected' : ''); ?>>M-Pesa STK → my bank account</option>
                            </select>
                        </div>

                        <div id="daraja-fields" style="<?php echo e(($dest['gateway'] ?? 'paystack') === 'daraja' ? '' : 'display:none;'); ?>">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Bank</label>
                                <select name="bank" class="form-select">
                                    <option value="">— choose bank —</option>
                                    <?php $__currentLoopData = $banks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $bank): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <option value="<?php echo e($key); ?>" <?php echo e($dest['bank'] === $key ? 'selected' : ''); ?>>
                                            <?php echo e($bank['name']); ?> (paybill <?php echo e($bank['paybill']); ?>)
                                        </option>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Your bank account number</label>
                                <input type="text" name="account" class="form-control" value="<?php echo e($dest['account']); ?>" inputmode="numeric" placeholder="e.g. 1234567890123">
                                <div class="form-text">
                                    Sent as the M-Pesa Account Reference, so funds land on this exact account.
                                    Double-check it — wrong digits send money elsewhere.
                                </div>
                            </div>
                        </div>

                        <button class="btn btn-warning"><i class="fas fa-save me-1"></i> Save payment settings</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-5">
            <div class="card shadow-sm h-100 <?php echo e($dest['ready'] ? 'border-success' : ''); ?>">
                <div class="card-body">
                    <h5 class="mb-3">Current status</h5>
                    <?php if($dest['ready']): ?>
                        <p class="mb-2"><span class="badge bg-success">Active — M-Pesa STK</span></p>
                        <ul class="list-unstyled small mb-0">
                            <li><i class="fas fa-landmark text-muted me-2"></i><strong><?php echo e($dest['bank_name']); ?></strong></li>
                            <li><i class="fas fa-hashtag text-muted me-2"></i>Paybill <?php echo e($dest['paybill']); ?></li>
                            <li><i class="fas fa-credit-card text-muted me-2"></i>Account <?php echo e($dest['account']); ?></li>
                        </ul>
                    <?php else: ?>
                        <p class="mb-2"><span class="badge bg-secondary"><?php echo e(($dest['gateway'] ?? 'paystack') === 'daraja' ? 'Incomplete' : 'Paystack'); ?></span></p>
                        <?php if(($dest['gateway'] ?? 'paystack') === 'daraja'): ?>
                            <p class="small text-muted mb-0">Daraja is selected but a bank/account is missing — payments still go through Paystack until you save valid details.</p>
                        <?php else: ?>
                            <p class="small text-muted mb-0">Customers pay through Paystack. Switch above to send money straight to your bank via M-Pesa.</p>
                        
                    
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        STK pushes are signed with the platform Daraja app
                        (org shortcode + passkey from the environment). A payer's phone gets
                        the PIN prompt; the callback marks the subscription paid automatically.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <?php if($recentPayments->isNotEmpty()): ?>
    <div class="card shadow-sm mt-4">
        <div class="card-body">
            <h5>Recent subscription payments</h5>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead><tr>
                        <th>Business</th><th>Plan</th><th>Amount</th><th>Gateway</th><th>Status</th><th>Receipt</th><th>Date</th>
                    </tr></thead>
                    <tbody>
                    <?php $__currentLoopData = $recentPayments; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <tr>
                            <td><?php echo e($p->business_name ?? '—'); ?></td>
                            <td><?php echo e(ucfirst(str_replace('_', ' ', $p->plan))); ?></td>
                            <td>KES <?php echo e(number_format($p->amount)); ?></td>
                            <td><span class="badge bg-light text-dark border"><?php echo e(ucfirst($p->gateway ?? 'paystack')); ?></span></td>
                            <td>
                                <span class="badge bg-<?php echo e($p->status === 'completed' ? 'success' : ($p->status === 'failed' ? 'danger' : 'warning text-dark')); ?>"><?php echo e(ucfirst($p->status)); ?></span>
                            </td>
                            <td class="small"><?php echo e($p->mpesa_receipt_number ?? '—'); ?></td>
                            <td class="small text-muted"><?php echo e(\Carbon\Carbon::parse($p->created_at)->format('d M Y, H:i')); ?></td>
                        </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
</div>

<script>
function toggleDaraja(v) {
    document.getElementById('daraja-fields').style.display = v === 'daraja' ? '' : 'none';
}
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.admin', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH D:\Shopybook\resources\views/admin/payments/settings.blade.php ENDPATH**/ ?>