@extends('layouts.master')

@section('title', 'Privacy Policy - Shopybook')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Privacy Policy</h1>
                    <p class="text-muted text-center mb-5">Last updated: {{ date('F d, Y') }}</p>
                    
                    <div class="privacy-content">
                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">1. Information We Collect</h2>
                            <div class="ms-3">
                                <h3 class="h6 mt-4">1.1 Account Information</h3>
                                <p>When you create a Shopybook account, we collect:</p>
                                <ul>
                                    <li>Name and email address</li>
                                    <li>Business information (company name, type, address)</li>
                                    <li>Phone number (optional)</li>
                                    <li>Profile picture (optional)</li>
                                </ul>

                                <h3 class="h6 mt-4">1.2 Social Media Account Information</h3>
                                <p>When you connect social media accounts, we collect:</p>
                                <ul>
                                    <li>Social media usernames and profile information</li>
                                    <li>Access tokens to post on your behalf</li>
                                    <li>Public profile data from connected platforms</li>
                                    <li>Post performance metrics and analytics</li>
                                </ul>

                                <h3 class="h6 mt-4">1.3 Usage Information</h3>
                                <p>We automatically collect:</p>
                                <ul>
                                    <li>Log data (IP address, browser type, operating system)</li>
                                    <li>Device information</li>
                                    <li>Usage patterns and feature interactions</li>
                                    <li>Performance metrics</li>
                                    <li>Page visits including route names, duration, and session identifiers</li>
                                </ul>
                                <p>This data is used to monitor platform performance, identify popular and underutilized features, and improve user experience. Page visit tracking is activated upon your consent via our tracking consent banner.</p>

                                <h3 class="h6 mt-4">1.4 AI-Powered Analytics</h3>
                                <p>We use AI (Claude by Anthropic) to analyze aggregated, anonymized usage patterns to identify usability issues and improve the platform. Specifically:</p>
                                <ul>
                                    <li><strong>No personal data is sent to AI services.</strong> We only share aggregated statistics (total visits, page counts, error rates)</li>
                                    <li><strong>No emails, names, IP addresses, or user IDs</strong> are included in AI analysis prompts</li>
                                    <li>AI analysis is used solely to identify difficult-to-use pages and improve user experience</li>
                                    <li>Data sent to Anthropic is subject to their <a href="https://www.anthropic.com/legal/privacy" target="_blank" rel="noopener">privacy policy</a></li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">2. How We Use Your Information</h2>
                            <div class="ms-3">
                                <p>We use your information to:</p>
                                <ul>
                                    <li><strong>Provide Services:</strong> Enable social media posting, scheduling, and analytics</li>
                                    <li><strong>Account Management:</strong> Create and maintain your account</li>
                                    <li><strong>Communication:</strong> Send service updates, notifications, and support</li>
                                    <li><strong>Improvement:</strong> Analyze usage to improve our platform</li>
                                    <li><strong>Security:</strong> Protect against fraud and unauthorized access</li>
                                    <li><strong>Compliance:</strong> Meet legal obligations</li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">3. Social Media Integration</h2>
                            <div class="ms-3">
                                <h3 class="h6 mt-4">3.1 Data Access</h3>
                                <p>When you connect social media accounts, we:</p>
                                <ul>
                                    <li>Only request permissions necessary for our services</li>
                                    <li>Store access tokens securely</li>
                                    <li>Never access private messages or personal data beyond what you authorize</li>
                                    <li>Post only content you explicitly create and schedule</li>
                                </ul>

                                <h3 class="h6 mt-4">3.2 Third-Party Platforms</h3>
                                <p>We integrate with:</p>
                                <ul>
                                    <li>Facebook and Instagram (Meta)</li>
                                    <li>Twitter/X</li>
                                    <li>LinkedIn</li>
                                    <li>TikTok, YouTube, Pinterest</li>
                                    <li>Discord, Telegram, Reddit</li>
                                    <li>WhatsApp Business, Snapchat</li>
                                </ul>
                                <p>Each platform has its own privacy policy that also applies to your data.</p>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">4. Data Sharing and Disclosure</h2>
                            <div class="ms-3">
                                <p>We do NOT sell your personal information. We may share data only in these situations:</p>
                                <ul>
                                    <li><strong>With Your Consent:</strong> When you explicitly authorize sharing</li>
                                    <li><strong>Service Providers:</strong> Trusted partners who help operate our platform</li>
                                    <li><strong>Legal Requirements:</strong> When required by law or legal process</li>
                                    <li><strong>Business Transfers:</strong> In case of merger, acquisition, or sale</li>
                                    <li><strong>Safety:</strong> To protect rights, safety, and security</li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">5. Data Security</h2>
                            <div class="ms-3">
                                <p>We protect your information through:</p>
                                <ul>
                                    <li>Encryption of data in transit and at rest</li>
                                    <li>Secure access controls and authentication</li>
                                    <li>Regular security audits and updates</li>
                                    <li>Limited access to personal data by employees</li>
                                    <li>Secure data centers and infrastructure</li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">6. Your Rights and Choices</h2>
                            <div class="ms-3">
                                <p>You have the right to:</p>
                                <ul>
                                    <li><strong>Access:</strong> Request a copy of your personal data</li>
                                    <li><strong>Correct:</strong> Update or correct inaccurate information</li>
                                    <li><strong>Delete:</strong> Request deletion of your account and data</li>
                                    <li><strong>Portability:</strong> Export your data in a common format</li>
                                    <li><strong>Withdraw Consent:</strong> Disconnect social media accounts at any time</li>
                                    <li><strong>Opt-out of Tracking:</strong> Decline or withdraw consent for usage analytics tracking at any time via the consent banner</li>
                                    <li><strong>Object to AI Processing:</strong> Request that your aggregated data not be included in AI-powered analytics</li>
                                    <li><strong>Opt-out:</strong> Unsubscribe from marketing communications</li>
                                </ul>
                                <p>To exercise these rights, contact us at <strong>privacy@shopybook.com</strong></p>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">7. Kenya Data Protection Act (DPA) Compliance</h2>
                            <div class="ms-3">
                                <p>Shopybook complies with the Kenya Data Protection Act, 2019. Specifically:</p>
                                <ul>
                                    <li>We are registered as a data controller with the Office of the Data Protection Commissioner (ODPC)</li>
                                    <li>We process personal data lawfully, fairly, and transparently</li>
                                    <li>We collect data only for specified, explicit, and legitimate purposes</li>
                                    <li>We minimize data collection to what is necessary for the stated purposes</li>
                                    <li>We retain data only as long as necessary and delete it upon request</li>
                                    <li>Cross-border data transfers (including to AI providers like Anthropic) use appropriate safeguards and anonymized data only</li>
                                    <li>We have appointed a Data Protection Officer who can be reached at <strong>privacy@shopybook.com</strong></li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">8. Data Retention</h2>
                            <div class="ms-3">
                                <p>We retain your information:</p>
                                <ul>
                                    <li>As long as your account is active</li>
                                    <li>As needed to provide services</li>
                                    <li>To comply with legal obligations</li>
                                    <li>To resolve disputes and enforce agreements</li>
                                </ul>
                                <p>You can request account deletion at any time, and we will delete your data within 30 days.</p>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">9. International Transfers</h2>
                            <div class="ms-3">
                                <p>Your information may be transferred to and processed in countries outside your residence. We ensure adequate protection through:</p>
                                <ul>
                                    <li>Standard contractual clauses</li>
                                    <li>Adequacy decisions</li>
                                    <li>Appropriate safeguards</li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">10. Children's Privacy</h2>
                            <div class="ms-3">
                                <p>Shopybook is not intended for children under 13. We do not knowingly collect personal information from children under 13. If we become aware of such collection, we will delete the information immediately.</p>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">11. Changes to This Policy</h2>
                            <div class="ms-3">
                                <p>We may update this privacy policy to reflect changes in our practices or applicable law. We will:</p>
                                <ul>
                                    <li>Post the updated policy on this page</li>
                                    <li>Update the "Last updated" date</li>
                                    <li>Notify you of material changes via email or in-app notification</li>
                                </ul>
                            </div>
                        </section>

                        <section class="mb-5">
                            <h2 class="h4 text-primary mb-3">12. Contact Information</h2>
                            <div class="ms-3">
                                <p>For questions about this privacy policy or our data practices, contact us:</p>
                                <div class="bg-light p-3 rounded">
                                    <p class="mb-1"><strong>Email:</strong> privacy@shopybook.com</p>
                                    <p class="mb-1"><strong>Address:</strong> [Your Business Address]</p>
                                    <p class="mb-0"><strong>Phone:</strong> [Your Phone Number]</p>
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
.privacy-content h2 {
    border-bottom: 2px solid #e9ecef;
    padding-bottom: 0.5rem;
}
.privacy-content section {
    scroll-margin-top: 2rem;
}
</style>
@endsection
