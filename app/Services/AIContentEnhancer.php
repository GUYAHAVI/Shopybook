<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AIContentEnhancer
{
    private $pythonScriptPath;
    
    public function __construct()
    {
        $this->pythonScriptPath = base_path('ai_models/content_enhancer.py');
    }
    
    /**
     * Enhance existing content with AI improvements
     */
    public function enhanceContent($originalText, $contentType = 'description', $tone = 'professional')
    {
        try {
            $command = "python \"{$this->pythonScriptPath}\" enhance " . 
                      escapeshellarg($originalText) . " " . 
                      escapeshellarg($contentType) . " " . 
                      escapeshellarg($tone);
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && isset($result['enhanced_content'])) {
                return [
                    'success' => true,
                    'enhanced_content' => $result['enhanced_content'],
                    'improvements' => $result['improvements'] ?? [],
                    'word_count' => $result['word_count'] ?? 0,
                    'improvement_percentage' => $result['improvement_percentage'] ?? 0
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to enhance content',
                'original' => $originalText
            ];
            
        } catch (\Exception $e) {
            Log::error('AI Content Enhancement failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'AI enhancement service unavailable',
                'original' => $originalText
            ];
        }
    }
    
    /**
     * Generate new content from keywords or brief description
     */
    public function generateContent($keywords, $contentType = 'description', $tone = 'professional', $length = 'medium')
    {
        try {
            $command = "python \"{$this->pythonScriptPath}\" generate " . 
                      escapeshellarg($keywords) . " " . 
                      escapeshellarg($contentType) . " " . 
                      escapeshellarg($tone) . " " . 
                      escapeshellarg($length);
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && isset($result['generated_content'])) {
                return [
                    'success' => true,
                    'generated_content' => $result['generated_content'],
                    'keywords_used' => $result['keywords_used'] ?? [],
                    'word_count' => $result['word_count'] ?? 0
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to generate content',
                'keywords' => $keywords
            ];
            
        } catch (\Exception $e) {
            Log::error('AI Content Generation failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'AI generation service unavailable',
                'keywords' => $keywords
            ];
        }
    }
    
    /**
     * Improve content for SEO optimization
     */
    public function optimizeForSEO($content, $targetKeywords = [])
    {
        try {
            $command = "python \"{$this->pythonScriptPath}\" seo " . 
                      escapeshellarg($content) . " " . 
                      escapeshellarg(json_encode($targetKeywords));
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && isset($result['optimized_content'])) {
                return [
                    'success' => true,
                    'optimized_content' => $result['optimized_content'],
                    'seo_score' => $result['seo_score'] ?? 0,
                    'keyword_density' => $result['keyword_density'] ?? [],
                    'suggestions' => $result['suggestions'] ?? []
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to optimize content for SEO',
                'original' => $content
            ];
            
        } catch (\Exception $e) {
            Log::error('AI SEO Optimization failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'AI SEO service unavailable',
                'original' => $content
            ];
        }
    }
    
    /**
     * Generate multiple content variations
     */
    public function generateVariations($baseContent, $contentType = 'description', $count = 3)
    {
        try {
            $command = "python \"{$this->pythonScriptPath}\" variations " . 
                      escapeshellarg($baseContent) . " " . 
                      escapeshellarg($contentType) . " " . 
                      escapeshellarg($count);
            
            $output = shell_exec($command);
            $result = json_decode($output, true);
            
            if ($result && isset($result['variations'])) {
                return [
                    'success' => true,
                    'variations' => $result['variations'],
                    'count' => count($result['variations'])
                ];
            }
            
            return [
                'success' => false,
                'error' => 'Failed to generate variations',
                'original' => $baseContent
            ];
            
        } catch (\Exception $e) {
            Log::error('AI Content Variations failed: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'AI variations service unavailable',
                'original' => $baseContent
            ];
        }
    }
    
    /**
     * Check if Python environment is available
     */
    public function checkEnvironment()
    {
        try {
            $command = "python --version";
            $output = shell_exec($command);
            return !empty($output);
        } catch (\Exception $e) {
            return false;
        }
    }
    
    /**
     * Get available content types
     */
    public function getContentTypes()
    {
        return [
            'product_description' => 'Product Description',
            'service_description' => 'Service Description',
            'business_description' => 'Business Description',
            'about_us' => 'About Us',
            'mission_statement' => 'Mission Statement',
            'value_proposition' => 'Value Proposition',
            'marketing_copy' => 'Marketing Copy',
            'blog_post' => 'Blog Post',
            'social_media_post' => 'Social Media Post',
            'email_content' => 'Email Content'
        ];
    }
    
    /**
     * Get available tones
     */
    public function getTones()
    {
        return [
            'professional' => 'Professional',
            'casual' => 'Casual',
            'friendly' => 'Friendly',
            'formal' => 'Formal',
            'enthusiastic' => 'Enthusiastic',
            'authoritative' => 'Authoritative',
            'conversational' => 'Conversational',
            'technical' => 'Technical'
        ];
    }
    
    /**
     * Get available lengths
     */
    public function getLengths()
    {
        return [
            'short' => 'Short (50-100 words)',
            'medium' => 'Medium (100-200 words)',
            'long' => 'Long (200-400 words)',
            'extended' => 'Extended (400+ words)'
        ];
    }
}

