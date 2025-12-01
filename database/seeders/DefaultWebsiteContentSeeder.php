<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DefaultWebsiteContentSeeder extends Seeder
{
    /**
     * Get default content templates for different business types
     */
    public static function getDefaultContent(string $category = 'business'): array
    {
        return match($category) {
            'business' => self::getBusinessContent(),
            'restaurant' => self::getRestaurantContent(),
            'store' => self::getStoreContent(),
            'portfolio' => self::getPortfolioContent(),
            'service' => self::getServiceContent(),
            default => self::getBusinessContent(),
        };
    }

    protected static function getBusinessContent(): array
    {
        return [
            'homepage' => [
                'hero' => [
                    'heading' => 'Transform Your Business Today',
                    'subheading' => 'Innovative solutions that drive real results for modern businesses',
                    'description' => 'We help businesses grow through cutting-edge technology and strategic expertise.',
                    'cta_primary' => 'Get Started',
                    'cta_secondary' => 'Learn More',
                    'image' => '/webbuilder/business-partners-shaking-hands-agreement.jpg',
                ],
                'features' => [
                    'heading' => 'Why Choose Us',
                    'subheading' => 'Everything you need to succeed',
                    'items' => [
                        [
                            'icon' => 'fa-rocket',
                            'title' => 'Fast & Reliable',
                            'description' => 'Lightning-fast performance that keeps your business running smoothly 24/7.',
                        ],
                        [
                            'icon' => 'fa-shield-alt',
                            'title' => 'Secure & Trusted',
                            'description' => 'Enterprise-grade security to protect your valuable business data.',
                        ],
                        [
                            'icon' => 'fa-users',
                            'title' => 'Expert Support',
                            'description' => 'Dedicated team ready to help you succeed at every step.',
                        ],
                        [
                            'icon' => 'fa-chart-line',
                            'title' => 'Proven Results',
                            'description' => 'Track record of helping businesses achieve their goals.',
                        ],
                    ],
                ],
                'services' => [
                    'heading' => 'Our Services',
                    'subheading' => 'Comprehensive solutions for your business',
                    'items' => [
                        [
                            'title' => 'Strategic Consulting',
                            'description' => 'Expert guidance to help you make informed business decisions and achieve sustainable growth.',
                            'image' => '/webbuilder/colleagues-reviewing-plans-tablet.jpg',
                            'features' => ['Market Analysis', 'Growth Strategy', 'Risk Assessment'],
                        ],
                        [
                            'title' => 'Digital Transformation',
                            'description' => 'Modernize your operations with cutting-edge technology solutions tailored to your needs.',
                            'image' => '/webbuilder/young-businessmen-standing-together-holding-laptop-discussing-business.jpg',
                            'features' => ['Cloud Migration', 'Process Automation', 'Digital Strategy'],
                        ],
                        [
                            'title' => 'Business Analytics',
                            'description' => 'Data-driven insights to optimize performance and uncover new opportunities.',
                            'image' => '/webbuilder/pexels-goumbik-669610.jpg',
                            'features' => ['Data Analysis', 'Performance Metrics', 'Custom Reports'],
                        ],
                    ],
                ],
                'stats' => [
                    'heading' => 'Our Impact',
                    'items' => [
                        ['number' => '500+', 'label' => 'Happy Clients'],
                        ['number' => '1M+', 'label' => 'Projects Completed'],
                        ['number' => '99%', 'label' => 'Client Satisfaction'],
                        ['number' => '24/7', 'label' => 'Support Available'],
                    ],
                ],
                'testimonials' => [
                    'heading' => 'What Our Clients Say',
                    'subheading' => 'Don\'t just take our word for it',
                    'items' => [
                        [
                            'name' => 'Sarah Johnson',
                            'role' => 'CEO, Tech Innovations',
                            'content' => 'Working with this team has transformed our business. Their expertise and dedication are unmatched.',
                            'rating' => 5,
                            'image' => '/images/defaults/testimonial-1.jpg',
                        ],
                        [
                            'name' => 'Michael Chen',
                            'role' => 'Founder, StartUp Hub',
                            'content' => 'Exceptional service and results that exceeded our expectations. Highly recommended!',
                            'rating' => 5,
                            'image' => '/images/defaults/testimonial-2.jpg',
                        ],
                        [
                            'name' => 'Emily Rodriguez',
                            'role' => 'Director, Growth Solutions',
                            'content' => 'The best investment we\'ve made for our company. The ROI speaks for itself.',
                            'rating' => 5,
                            'image' => '/images/defaults/testimonial-3.jpg',
                        ],
                    ],
                ],
                'cta' => [
                    'heading' => 'Ready to Get Started?',
                    'subheading' => 'Join thousands of successful businesses already using our services',
                    'button_text' => 'Start Your Free Trial',
                    'button_secondary' => 'Schedule a Demo',
                ],
            ],
            'about' => [
                'hero' => [
                    'heading' => 'About Our Company',
                    'subheading' => 'Building the future, one innovation at a time',
                ],
                'story' => [
                    'heading' => 'Our Story',
                    'content' => 'Founded with a vision to revolutionize the industry, we\'ve grown from a small startup to a trusted partner for businesses worldwide. Our journey has been driven by innovation, dedication, and an unwavering commitment to excellence.',
                    'image' => '/webbuilder/african-entrepreneur-start-up-company-reading-charts-documents-paperwork-diverse-team-business-people-analyzing-company-financial-reports-from-computer-successful-corporate-professional-en.jpg',
                ],
                'mission' => [
                    'heading' => 'Our Mission',
                    'content' => 'To empower businesses with innovative solutions that drive growth, efficiency, and success in an ever-evolving digital landscape.',
                ],
                'vision' => [
                    'heading' => 'Our Vision',
                    'content' => 'To be the global leader in business transformation, recognized for our innovation, integrity, and impact.',
                ],
                'values' => [
                    'heading' => 'Our Values',
                    'items' => [
                        ['title' => 'Innovation', 'description' => 'Constantly pushing boundaries and exploring new possibilities.'],
                        ['title' => 'Integrity', 'description' => 'Building trust through transparency and ethical practices.'],
                        ['title' => 'Excellence', 'description' => 'Delivering exceptional quality in everything we do.'],
                        ['title' => 'Collaboration', 'description' => 'Working together to achieve extraordinary results.'],
                    ],
                ],
                'team' => [
                    'heading' => 'Meet Our Team',
                    'subheading' => 'The people behind our success',
                    'members' => [
                        [
                            'name' => 'John Smith',
                            'role' => 'Chief Executive Officer',
                            'bio' => 'Visionary leader with 20+ years of industry experience.',
                            'image' => '/images/defaults/team-1.jpg',
                        ],
                        [
                            'name' => 'Jane Doe',
                            'role' => 'Chief Technology Officer',
                            'bio' => 'Tech innovator passionate about cutting-edge solutions.',
                            'image' => '/images/defaults/team-2.jpg',
                        ],
                        [
                            'name' => 'Robert Williams',
                            'role' => 'Head of Operations',
                            'bio' => 'Operations expert ensuring seamless delivery.',
                            'image' => '/images/defaults/team-3.jpg',
                        ],
                        [
                            'name' => 'Lisa Anderson',
                            'role' => 'Marketing Director',
                            'bio' => 'Creative strategist driving brand growth.',
                            'image' => '/images/defaults/team-4.jpg',
                        ],
                    ],
                ],
            ],
            'services' => [
                'hero' => [
                    'heading' => 'Our Services',
                    'subheading' => 'Comprehensive solutions tailored to your needs',
                ],
                'services_list' => [
                    [
                        'title' => 'Business Consulting',
                        'description' => 'Strategic guidance to navigate challenges and seize opportunities in your market.',
                        'features' => [
                            'Market Research & Analysis',
                            'Business Strategy Development',
                            'Growth Planning',
                            'Risk Management',
                            'Competitive Analysis',
                        ],
                        'pricing' => 'Custom pricing based on scope',
                        'image' => '/images/defaults/consulting.jpg',
                    ],
                    [
                        'title' => 'Technology Solutions',
                        'description' => 'Cutting-edge technology implementation to modernize your operations.',
                        'features' => [
                            'Cloud Infrastructure',
                            'Custom Software Development',
                            'System Integration',
                            'Cybersecurity',
                            'IT Support & Maintenance',
                        ],
                        'pricing' => 'Starting from $999/month',
                        'image' => '/images/defaults/technology.jpg',
                    ],
                    [
                        'title' => 'Digital Marketing',
                        'description' => 'Data-driven marketing strategies to grow your brand and reach your audience.',
                        'features' => [
                            'SEO & Content Marketing',
                            'Social Media Management',
                            'PPC Advertising',
                            'Email Marketing',
                            'Analytics & Reporting',
                        ],
                        'pricing' => 'Starting from $499/month',
                        'image' => '/images/defaults/marketing.jpg',
                    ],
                ],
            ],
            'contact' => [
                'hero' => [
                    'heading' => 'Get In Touch',
                    'subheading' => 'We\'d love to hear from you',
                ],
                'info' => [
                    'address' => '123 Business Street, Suite 100\nNew York, NY 10001',
                    'phone' => '+1 (555) 123-4567',
                    'email' => 'hello@yourbusiness.com',
                    'hours' => 'Monday - Friday: 9:00 AM - 6:00 PM\nSaturday: 10:00 AM - 4:00 PM\nSunday: Closed',
                ],
                'social' => [
                    'facebook' => 'https://facebook.com/yourbusiness',
                    'twitter' => 'https://twitter.com/yourbusiness',
                    'linkedin' => 'https://linkedin.com/company/yourbusiness',
                    'instagram' => 'https://instagram.com/yourbusiness',
                ],
            ],
        ];
    }

    protected static function getRestaurantContent(): array
    {
        return [
            'homepage' => [
                'hero' => [
                    'heading' => 'Authentic Flavors, Memorable Experiences',
                    'subheading' => 'Where passion meets cuisine',
                    'description' => 'Discover a culinary journey that delights your senses',
                    'cta_primary' => 'Reserve a Table',
                    'cta_secondary' => 'View Menu',
                    'image' => '/images/defaults/restaurant-hero.jpg',
                ],
                'features' => [
                    'heading' => 'Why Dine With Us',
                    'items' => [
                        [
                            'icon' => 'fa-utensils',
                            'title' => 'Fresh Ingredients',
                            'description' => 'Locally sourced, organic ingredients prepared daily.',
                        ],
                        [
                            'icon' => 'fa-chef-hat',
                            'title' => 'Expert Chefs',
                            'description' => 'Award-winning culinary team with decades of experience.',
                        ],
                        [
                            'icon' => 'fa-wine-glass',
                            'title' => 'Fine Selection',
                            'description' => 'Curated wine list and craft cocktails.',
                        ],
                        [
                            'icon' => 'fa-heart',
                            'title' => 'Warm Atmosphere',
                            'description' => 'Elegant ambiance perfect for any occasion.',
                        ],
                    ],
                ],
                'menu_preview' => [
                    'heading' => 'Our Specialties',
                    'subheading' => 'Chef\'s recommended dishes',
                    'items' => [
                        [
                            'name' => 'Grilled Salmon',
                            'description' => 'Fresh Atlantic salmon with seasonal vegetables and lemon butter sauce',
                            'price' => '$32',
                            'image' => '/images/defaults/dish-1.jpg',
                        ],
                        [
                            'name' => 'Filet Mignon',
                            'description' => 'Premium beef tenderloin with truffle mashed potatoes',
                            'price' => '$48',
                            'image' => '/images/defaults/dish-2.jpg',
                        ],
                        [
                            'name' => 'Lobster Risotto',
                            'description' => 'Creamy arborio rice with fresh lobster and parmesan',
                            'price' => '$42',
                            'image' => '/images/defaults/dish-3.jpg',
                        ],
                    ],
                ],
                'testimonials' => [
                    'heading' => 'Guest Reviews',
                    'items' => [
                        [
                            'name' => 'David Thompson',
                            'content' => 'The best dining experience in the city! Every dish was perfection.',
                            'rating' => 5,
                        ],
                        [
                            'name' => 'Maria Garcia',
                            'content' => 'Exceptional food, wonderful service, and a beautiful atmosphere.',
                            'rating' => 5,
                        ],
                    ],
                ],
            ],
            'menu' => [
                'categories' => [
                    [
                        'name' => 'Appetizers',
                        'items' => [
                            ['name' => 'Bruschetta', 'description' => 'Toasted bread with tomatoes, garlic, and basil', 'price' => '$12'],
                            ['name' => 'Calamari', 'description' => 'Crispy fried squid with marinara sauce', 'price' => '$14'],
                            ['name' => 'Caprese Salad', 'description' => 'Fresh mozzarella, tomatoes, and basil', 'price' => '$13'],
                        ],
                    ],
                    [
                        'name' => 'Main Courses',
                        'items' => [
                            ['name' => 'Grilled Salmon', 'description' => 'Fresh Atlantic salmon with seasonal vegetables', 'price' => '$32'],
                            ['name' => 'Filet Mignon', 'description' => 'Premium beef tenderloin', 'price' => '$48'],
                            ['name' => 'Chicken Parmesan', 'description' => 'Breaded chicken with marinara and mozzarella', 'price' => '$26'],
                        ],
                    ],
                    [
                        'name' => 'Desserts',
                        'items' => [
                            ['name' => 'Tiramisu', 'description' => 'Classic Italian dessert', 'price' => '$10'],
                            ['name' => 'Cheesecake', 'description' => 'New York style with berry compote', 'price' => '$9'],
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function getStoreContent(): array
    {
        return [
            'homepage' => [
                'hero' => [
                    'heading' => 'Shop the Latest Trends',
                    'subheading' => 'Quality products at unbeatable prices',
                    'cta_primary' => 'Shop Now',
                    'cta_secondary' => 'View Collection',
                    'image' => '/webbuilder/beautiful-three-welldressed-afro-american-girls-customers-with-colored-shopping-bags-mobile-phone-shop-choosing-smartphone.jpg',
                ],
                'features' => [
                    'items' => [
                        ['icon' => 'fa-shipping-fast', 'title' => 'Free Shipping', 'description' => 'On orders over $50'],
                        ['icon' => 'fa-undo', 'title' => 'Easy Returns', 'description' => '30-day return policy'],
                        ['icon' => 'fa-lock', 'title' => 'Secure Payment', 'description' => 'Safe & encrypted'],
                        ['icon' => 'fa-headset', 'title' => '24/7 Support', 'description' => 'Always here to help'],
                    ],
                ],
                'categories' => [
                    'heading' => 'Shop by Category',
                    'items' => [
                        ['name' => 'Electronics', 'image' => '/images/defaults/cat-electronics.jpg'],
                        ['name' => 'Fashion', 'image' => '/images/defaults/cat-fashion.jpg'],
                        ['name' => 'Home & Garden', 'image' => '/images/defaults/cat-home.jpg'],
                        ['name' => 'Sports', 'image' => '/images/defaults/cat-sports.jpg'],
                    ],
                ],
            ],
        ];
    }

    protected static function getPortfolioContent(): array
    {
        return [
            'homepage' => [
                'hero' => [
                    'heading' => 'Creative Designer & Developer',
                    'subheading' => 'Bringing ideas to life through design and code',
                    'cta_primary' => 'View Portfolio',
                    'cta_secondary' => 'Contact Me',
                ],
                'portfolio' => [
                    'heading' => 'Featured Work',
                    'items' => [
                        [
                            'title' => 'Brand Identity Project',
                            'category' => 'Branding',
                            'description' => 'Complete brand identity design for tech startup',
                            'image' => '/images/defaults/project-1.jpg',
                        ],
                        [
                            'title' => 'E-Commerce Website',
                            'category' => 'Web Design',
                            'description' => 'Modern online store with seamless UX',
                            'image' => '/images/defaults/project-2.jpg',
                        ],
                        [
                            'title' => 'Mobile App UI',
                            'category' => 'UI/UX',
                            'description' => 'Intuitive mobile app interface design',
                            'image' => '/images/defaults/project-3.jpg',
                        ],
                    ],
                ],
            ],
        ];
    }

    protected static function getServiceContent(): array
    {
        return [
            'homepage' => [
                'hero' => [
                    'heading' => 'Professional Services You Can Trust',
                    'subheading' => 'Expert solutions for your needs',
                    'cta_primary' => 'Book Appointment',
                    'cta_secondary' => 'Learn More',
                ],
                'services' => [
                    'heading' => 'What We Offer',
                    'items' => [
                        [
                            'title' => 'Consultation',
                            'description' => 'Expert advice tailored to your specific needs',
                            'icon' => 'fa-comments',
                            'price' => 'From $100/hr',
                        ],
                        [
                            'title' => 'Implementation',
                            'description' => 'Professional execution of your projects',
                            'icon' => 'fa-cogs',
                            'price' => 'Custom quotes',
                        ],
                        [
                            'title' => 'Support',
                            'description' => 'Ongoing assistance and maintenance',
                            'icon' => 'fa-life-ring',
                            'price' => 'From $500/mo',
                        ],
                    ],
                ],
            ],
        ];
    }

    public function run(): void
    {
        $this->command->info('Default content templates ready!');
    }
}
