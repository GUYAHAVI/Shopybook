<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class AIBusinessAdvisor
{
    protected $pythonScriptPath;
    protected $aiModelsPath;

    public function __construct()
    {
        $this->pythonScriptPath = base_path('ai_models');
        $this->aiModelsPath = base_path('ai_models');
    }

    /**
     * Generate comprehensive business analysis
     */
    public function generateComprehensiveAnalysis($businessId = null)
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python main_ai_orchestrator.py --business_id " . ($businessId ?? 'all');
            $output = shell_exec($command);
            
            // Parse the output and return structured data
            $result = $this->parseAIOutput($output);
            
            // Cache the result for 1 hour
            Cache::put("ai_analysis_{$businessId}", $result, 3600);
            
            return $result;
        } catch (\Exception $e) {
            Log::error('AI Analysis Error: ' . $e->getMessage());
            return [
                'error' => 'AI analysis failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate specific type of analysis
     */
    public function generateSpecificAnalysis($businessId, $analysisType)
    {
        $validTypes = ['revenue', 'marketing', 'customers', 'operations'];
        
        if (!in_array($analysisType, $validTypes)) {
            return ['error' => 'Invalid analysis type'];
        }

        try {
            $command = "cd {$this->pythonScriptPath} && python main_ai_orchestrator.py --business_id {$businessId} --analysis_type {$analysisType}";
            $output = shell_exec($command);
            
            return $this->parseAIOutput($output);
        } catch (\Exception $e) {
            Log::error('AI Specific Analysis Error: ' . $e->getMessage());
            return [
                'error' => 'AI analysis failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate marketing content
     */
    public function generateMarketingContent($businessId, $contentType = 'all')
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python models/marketing_ai.py --business_id {$businessId} --content_type {$contentType}";
            $output = shell_exec($command);
            
            return $this->parseAIOutput($output);
        } catch (\Exception $e) {
            Log::error('Marketing Content Generation Error: ' . $e->getMessage());
            return [
                'error' => 'Marketing content generation failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate business recommendations
     */
    public function generateBusinessRecommendations($businessId)
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python models/business_intelligence.py --business_id {$businessId}";
            $output = shell_exec($command);
            
            return $this->parseAIOutput($output);
        } catch (\Exception $e) {
            Log::error('Business Recommendations Error: ' . $e->getMessage());
            return [
                'error' => 'Business recommendations generation failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Create video content using LTX Video 13
     */
    public function createVideoContent($businessId, $script, $videoType = 'brand_introduction')
    {
        try {
            // Prepare video configuration
            $videoConfig = [
                'business_id' => $businessId,
                'script' => $script,
                'video_type' => $videoType,
                'style' => 'professional',
                'duration' => '30-60 seconds'
            ];

            // Save configuration to temporary file
            $configFile = storage_path("app/temp/video_config_{$businessId}.json");
            Storage::put("temp/video_config_{$businessId}.json", json_encode($videoConfig));

            // Call video generation script
            $command = "cd {$this->pythonScriptPath} && python utils/video_generator.py --config_file {$configFile}";
            $output = shell_exec($command);

            // Parse video generation result
            $result = $this->parseAIOutput($output);

            // Clean up temporary file
            Storage::delete("temp/video_config_{$businessId}.json");

            return $result;
        } catch (\Exception $e) {
            Log::error('Video Content Creation Error: ' . $e->getMessage());
            return [
                'error' => 'Video content creation failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get cached analysis or generate new one
     */
    public function getCachedAnalysis($businessId)
    {
        $cacheKey = "ai_analysis_{$businessId}";
        
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        return $this->generateComprehensiveAnalysis($businessId);
    }

    /**
     * Generate loyalty program design
     */
    public function designLoyaltyProgram($businessId)
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python models/loyalty_ai.py --business_id {$businessId}";
            $output = shell_exec($command);
            
            return $this->parseAIOutput($output);
        } catch (\Exception $e) {
            Log::error('Loyalty Program Design Error: ' . $e->getMessage());
            return [
                'error' => 'Loyalty program design failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate service packaging recommendations
     */
    public function generateServicePackages($businessId)
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python models/service_optimization.py --business_id {$businessId}";
            $output = shell_exec($command);
            
            return $this->parseAIOutput($output);
        } catch (\Exception $e) {
            Log::error('Service Package Generation Error: ' . $e->getMessage());
            return [
                'error' => 'Service package generation failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Generate pricing strategy recommendations
     */
    public function generatePricingStrategy($businessId)
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python models/pricing_ai.py --business_id {$businessId}";
            $output = shell_exec($command);
            
            return $this->parseAIOutput($output);
        } catch (\Exception $e) {
            Log::error('Pricing Strategy Generation Error: ' . $e->getMessage());
            return [
                'error' => 'Pricing strategy generation failed',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Parse AI model output
     */
    protected function parseAIOutput($output)
    {
        try {
            // Try to parse as JSON
            $data = json_decode($output, true);
            
            if (json_last_error() === JSON_ERROR_NONE) {
                return $data;
            }

            // If not JSON, try to extract structured data from text output
            return $this->parseTextOutput($output);
        } catch (\Exception $e) {
            Log::error('AI Output Parsing Error: ' . $e->getMessage());
            return [
                'error' => 'Failed to parse AI output',
                'raw_output' => $output
            ];
        }
    }

    /**
     * Parse text output from AI models
     */
    protected function parseTextOutput($output)
    {
        $lines = explode("\n", $output);
        $result = [];
        
        foreach ($lines as $line) {
            $line = trim($line);
            
            if (empty($line)) continue;
            
            // Look for key-value pairs
            if (strpos($line, ':') !== false) {
                $parts = explode(':', $line, 2);
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                
                $result[$key] = $value;
            }
            
            // Look for structured data patterns
            if (strpos($line, '📊') !== false) {
                $result['data_collection'] = trim(str_replace('📊', '', $line));
            }
            
            if (strpos($line, '🧠') !== false) {
                $result['analysis'] = trim(str_replace('🧠', '', $line));
            }
            
            if (strpos($line, '💡') !== false) {
                $result['recommendations'] = trim(str_replace('💡', '', $line));
            }
            
            if (strpos($line, '📝') !== false) {
                $result['content_generation'] = trim(str_replace('📝', '', $line));
            }
        }
        
        return $result;
    }

    /**
     * Check if Python environment is properly set up
     */
    public function checkPythonEnvironment()
    {
        try {
            $command = "cd {$this->pythonScriptPath} && python --version";
            $output = shell_exec($command);
            
            if (strpos($output, 'Python') !== false) {
                return [
                    'status' => 'success',
                    'python_version' => trim($output),
                    'ai_models_path' => $this->aiModelsPath
                ];
            } else {
                return [
                    'status' => 'error',
                    'message' => 'Python not found or not properly configured'
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Install required Python packages
     */
    public function installPythonPackages()
    {
        try {
            $requirementsFile = $this->aiModelsPath . '/requirements.txt';
            
            if (!file_exists($requirementsFile)) {
                return [
                    'status' => 'error',
                    'message' => 'Requirements file not found'
                ];
            }

            $command = "cd {$this->pythonScriptPath} && pip install -r requirements.txt";
            $output = shell_exec($command);
            
            return [
                'status' => 'success',
                'message' => 'Python packages installed successfully',
                'output' => $output
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Get AI analysis summary for dashboard
     */
    public function getDashboardSummary($businessId)
    {
        $analysis = $this->getCachedAnalysis($businessId);
        
        if (isset($analysis['error'])) {
            return $analysis;
        }

        return [
            'business_health_score' => $analysis['executive_summary']['business_health_score'] ?? 0,
            'key_metrics' => $analysis['executive_summary']['key_metrics'] ?? [],
            'critical_insights' => $analysis['executive_summary']['critical_insights'] ?? [],
            'priority_actions' => $analysis['executive_summary']['priority_actions'] ?? [],
            'recommendations_count' => count($analysis['recommendations'] ?? []),
            'marketing_content_ready' => !empty($analysis['marketing_content'] ?? [])
        ];
    }

    /**
     * Export AI analysis report
     */
    public function exportReport($businessId, $format = 'json')
    {
        $analysis = $this->getCachedAnalysis($businessId);
        
        if (isset($analysis['error'])) {
            return $analysis;
        }

        switch ($format) {
            case 'json':
                return $analysis;
            
            case 'pdf':
                return $this->generatePDFReport($analysis);
            
            case 'csv':
                return $this->generateCSVReport($analysis);
            
            default:
                return $analysis;
        }
    }

    /**
     * Generate PDF report (placeholder)
     */
    protected function generatePDFReport($analysis)
    {
        // Implementation for PDF generation
        return [
            'status' => 'success',
            'message' => 'PDF report generation not implemented yet',
            'data' => $analysis
        ];
    }

    /**
     * Generate CSV report (placeholder)
     */
    protected function generateCSVReport($analysis)
    {
        // Implementation for CSV generation
        return [
            'status' => 'success',
            'message' => 'CSV report generation not implemented yet',
            'data' => $analysis
        ];
    }
}
