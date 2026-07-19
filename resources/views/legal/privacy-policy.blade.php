@extends('layouts.master')

@section('title', 'Privacy Policy - Shopybook')

@section('content')
<div class="privacy-page" style="background: #f8f9fa; min-height: calc(100vh - 200px); padding-top: 80px;">
<div class="bg-primary text-white py-5 mb-4">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <h1 class="fw-bold mb-2"><i class="fas fa-shield-alt me-2"></i>Privacy Policy</h1>
                <p class="mb-0 opacity-75">Last updated: {{ date('F d, Y') }}</p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    <div class="row">
        {{-- Table of Contents --}}
        <div class="col-lg-3 d-none d-lg-block">
            <div class="position-sticky" style="top: 1rem;">
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h6 class="text-uppercase text-muted fw-bold mb-3 small">Contents</h6>
                        <nav class="nav flex-column">
                            <a href="#section-1" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">1. Information We Collect</a>
                            <a href="#section-2" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">2. How We Use Information</a>
                            <a href="#section-3" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">3. Social Media Integration</a>
                            <a href="#section-4" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">4. Data Sharing</a>
                            <a href="#section-5" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">5. Data Security</a>
                            <a href="#section-6" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">6. Your Rights</a>
                            <a href="#section-7" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">7. Kenya DPA Compliance</a>
                            <a href="#section-8" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">8. Data Retention</a>
                            <a href="#section-9" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">9. International Transfers</a>
                            <a href="#section-10" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">10. Children's Privacy</a>
                            <a href="#section-11" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">11. Policy Changes</a>
                            <a href="#section-12" class="nav-link py-1 px-2 small text-decoration-none text-dark rounded">12. Contact</a>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        {{-- Main Content --}}
        <div class="col-lg-9">
            <div class="card border-0 shadow-sm">
                <div class="card-body p-4 p-md-5">

                    <section id="section-1" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-info-circle text-primary me-2"></i>1. Information We Collect
                        </h2>

                        <h3 class="h6 fw-bold text-dark mt-4">1.1 Account Information</h3>
                        <p class="text-body">When you create a Shopybook account, we collect:</p>
                        <ul class="text-body">
                            <li>Name and email address</li>
                            <li>Business information (company name, type, address)</li>
                            <li>Phone number (optional)</li>
                            <li>Profile picture (optional)</li>
                        </ul>

                        <h3 class="h6 fw-bold text-dark mt-4">1.2 Social Media Account Information</h3>
                        <p class="text-body">When you connect social media accounts, we collect:</p>
                        <ul class="text-body">
                            <li>Social media usernames and profile information</li>
                            <li>Access tokens to post on your behalf</li>
                            <li>Public profile data from connected platforms</li>
                            <li>Post performance metrics and analytics</li>
                        </ul>

                        <h3 class="h6 fw-bold text-dark mt-4">1.3 Usage Information</h3>
                        <p class="text-body">We automatically collect:</p>
                        <ul class="text-body">
                            <li>Log data (IP address, browser type, operating system)</li>
                            <li>Device information</li>
                            <li>Usage patterns and feature interactions</li>
                            <li>Performance metrics</li>
                            <li>Page visits including route names, duration, and session identifiers</li>
                        </ul>
                        <p class="text-body">This data is used to monitor platform performance, identify popular and underutilized features, and improve user experience. For <strong>authenticated users</strong>, we track usage under legitimate interest to provide and improve our services. For <strong>anonymous visitors</strong>, page visit tracking is activated only upon your consent via our tracking consent banner.</p>

                        <h3 class="h6 fw-bold text-dark mt-4">1.4 AI-Powered Analytics</h3>
                        <p class="text-body">We use AI (Claude by Anthropic) to analyze aggregated, anonymized usage patterns to identify usability issues and improve the platform. Specifically:</p>
                        <ul class="text-body">
                            <li><strong>No personal data is sent to AI services.</strong> We only share aggregated statistics (total visits, page counts, error rates)</li>
                            <li><strong>No emails, names, IP addresses, or user IDs</strong> are included in AI analysis prompts</li>
                            <li>AI analysis is used solely to identify difficult-to-use pages and improve user experience</li>
                            <li>Data sent to Anthropic is subject to their <a href="https://www.anthropic.com/legal/privacy" target="_blank" rel="noopener" class="text-primary">privacy policy</a></li>
                        </ul>
                    </section>

                    <section id="section-2" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-cog text-primary me-2"></i>2. How We Use Your Information
                        </h2>
                        <p class="text-body">We use your information to:</p>
                        <ul class="text-body">
                            <li><strong>Provide Services:</strong> Enable social media posting, scheduling, and analytics</li>
                            <li><strong>Account Management:</strong> Create and maintain your account</li>
                            <li><strong>Communication:</strong> Send service updates, notifications, and support</li>
                            <li><strong>Improvement:</strong> Analyze usage to improve our platform</li>
                            <li><strong>Security:</strong> Protect against fraud and unauthorized access</li>
                            <li><strong>Compliance:</strong> Meet legal obligations</li>
                        </ul>
                    </section>

                    <section id="section-3" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fab fa-facebook text-primary me-2"></i>3. Social Media Integration
                        </h2>
                        <h3 class="h6 fw-bold text-dark mt-4">3.1 Data Access</h3>
                        <p class="text-body">When you connect social media accounts, we:</p>
                        <ul class="text-body">
                            <li>Only request permissions necessary for our services</li>
                            <li>Store access tokens securely</li>
                            <li>Never access private messages or personal data beyond what you authorize</li>
                            <li>Post only content you explicitly create and schedule</li>
                        </ul>

                        <h3 class="h6 fw-bold text-dark mt-4">3.2 Third-Party Platforms</h3>
                        <p class="text-body">We integrate with:</p>
                        <div class="d-flex flex-wrap gap-2 my-3">
                            <span class="badge bg-light text-dark border px-3 py-2">Facebook &amp; Instagram</span>
                            <span class="badge bg-light text-dark border px-3 py-2">Twitter/X</span>
                            <span class="badge bg-light text-dark border px-3 py-2">LinkedIn</span>
                            <span class="badge bg-light text-dark border px-3 py-2">TikTok</span>
                            <span class="badge bg-light text-dark border px-3 py-2">YouTube</span>
                            <span class="badge bg-light text-dark border px-3 py-2">Pinterest</span>
                            <span class="badge bg-light text-dark border px-3 py-2">Discord</span>
                            <span class="badge bg-light text-dark border px-3 py-2">Telegram</span>
                            <span class="badge bg-light text-dark border px-3 py-2">Reddit</span>
                            <span class="badge bg-light text-dark border px-3 py-2">WhatsApp Business</span>
                            <span class="badge bg-light text-dark border px-3 py-2">Snapchat</span>
                        </div>
                        <p class="text-body">Each platform has its own privacy policy that also applies to your data.</p>
                    </section>

                    <section id="section-4" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-share-alt text-primary me-2"></i>4. Data Sharing and Disclosure
                        </h2>
                        <div class="alert alert-warning border-0 rounded-3 py-2 px-3 mb-3">
                            <i class="fas fa-times-circle me-1"></i> We do <strong>NOT</strong> sell your personal information.
                        </div>
                        <p class="text-body">We may share data only in these situations:</p>
                        <ul class="text-body">
                            <li><strong>With Your Consent:</strong> When you explicitly authorize sharing</li>
                            <li><strong>Service Providers:</strong> Trusted partners who help operate our platform</li>
                            <li><strong>Legal Requirements:</strong> When required by law or legal process</li>
                            <li><strong>Business Transfers:</strong> In case of merger, acquisition, or sale</li>
                            <li><strong>Safety:</strong> To protect rights, safety, and security</li>
                        </ul>
                    </section>

                    <section id="section-5" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-lock text-primary me-2"></i>5. Data Security
                        </h2>
                        <p class="text-body">We protect your information through:</p>
                        <ul class="text-body">
                            <li>Encryption of data in transit and at rest</li>
                            <li>Secure access controls and authentication</li>
                            <li>Regular security audits and updates</li>
                            <li>Limited access to personal data by employees</li>
                            <li>Secure data centers and infrastructure</li>
                        </ul>
                    </section>

                    <section id="section-6" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-user-shield text-primary me-2"></i>6. Your Rights and Choices
                        </h2>
                        <p class="text-body">You have the right to:</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-eye text-primary me-2"></i><strong>Access:</strong> Request a copy of your personal data</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-edit text-primary me-2"></i><strong>Correct:</strong> Update or correct inaccurate information</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-trash text-primary me-2"></i><strong>Delete:</strong> Request deletion of your account and data</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-download text-primary me-2"></i><strong>Portability:</strong> Export your data in a common format</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-unlink text-primary me-2"></i><strong>Withdraw Consent:</strong> Disconnect social media accounts at any time</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-ban text-primary me-2"></i><strong>Opt-out of Tracking:</strong> Decline analytics tracking via the consent banner</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-robot text-primary me-2"></i><strong>Object to AI Processing:</strong> Opt out of AI-powered analytics</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="border rounded-3 p-3 h-100">
                                    <p class="mb-1 text-body"><i class="fas fa-envelope-open text-primary me-2"></i><strong>Opt-out:</strong> Unsubscribe from marketing communications</p>
                                </div>
                            </div>
                        </div>
                        <p class="text-body mt-3">To exercise these rights, contact us at <a href="mailto:privacy@shopybook.com" class="text-primary fw-bold">privacy@shopybook.com</a></p>
                    </section>

                    <section id="section-7" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-balance-scale text-primary me-2"></i>7. Kenya Data Protection Act (DPA) Compliance
                        </h2>
                        <div class="alert alert-info border-0 rounded-3 py-2 px-3 mb-3">
                            <i class="fas fa-info-circle me-1"></i> Shopybook complies with the <strong>Kenya Data Protection Act, 2019</strong>.
                        </div>
                        <ul class="text-body">
                            <li>We are registered as a data controller with the Office of the Data Protection Commissioner (ODPC)</li>
                            <li>We process personal data lawfully, fairly, and transparently</li>
                            <li>We collect data only for specified, explicit, and legitimate purposes</li>
                            <li>We minimize data collection to what is necessary for the stated purposes</li>
                            <li>We retain data only as long as necessary and delete it upon request</li>
                            <li>Cross-border data transfers (including to AI providers like Anthropic) use appropriate safeguards and anonymized data only</li>
                            <li>We have appointed a Data Protection Officer who can be reached at <strong>privacy@shopybook.com</strong></li>
                        </ul>
                    </section>

                    <section id="section-8" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>8. Data Retention
                        </h2>
                        <p class="text-body">We retain your information:</p>
                        <ul class="text-body">
                            <li>As long as your account is active</li>
                            <li>As needed to provide services</li>
                            <li>To comply with legal obligations</li>
                            <li>To resolve disputes and enforce agreements</li>
                        </ul>
                        <p class="text-body">You can request account deletion at any time, and we will delete your data within 30 days.</p>
                    </section>

                    <section id="section-9" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-globe text-primary me-2"></i>9. International Transfers
                        </h2>
                        <p class="text-body">Your information may be transferred to and processed in countries outside your residence. We ensure adequate protection through:</p>
                        <ul class="text-body">
                            <li>Standard contractual clauses</li>
                            <li>Adequacy decisions</li>
                            <li>Appropriate safeguards</li>
                        </ul>
                    </section>

                    <section id="section-10" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-child text-primary me-2"></i>10. Children's Privacy
                        </h2>
                        <p class="text-body">Shopybook is not intended for children under 13. We do not knowingly collect personal information from children under 13. If we become aware of such collection, we will delete the information immediately.</p>
                    </section>

                    <section id="section-11" class="mb-5">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-edit text-primary me-2"></i>11. Changes to This Policy
                        </h2>
                        <p class="text-body">We may update this privacy policy to reflect changes in our practices or applicable law. We will:</p>
                        <ul class="text-body">
                            <li>Post the updated policy on this page</li>
                            <li>Update the "Last updated" date</li>
                            <li>Notify you of material changes via email or in-app notification</li>
                        </ul>
                    </section>

                    <section id="section-12" class="mb-0">
                        <h2 class="h5 fw-bold text-dark border-bottom pb-2 mb-3">
                            <i class="fas fa-envelope text-primary me-2"></i>12. Contact Information
                        </h2>
                        <p class="text-body">For questions about this privacy policy or our data practices, contact us:</p>
                        <div class="bg-light rounded-3 p-4 border">
                            <p class="mb-2 text-body"><i class="fas fa-envelope me-2 text-primary"></i><strong>Email:</strong> <a href="mailto:privacy@shopybook.com" class="text-primary">privacy@shopybook.com</a></p>
                            <p class="mb-2 text-body"><i class="fas fa-map-marker-alt me-2 text-primary"></i><strong>Address:</strong> [Your Business Address]</p>
                            <p class="mb-0 text-body"><i class="fas fa-phone me-2 text-primary"></i><strong>Phone:</strong> [Your Phone Number]</p>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</div>
</div>

<style>
.privacy-page,
.privacy-page section,
.privacy-page .container,
.privacy-page .container-fluid {
    background: #f8f9fa !important;
    color: #212529 !important;
}
.privacy-page .card {
    background: #ffffff !important;
}
.privacy-page .card-body {
    color: #212529 !important;
}
.privacy-page .nav-link:hover {
    background-color: #e9ecef !important;
}
.privacy-content section, [id^="section-"] {
    scroll-margin-top: 2rem;
}
</style>
@endsection
