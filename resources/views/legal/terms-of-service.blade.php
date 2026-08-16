@extends('layouts.public')

@section('title', 'Terms of Service - Shopybook')

@section('content')

{{-- ═══════════════ HERO ═══════════════ --}}
<section class="hero-section text-light">
    <div class="container text-center">
        <h1>Terms of Service</h1>
        <p class="mb-0">Last updated: {{ date('F d, Y') }}</p>
    </div>
</section>

{{-- ═══════════════ CONTENT ═══════════════ --}}
<section class="sb-section sb-section-gray">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="sb-docs-content">

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">1. Acceptance of Terms</h2>
                        <p>By accessing or using Shopybook ("Service"), you agree to be bound by these Terms of Service ("Terms"). If you disagree with any part of these terms, you may not access the Service.</p>
                        <p>These Terms apply to all visitors, users, and others who access or use the Service.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">2. Description of Service</h2>
                        <p>Shopybook is a social media management platform that enables businesses to:</p>
                        <ul>
                            <li>Connect and manage multiple social media accounts</li>
                            <li>Schedule and publish content across platforms</li>
                            <li>Track analytics and engagement metrics</li>
                            <li>Manage customer interactions and responses</li>
                            <li>Automate social media marketing workflows</li>
                        </ul>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">3. User Accounts</h2>
                        <h4 class="mt-3">3.1 Account Creation</h4>
                        <p>To use certain features, you must create an account. You agree to:</p>
                        <ul>
                            <li>Provide accurate, current, and complete information</li>
                            <li>Maintain and update your account information</li>
                            <li>Keep your password secure and confidential</li>
                            <li>Accept responsibility for all activities under your account</li>
                            <li>Notify us immediately of any unauthorized use</li>
                        </ul>
                        <h4 class="mt-3">3.2 Account Eligibility</h4>
                        <p>You must be at least 18 years old and legally able to enter into contracts to use this Service.</p>
                        <h4 class="mt-3">3.3 Business Accounts</h4>
                        <p>If using the Service on behalf of a business, you represent that you have authority to bind that entity to these Terms.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">4. Acceptable Use</h2>
                        <h4 class="mt-3">4.1 Permitted Use</h4>
                        <p>You may use the Service for legitimate business purposes in compliance with all applicable laws and regulations.</p>
                        <h4 class="mt-3">4.2 Prohibited Activities</h4>
                        <p>You agree NOT to:</p>
                        <ul>
                            <li>Violate any applicable laws or regulations</li>
                            <li>Post spam, harassment, or inappropriate content</li>
                            <li>Infringe on intellectual property rights</li>
                            <li>Transmit malware, viruses, or harmful code</li>
                            <li>Attempt to gain unauthorized access to our systems</li>
                            <li>Use the Service for illegal activities</li>
                            <li>Reverse engineer or copy our software</li>
                            <li>Violate social media platform terms of service</li>
                            <li>Share account credentials with unauthorized parties</li>
                            <li>Use automated systems to abuse the Service</li>
                        </ul>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">5. Social Media Integration</h2>
                        <h4 class="mt-3">5.1 Third-Party Platforms</h4>
                        <p>Our Service integrates with third-party social media platforms. You acknowledge that:</p>
                        <ul>
                            <li>Each platform has its own terms of service</li>
                            <li>We are not responsible for third-party platform changes</li>
                            <li>Platform access may be limited or terminated by third parties</li>
                            <li>You must comply with each platform's terms and policies</li>
                        </ul>
                        <h4 class="mt-3">5.2 Content Responsibility</h4>
                        <p>You are solely responsible for:</p>
                        <ul>
                            <li>All content you create and publish</li>
                            <li>Ensuring content complies with platform guidelines</li>
                            <li>Obtaining necessary rights for any media used</li>
                            <li>Monitoring and responding to audience interactions</li>
                        </ul>
                        <h4 class="mt-3">5.3 Data and Analytics</h4>
                        <p>Analytics and data provided are for informational purposes only. We make no guarantees about accuracy or completeness.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">6. Intellectual Property</h2>
                        <h4 class="mt-3">6.1 Our IP</h4>
                        <p>The Service, including software, text, graphics, logos, and trademarks, is owned by Shopybook and protected by intellectual property laws.</p>
                        <h4 class="mt-3">6.2 Your Content</h4>
                        <p>You retain ownership of content you create. By using the Service, you grant us a limited license to:</p>
                        <ul>
                            <li>Store, process, and transmit your content</li>
                            <li>Publish content to connected social media platforms</li>
                            <li>Analyze content for service improvement (anonymized)</li>
                        </ul>
                        <h4 class="mt-3">6.3 Feedback</h4>
                        <p>Any feedback or suggestions you provide may be used by us without compensation or attribution.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">7. Payment and Billing</h2>
                        <h4 class="mt-3">7.1 Subscription Plans</h4>
                        <p>Paid features require a subscription. By subscribing, you agree to:</p>
                        <ul>
                            <li>Pay all applicable fees</li>
                            <li>Provide accurate billing information</li>
                            <li>Authorize recurring charges</li>
                            <li>Update payment information as needed</li>
                        </ul>
                        <h4 class="mt-3">7.2 Refunds</h4>
                        <p>Refunds are provided according to our refund policy. Generally, we offer:</p>
                        <ul>
                            <li>30-day money-back guarantee for new subscribers</li>
                            <li>Pro-rated refunds for annual plans (case-by-case)</li>
                            <li>No refunds for partial months</li>
                        </ul>
                        <h4 class="mt-3">7.3 Plan Changes</h4>
                        <p>You may upgrade or downgrade your plan at any time. Changes take effect at the next billing cycle.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">8. Privacy and Data</h2>
                        <p>Your privacy is important to us. Our collection and use of personal information is governed by our <a href="{{ route('privacy-policy') }}" style="color:#ff511a;">Privacy Policy</a>, which is incorporated into these Terms by reference.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">9. Service Availability</h2>
                        <h4 class="mt-3">9.1 Uptime</h4>
                        <p>We strive for high availability but do not guarantee uninterrupted service. The Service may be unavailable due to:</p>
                        <ul>
                            <li>Scheduled maintenance</li>
                            <li>Technical difficulties</li>
                            <li>Third-party platform issues</li>
                            <li>Force majeure events</li>
                        </ul>
                        <h4 class="mt-3">9.2 Support</h4>
                        <p>We provide customer support during business hours. Response times vary by subscription plan.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">10. Disclaimers and Limitation of Liability</h2>
                        <h4 class="mt-3">10.1 Disclaimers</h4>
                        <p>THE SERVICE IS PROVIDED "AS IS" AND "AS AVAILABLE" WITHOUT WARRANTIES OF ANY KIND. WE DISCLAIM ALL WARRANTIES, EXPRESS OR IMPLIED.</p>
                        <h4 class="mt-3">10.2 Limitation of Liability</h4>
                        <p>IN NO EVENT SHALL SHOPYBOOK BE LIABLE FOR ANY INDIRECT, INCIDENTAL, SPECIAL, OR CONSEQUENTIAL DAMAGES. OUR TOTAL LIABILITY SHALL NOT EXCEED THE AMOUNT PAID BY YOU IN THE 12 MONTHS PRECEDING THE CLAIM.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">11. Termination</h2>
                        <h4 class="mt-3">11.1 Termination by You</h4>
                        <p>You may terminate your account at any time by contacting us or using account settings.</p>
                        <h4 class="mt-3">11.2 Termination by Us</h4>
                        <p>We may terminate or suspend accounts for:</p>
                        <ul>
                            <li>Violation of these Terms</li>
                            <li>Non-payment of fees</li>
                            <li>Illegal or harmful activities</li>
                            <li>At our discretion with notice</li>
                        </ul>
                        <h4 class="mt-3">11.3 Effect of Termination</h4>
                        <p>Upon termination:</p>
                        <ul>
                            <li>Your access to the Service ends immediately</li>
                            <li>You may download your data for 30 days</li>
                            <li>We may delete your data after the retention period</li>
                            <li>Outstanding fees remain due</li>
                        </ul>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">12. Changes to Terms</h2>
                        <p>We may modify these Terms at any time. Changes will be effective:</p>
                        <ul>
                            <li>Immediately upon posting for non-material changes</li>
                            <li>30 days after notice for material changes</li>
                        </ul>
                        <p>Continued use of the Service constitutes acceptance of modified Terms.</p>
                    </section>

                    <section class="sb-docs-section">
                        <h2 class="sb-docs-h2">13. General Provisions</h2>
                        <h4 class="mt-3">13.1 Governing Law</h4>
                        <p>These Terms are governed by the laws of the Republic of Kenya without regard to conflict of law principles.</p>
                        <h4 class="mt-3">13.2 Dispute Resolution</h4>
                        <p>Disputes will be resolved through binding arbitration in accordance with the rules of the Nairobi Centre for International Arbitration.</p>
                        <h4 class="mt-3">13.3 Severability</h4>
                        <p>If any provision is found unenforceable, the remaining provisions continue in full force.</p>
                        <h4 class="mt-3">13.4 Entire Agreement</h4>
                        <p>These Terms constitute the entire agreement between you and Shopybook regarding the Service.</p>
                    </section>

                    <section class="sb-docs-section mb-0">
                        <h2 class="sb-docs-h2">14. Contact Information</h2>
                        <p>For questions about these Terms, contact us:</p>
                        <div class="sb-step-card">
                            <p class="mb-1"><strong>Email:</strong> <a href="mailto:legal@shopybook.com" style="color:#ff511a;">legal@shopybook.com</a></p>
                            <p class="mb-1"><strong>Address:</strong> Nairobi, Kenya</p>
                            <p class="mb-0"><strong>Phone:</strong> 0717 745 891</p>
                        </div>
                    </section>

                </div>
            </div>
        </div>
    </div>
</section>

@endsection
