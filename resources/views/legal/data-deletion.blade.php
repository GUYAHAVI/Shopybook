@extends('layouts.public')

@section('title', 'Data Deletion Instructions - Shopybook')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <h1>Data Deletion Instructions</h1>
        <p class="mb-0">How to request deletion of your data from Shopybook</p>
    </div>
</section>

{{-- ═══════════════ CONTENT ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="sb-docs-content">

                    <div class="sb-tip-box mb-4">
                        <i class="fas fa-info-circle"></i>
                        <div><strong>Note:</strong> This page explains how to delete your data from Shopybook. For social media platform data, you must follow each platform's own deletion process.</div>
                    </div>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">Account Deletion Options</h2>
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="sb-step-card text-center h-100">
                                    <i class="fas fa-user-minus fa-3x mb-3" style="color:#ff511a;"></i>
                                    <h5>Self-Service Deletion</h5>
                                    <p>Delete your account directly from your dashboard</p>
                                    <a href="{{ route('dashboard') }}" class="btn1">Go to Dashboard</a>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card text-center h-100">
                                    <i class="fas fa-envelope fa-3x mb-3" style="color:#7b2e2e;"></i>
                                    <h5>Email Request</h5>
                                    <p>Send us an email to request account deletion</p>
                                    <a href="mailto:privacy@shopybook.com?subject=Account Deletion Request" class="btnb">Send Email</a>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">Step-by-Step Instructions</h2>

                        <div class="accordion" id="deletionAccordion">
                            <div class="accordion-item sb-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button sb-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                        Method 1: Dashboard Deletion (Recommended)
                                    </button>
                                </h2>
                                <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#deletionAccordion">
                                    <div class="accordion-body">
                                        <ol>
                                            <li><strong>Log in</strong> to your Shopybook account</li>
                                            <li><strong>Go to Settings</strong> &rarr; Account Settings</li>
                                            <li><strong>Scroll down</strong> to the "Danger Zone" section</li>
                                            <li><strong>Click "Delete Account"</strong> button</li>
                                            <li><strong>Confirm deletion</strong> by typing your password</li>
                                            <li><strong>Check your email</strong> for confirmation</li>
                                        </ol>
                                        <div class="sb-tip-box" style="background:rgba(220,53,69,0.08); border-left-color:#dc3545;">
                                            <i class="fas fa-exclamation-triangle" style="color:#dc3545;"></i>
                                            <div><strong>Warning:</strong> This action is irreversible. All your data will be permanently deleted.</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item sb-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed sb-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                        Method 2: Email Request
                                    </button>
                                </h2>
                                <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#deletionAccordion">
                                    <div class="accordion-body">
                                        <p><strong>Send an email to:</strong> <a href="mailto:privacy@shopybook.com" style="color:#ff511a;">privacy@shopybook.com</a></p>
                                        <p><strong>Include the following information:</strong></p>
                                        <ul>
                                            <li>Subject: "Account Deletion Request"</li>
                                            <li>Your registered email address</li>
                                            <li>Your full name</li>
                                            <li>Reason for deletion (optional)</li>
                                            <li>Confirmation that you understand data cannot be recovered</li>
                                        </ul>
                                        <div class="sb-step-card">
                                            <strong>Email Template:</strong>
                                            <hr>
                                            <em>
                                                Subject: Account Deletion Request<br><br>
                                                Hello,<br><br>
                                                I would like to request the deletion of my Shopybook account and all associated data.<br><br>
                                                Account Details:<br>
                                                - Email: [your-email@example.com]<br>
                                                - Name: [Your Full Name]<br><br>
                                                I understand that this action is irreversible and all my data will be permanently deleted.<br><br>
                                                Thank you,<br>
                                                [Your Name]
                                            </em>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="accordion-item sb-accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed sb-accordion-btn" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                        Method 3: Facebook App Data Deletion
                                    </button>
                                </h2>
                                <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#deletionAccordion">
                                    <div class="accordion-body">
                                        <p>If you connected your Facebook account and want to delete only Facebook-related data:</p>
                                        <ol>
                                            <li><strong>Go to your Facebook Settings</strong> &rarr; Apps and Websites</li>
                                            <li><strong>Find "Shopybook"</strong> in your active apps</li>
                                            <li><strong>Click "Remove"</strong> or "Revoke Access"</li>
                                            <li><strong>Your Facebook data</strong> will be automatically deleted from our systems within 24 hours</li>
                                        </ol>
                                        <p><strong>Alternative:</strong> In your Shopybook dashboard, go to Social Media &rarr; Connected Accounts and click "Disconnect" next to Facebook.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">What Gets Deleted</h2>
                        <div class="row">
                            <div class="col-md-6">
                                <h5 style="color:#43ba7f;"><i class="fas fa-check-circle me-2"></i>Data We Delete</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Account information</li>
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Social media tokens</li>
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Posted content drafts</li>
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Analytics data</li>
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Billing information</li>
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Support conversations</li>
                                    <li><i class="fas fa-trash me-2" style="color:#dc3545;"></i> Usage logs</li>
                                </ul>
                            </div>
                            <div class="col-md-6">
                                <h5 style="color:#ff511a;"><i class="fas fa-exclamation-triangle me-2"></i>Data We Keep</h5>
                                <ul class="list-unstyled">
                                    <li><i class="fas fa-archive me-2" style="color:#ff511a;"></i> Legal compliance records (as required)</li>
                                    <li><i class="fas fa-archive me-2" style="color:#ff511a;"></i> Anonymized analytics (no personal data)</li>
                                    <li><i class="fas fa-archive me-2" style="color:#ff511a;"></i> Backup data (deleted within 90 days)</li>
                                </ul>
                                <div class="sb-tip-box mt-3">
                                    <i class="fas fa-info-circle"></i>
                                    <div><small><strong>Note:</strong> Content already posted to social media platforms remains on those platforms according to their policies.</small></div>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">Timeline</h2>
                        <div class="sb-timeline">
                            <div class="sb-timeline-item">
                                <div class="sb-timeline-marker" style="background:#ff511a;"></div>
                                <div class="sb-timeline-content">
                                    <h6 class="mb-1">Immediate (0-1 hour)</h6>
                                    <p class="mb-0">Account access disabled, deletion process begins</p>
                                </div>
                            </div>
                            <div class="sb-timeline-item">
                                <div class="sb-timeline-marker" style="background:#ffc107;"></div>
                                <div class="sb-timeline-content">
                                    <h6 class="mb-1">24 Hours</h6>
                                    <p class="mb-0">Primary data deleted from active systems</p>
                                </div>
                            </div>
                            <div class="sb-timeline-item">
                                <div class="sb-timeline-marker" style="background:#43ba7f;"></div>
                                <div class="sb-timeline-content">
                                    <h6 class="mb-1">30 Days</h6>
                                    <p class="mb-0">All data permanently deleted, including backups</p>
                                </div>
                            </div>
                            <div class="sb-timeline-item">
                                <div class="sb-timeline-marker" style="background:#7b2e2e;"></div>
                                <div class="sb-timeline-content">
                                    <h6 class="mb-1">90 Days</h6>
                                    <p class="mb-0">Final archive purge (legal compliance records may remain longer if required)</p>
                                </div>
                            </div>
                        </div>
                    </section>

                    <section class="sb-docs-section mb-0">
                        <h2 class="sb-docs-h2">Need Help?</h2>
                        <p>If you have questions about data deletion or need assistance:</p>
                        <div class="row text-center">
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-envelope fa-2x mb-2" style="color:#ff511a;"></i>
                                <p class="mb-1"><strong>Email Support</strong></p>
                                <a href="mailto:privacy@shopybook.com" style="color:#ff511a;">privacy@shopybook.com</a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-life-ring fa-2x mb-2" style="color:#ff511a;"></i>
                                <p class="mb-1"><strong>Help Center</strong></p>
                                <a href="{{ route('help') }}" style="color:#ff511a;">Visit Help Center</a>
                            </div>
                            <div class="col-md-4 mb-3">
                                <i class="fas fa-comments fa-2x mb-2" style="color:#ff511a;"></i>
                                <p class="mb-1"><strong>Live Chat</strong></p>
                                <small class="text-muted">Available in dashboard</small>
                            </div>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
