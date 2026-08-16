@extends('layouts.public')

@section('title', 'Privacy Policy - Shopybook')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <h1><i class="fas fa-shield-alt me-2"></i>Privacy Policy</h1>
        <p class="mb-0">Last updated: {{ date('F d, Y') }}</p>
    </div>
</section>

{{-- ═══════════════ CONTENT ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row">

            {{-- Table of Contents --}}
            <div class="col-lg-3 d-none d-lg-block">
                <div class="sb-docs-sidebar">
                    <h5 class="mb-3">Contents</h5>
                    <nav>
                        <a href="#section-1" class="sb-docs-nav">1. Information We Collect</a>
                        <a href="#section-2" class="sb-docs-nav">2. How We Use Information</a>
                        <a href="#section-3" class="sb-docs-nav">3. Social Media Integration</a>
                        <a href="#section-4" class="sb-docs-nav">4. Data Sharing</a>
                        <a href="#section-5" class="sb-docs-nav">5. Data Security</a>
                        <a href="#section-6" class="sb-docs-nav">6. Your Rights</a>
                        <a href="#section-7" class="sb-docs-nav">7. Kenya DPA Compliance</a>
                        <a href="#section-8" class="sb-docs-nav">8. Data Retention</a>
                        <a href="#section-9" class="sb-docs-nav">9. International Transfers</a>
                        <a href="#section-10" class="sb-docs-nav">10. Children's Privacy</a>
                        <a href="#section-11" class="sb-docs-nav">11. Policy Changes</a>
                        <a href="#section-12" class="sb-docs-nav">12. Contact</a>
                    </nav>
                </div>
            </div>

            {{-- Main Content --}}
            <div class="col-lg-9">
                <div class="sb-docs-content">

                    <section id="section-1" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-info-circle me-2"></i>1. Information We Collect</h2>

                        <h4 class="mt-4">1.1 Account Information</h4>
                        <p>When you create a Shopybook account, we collect:</p>
                        <ul>
                            <li>Name and email address</li>
                            <li>Business information (company name, type, address)</li>
                            <li>Phone number (optional)</li>
                            <li>Profile picture (optional)</li>
                        </ul>

                        <h4 class="mt-4">1.2 Social Media Account Information</h4>
                        <p>When you connect social media accounts, we collect:</p>
                        <ul>
                            <li>Social media usernames and profile information</li>
                            <li>Access tokens to post on your behalf</li>
                            <li>Public profile data from connected platforms</li>
                            <li>Post performance metrics and analytics</li>
                        </ul>

                        <h4 class="mt-4">1.3 Usage Information</h4>
                        <p>We automatically collect:</p>
                        <ul>
                            <li>Log data (IP address, browser type, operating system)</li>
                            <li>Device information</li>
                            <li>Usage patterns and feature interactions</li>
                            <li>Performance metrics</li>
                            <li>Page visits including route names, duration, and session identifiers</li>
                        </ul>
                        <p>This data is used to monitor platform performance, identify popular and underutilized features, and improve user experience. For <strong>authenticated users</strong>, we track usage under legitimate interest to provide and improve our services. For <strong>anonymous visitors</strong>, page visit tracking is activated only upon your consent via our tracking consent banner.</p>

                        <h4 class="mt-4">1.4 AI-Powered Analytics</h4>
                        <p>We use AI (Claude by Anthropic) to analyze aggregated, anonymized usage patterns to identify usability issues and improve the platform. Specifically:</p>
                        <ul>
                            <li><strong>No personal data is sent to AI services.</strong> We only share aggregated statistics (total visits, page counts, error rates)</li>
                            <li><strong>No emails, names, IP addresses, or user IDs</strong> are included in AI analysis prompts</li>
                            <li>AI analysis is used solely to identify difficult-to-use pages and improve user experience</li>
                            <li>Data sent to Anthropic is subject to their <a href="https://www.anthropic.com/legal/privacy" target="_blank" rel="noopener" style="color:#ff511a;">privacy policy</a></li>
                        </ul>
                    </section>

                    <section id="section-2" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-cog me-2"></i>2. How We Use Your Information</h2>
                        <p>We use your information to:</p>
                        <ul>
                            <li><strong>Provide Services:</strong> Enable social media posting, scheduling, and analytics</li>
                            <li><strong>Account Management:</strong> Create and maintain your account</li>
                            <li><strong>Communication:</strong> Send service updates, notifications, and support</li>
                            <li><strong>Improvement:</strong> Analyze usage to improve our platform</li>
                            <li><strong>Security:</strong> Protect against fraud and unauthorized access</li>
                            <li><strong>Compliance:</strong> Meet legal obligations</li>
                        </ul>
                    </section>

                    <section id="section-3" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fab fa-facebook me-2"></i>3. Social Media Integration</h2>
                        <h4 class="mt-4">3.1 Data Access</h4>
                        <p>When you connect social media accounts, we:</p>
                        <ul>
                            <li>Only request permissions necessary for our services</li>
                            <li>Store access tokens securely</li>
                            <li>Never access private messages or personal data beyond what you authorize</li>
                            <li>Post only content you explicitly create and schedule</li>
                        </ul>

                        <h4 class="mt-4">3.2 Third-Party Platforms</h4>
                        <p>We integrate with:</p>
                        <div class="d-flex flex-wrap gap-2 my-3">
                            <span class="sb-feature-badge">Facebook &amp; Instagram</span>
                            <span class="sb-feature-badge">Twitter/X</span>
                            <span class="sb-feature-badge">LinkedIn</span>
                            <span class="sb-feature-badge">TikTok</span>
                            <span class="sb-feature-badge">YouTube</span>
                            <span class="sb-feature-badge">Pinterest</span>
                            <span class="sb-feature-badge">Discord</span>
                            <span class="sb-feature-badge">Telegram</span>
                            <span class="sb-feature-badge">Reddit</span>
                            <span class="sb-feature-badge">WhatsApp Business</span>
                            <span class="sb-feature-badge">Snapchat</span>
                        </div>
                        <p>Each platform has its own privacy policy that also applies to your data.</p>
                    </section>

                    <section id="section-4" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-share-alt me-2"></i>4. Data Sharing and Disclosure</h2>
                        <div class="sb-tip-box" style="background:rgba(220,53,69,0.08); border-left-color:#dc3545;">
                            <i class="fas fa-times-circle" style="color:#dc3545;"></i>
                            <div>We do <strong>NOT</strong> sell your personal information.</div>
                        </div>
                        <p>We may share data only in these situations:</p>
                        <ul>
                            <li><strong>With Your Consent:</strong> When you explicitly authorize sharing</li>
                            <li><strong>Service Providers:</strong> Trusted partners who help operate our platform</li>
                            <li><strong>Legal Requirements:</strong> When required by law or legal process</li>
                            <li><strong>Business Transfers:</strong> In case of merger, acquisition, or sale</li>
                            <li><strong>Safety:</strong> To protect rights, safety, and security</li>
                        </ul>
                    </section>

                    <section id="section-5" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-lock me-2"></i>5. Data Security</h2>
                        <p>We protect your information through:</p>
                        <ul>
                            <li>Encryption of data in transit and at rest</li>
                            <li>Secure access controls and authentication</li>
                            <li>Regular security audits and updates</li>
                            <li>Limited access to personal data by employees</li>
                            <li>Secure data centers and infrastructure</li>
                        </ul>
                    </section>

                    <section id="section-6" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-user-shield me-2"></i>6. Your Rights and Choices</h2>
                        <p>You have the right to:</p>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-eye me-2" style="color:#ff511a;"></i><strong>Access:</strong> Request a copy of your personal data</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-edit me-2" style="color:#ff511a;"></i><strong>Correct:</strong> Update or correct inaccurate information</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-trash me-2" style="color:#ff511a;"></i><strong>Delete:</strong> Request deletion of your account and data</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-download me-2" style="color:#ff511a;"></i><strong>Portability:</strong> Export your data in a common format</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-unlink me-2" style="color:#ff511a;"></i><strong>Withdraw Consent:</strong> Disconnect social media accounts at any time</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-ban me-2" style="color:#ff511a;"></i><strong>Opt-out of Tracking:</strong> Decline analytics tracking via the consent banner</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-robot me-2" style="color:#ff511a;"></i><strong>Object to AI Processing:</strong> Opt out of AI-powered analytics</p></div>
                            </div>
                            <div class="col-md-6">
                                <div class="sb-step-card mb-2"><p class="mb-0"><i class="fas fa-envelope-open me-2" style="color:#ff511a;"></i><strong>Opt-out:</strong> Unsubscribe from marketing communications</p></div>
                            </div>
                        </div>
                        <p class="mt-3">To exercise these rights, contact us at <a href="mailto:privacy@shopybook.com" style="color:#ff511a; font-weight:600;">privacy@shopybook.com</a></p>
                    </section>

                    <section id="section-7" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-balance-scale me-2"></i>7. Kenya Data Protection Act (DPA) Compliance</h2>
                        <div class="sb-tip-box">
                            <i class="fas fa-info-circle"></i>
                            <div>Shopybook complies with the <strong>Kenya Data Protection Act, 2019</strong>.</div>
                        </div>
                        <ul>
                            <li>We are registered as a data controller with the Office of the Data Protection Commissioner (ODPC)</li>
                            <li>We process personal data lawfully, fairly, and transparently</li>
                            <li>We collect data only for specified, explicit, and legitimate purposes</li>
                            <li>We minimize data collection to what is necessary for the stated purposes</li>
                            <li>We retain data only as long as necessary and delete it upon request</li>
                            <li>Cross-border data transfers (including to AI providers like Anthropic) use appropriate safeguards and anonymized data only</li>
                            <li>We have appointed a Data Protection Officer who can be reached at <strong>privacy@shopybook.com</strong></li>
                        </ul>
                    </section>

                    <section id="section-8" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-clock me-2"></i>8. Data Retention</h2>
                        <p>We retain your information:</p>
                        <ul>
                            <li>As long as your account is active</li>
                            <li>As needed to provide services</li>
                            <li>To comply with legal obligations</li>
                            <li>To resolve disputes and enforce agreements</li>
                        </ul>
                        <p>You can request account deletion at any time, and we will delete your data within 30 days.</p>
                    </section>

                    <section id="section-9" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-globe me-2"></i>9. International Transfers</h2>
                        <p>Your information may be transferred to and processed in countries outside your residence. We ensure adequate protection through:</p>
                        <ul>
                            <li>Standard contractual clauses</li>
                            <li>Adequacy decisions</li>
                            <li>Appropriate safeguards</li>
                        </ul>
                    </section>

                    <section id="section-10" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-child me-2"></i>10. Children's Privacy</h2>
                        <p>Shopybook is not intended for children under 13. We do not knowingly collect personal information from children under 13. If we become aware of such collection, we will delete the information immediately.</p>
                    </section>

                    <section id="section-11" class="sb-docs-section">
                        <h2 class="sb-docs-h2"><i class="fas fa-edit me-2"></i>11. Changes to This Policy</h2>
                        <p>We may update this privacy policy to reflect changes in our practices or applicable law. We will:</p>
                        <ul>
                            <li>Post the updated policy on this page</li>
                            <li>Update the "Last updated" date</li>
                            <li>Notify you of material changes via email or in-app notification</li>
                        </ul>
                    </section>

                    <section id="section-12" class="sb-docs-section mb-0">
                        <h2 class="sb-docs-h2"><i class="fas fa-envelope me-2"></i>12. Contact Information</h2>
                        <p>For questions about this privacy policy or our data practices, contact us:</p>
                        <div class="sb-step-card">
                            <p class="mb-2"><i class="fas fa-envelope me-2" style="color:#ff511a;"></i><strong>Email:</strong> <a href="mailto:privacy@shopybook.com" style="color:#ff511a;">privacy@shopybook.com</a></p>
                            <p class="mb-2"><i class="fas fa-map-marker-alt me-2" style="color:#ff511a;"></i><strong>Address:</strong> Nairobi, Kenya</p>
                            <p class="mb-0"><i class="fas fa-phone me-2" style="color:#ff511a;"></i><strong>Phone:</strong> 0717 745 891</p>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
