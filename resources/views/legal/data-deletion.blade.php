@extends('layouts.master')

@section('title', 'Data Deletion Instructions - Shopybook')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Data Deletion Instructions</h1>
                    <p class="text-muted text-center mb-5">How to request deletion of your data from Shopybook</p>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Note:</strong> This page explains how to delete your data from Shopybook. For social media platform data, you must follow each platform's own deletion process.
                    </div>

                    <div class="deletion-content">
                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">Account Deletion Options</h2>
                            
                            <div class="row">
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-primary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-user-minus fa-3x text-primary mb-3"></i>
                                            <h5 class="card-title">Self-Service Deletion</h5>
                                            <p class="card-text">Delete your account directly from your dashboard</p>
                                            <a href="{{ route('dashboard') }}" class="btn btn-primary">Go to Dashboard</a>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-6 mb-4">
                                    <div class="card h-100 border-secondary">
                                        <div class="card-body text-center">
                                            <i class="fas fa-envelope fa-3x text-secondary mb-3"></i>
                                            <h5 class="card-title">Email Request</h5>
                                            <p class="card-text">Send us an email to request account deletion</p>
                                            <a href="mailto:privacy@shopybook.com?subject=Account Deletion Request" class="btn btn-secondary">Send Email</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">Step-by-Step Instructions</h2>
                            
                            <div class="accordion" id="deletionAccordion">
                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="method1">
                                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapse1">
                                            Method 1: Dashboard Deletion (Recommended)
                                        </button>
                                    </h2>
                                    <div id="collapse1" class="accordion-collapse collapse show" data-bs-parent="#deletionAccordion">
                                        <div class="accordion-body">
                                            <ol>
                                                <li><strong>Log in</strong> to your Shopybook account</li>
                                                <li><strong>Go to Settings</strong> → Account Settings</li>
                                                <li><strong>Scroll down</strong> to the "Danger Zone" section</li>
                                                <li><strong>Click "Delete Account"</strong> button</li>
                                                <li><strong>Confirm deletion</strong> by typing your password</li>
                                                <li><strong>Check your email</strong> for confirmation</li>
                                            </ol>
                                            <div class="alert alert-warning mt-3">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                <strong>Warning:</strong> This action is irreversible. All your data will be permanently deleted.
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="method2">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse2">
                                            Method 2: Email Request
                                        </button>
                                    </h2>
                                    <div id="collapse2" class="accordion-collapse collapse" data-bs-parent="#deletionAccordion">
                                        <div class="accordion-body">
                                            <p><strong>Send an email to:</strong> <a href="mailto:privacy@shopybook.com">privacy@shopybook.com</a></p>
                                            
                                            <p><strong>Include the following information:</strong></p>
                                            <ul>
                                                <li>Subject: "Account Deletion Request"</li>
                                                <li>Your registered email address</li>
                                                <li>Your full name</li>
                                                <li>Reason for deletion (optional)</li>
                                                <li>Confirmation that you understand data cannot be recovered</li>
                                            </ul>

                                            <div class="bg-light p-3 rounded">
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

                                <div class="accordion-item">
                                    <h2 class="accordion-header" id="method3">
                                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse3">
                                            Method 3: Facebook App Data Deletion
                                        </button>
                                    </h2>
                                    <div id="collapse3" class="accordion-collapse collapse" data-bs-parent="#deletionAccordion">
                                        <div class="accordion-body">
                                            <p>If you connected your Facebook account and want to delete only Facebook-related data:</p>
                                            <ol>
                                                <li><strong>Go to your Facebook Settings</strong> → Apps and Websites</li>
                                                <li><strong>Find "Shopybook"</strong> in your active apps</li>
                                                <li><strong>Click "Remove"</strong> or "Revoke Access"</li>
                                                <li><strong>Your Facebook data</strong> will be automatically deleted from our systems within 24 hours</li>
                                            </ol>
                                            
                                            <p><strong>Alternative:</strong> In your Shopybook dashboard, go to Social Media → Connected Accounts and click "Disconnect" next to Facebook.</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">What Gets Deleted</h2>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <h5 class="text-success"><i class="fas fa-check-circle me-2"></i>Data We Delete</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-trash text-danger me-2"></i> Account information</li>
                                        <li><i class="fas fa-trash text-danger me-2"></i> Social media tokens</li>
                                        <li><i class="fas fa-trash text-danger me-2"></i> Posted content drafts</li>
                                        <li><i class="fas fa-trash text-danger me-2"></i> Analytics data</li>
                                        <li><i class="fas fa-trash text-danger me-2"></i> Billing information</li>
                                        <li><i class="fas fa-trash text-danger me-2"></i> Support conversations</li>
                                        <li><i class="fas fa-trash text-danger me-2"></i> Usage logs</li>
                                    </ul>
                                </div>
                                
                                <div class="col-md-6">
                                    <h5 class="text-warning"><i class="fas fa-exclamation-triangle me-2"></i>Data We Keep</h5>
                                    <ul class="list-unstyled">
                                        <li><i class="fas fa-archive text-warning me-2"></i> Legal compliance records (as required)</li>
                                        <li><i class="fas fa-archive text-warning me-2"></i> Anonymized analytics (no personal data)</li>
                                        <li><i class="fas fa-archive text-warning me-2"></i> Backup data (deleted within 90 days)</li>
                                    </ul>
                                    
                                    <div class="alert alert-info mt-3">
                                        <small><strong>Note:</strong> Content already posted to social media platforms remains on those platforms according to their policies.</small>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">Timeline</h2>
                            
                            <div class="timeline">
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-primary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">Immediate (0-1 hour)</h6>
                                        <p class="mb-0">Account access disabled, deletion process begins</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-warning"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">24 Hours</h6>
                                        <p class="mb-0">Primary data deleted from active systems</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-success"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">30 Days</h6>
                                        <p class="mb-0">All data permanently deleted, including backups</p>
                                    </div>
                                </div>
                                
                                <div class="timeline-item">
                                    <div class="timeline-marker bg-secondary"></div>
                                    <div class="timeline-content">
                                        <h6 class="mb-1">90 Days</h6>
                                        <p class="mb-0">Final archive purge (legal compliance records may remain longer if required)</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        <section class="mb-4">
                            <h2 class="h4 text-primary mb-3">Need Help?</h2>
                            <p>If you have questions about data deletion or need assistance:</p>
                            
                            <div class="row">
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-envelope fa-2x text-primary mb-2"></i>
                                    <p class="mb-1"><strong>Email Support</strong></p>
                                    <a href="mailto:privacy@shopybook.com">privacy@shopybook.com</a>
                                </div>
                                
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-life-ring fa-2x text-primary mb-2"></i>
                                    <p class="mb-1"><strong>Help Center</strong></p>
                                    <a href="{{ route('help') }}">Visit Help Center</a>
                                </div>
                                
                                <div class="col-md-4 text-center mb-3">
                                    <i class="fas fa-comments fa-2x text-primary mb-2"></i>
                                    <p class="mb-1"><strong>Live Chat</strong></p>
                                    <small class="text-muted">Available in dashboard</small>
                                </div>
                            </div>
                        </section>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}

.timeline::before {
    content: '';
    position: absolute;
    left: 10px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #dee2e6;
}

.timeline-item {
    position: relative;
    margin-bottom: 20px;
}

.timeline-marker {
    position: absolute;
    left: -20px;
    top: 5px;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    border: 3px solid white;
    box-shadow: 0 0 0 3px #dee2e6;
}

.timeline-content {
    margin-left: 20px;
}
</style>
@endsection
