<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIBusinessAdvisor;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class AIAnalysisController extends Controller
{
    protected $aiAdvisor;

    public function __construct(AIBusinessAdvisor $aiAdvisor)
    {
        $this->aiAdvisor = $aiAdvisor;
        $this->middleware('auth');
        $this->middleware('has.business');
    }

    /**
     * Show AI dashboard
     */
    public function dashboard()
    {
        $business = Auth::user()->business;
        
        // Get AI summary for dashboard
        $aiSummary = $this->aiAdvisor->getDashboardSummary($business->id);
        
        return view('ai.dashboard', compact('aiSummary', 'business'));
    }

    /**
     * Generate comprehensive analysis
     */
    public function comprehensiveAnalysis()
    {
        $business = Auth::user()->business;
        
        try {
            $analysis = $this->aiAdvisor->generateComprehensiveAnalysis($business->id);
            
            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'AI analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate specific analysis type
     */
    public function specificAnalysis(Request $request)
    {
        $business = Auth::user()->business;
        $analysisType = $request->input('type', 'revenue');
        
        try {
            $analysis = $this->aiAdvisor->generateSpecificAnalysis($business->id, $analysisType);
            
            return response()->json([
                'success' => true,
                'data' => $analysis
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Specific analysis failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generate marketing content
     */
    public function marketingContent()
    {
        $business = Auth::user()->business;
        
        try {
            $content = $this->aiAdvisor->generateMarketingContent($business->id);
            
            return view('ai.marketing', compact('content', 'business'));
        } catch (\Exception $e) {
            return back()->with('error', 'Marketing content generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate business recommendations
     */
    public function recommendations()
    {
        $business = Auth::user()->business;
        
        try {
            $recommendations = $this->aiAdvisor->generateBusinessRecommendations($business->id);
            
            return view('ai.recommendations', compact('recommendations', 'business'));
        } catch (\Exception $e) {
            return back()->with('error', 'Recommendations generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate loyalty program design
     */
    public function loyaltyProgram()
    {
        $business = Auth::user()->business;
        
        try {
            $loyaltyProgram = $this->aiAdvisor->designLoyaltyProgram($business->id);
            
            return view('ai.loyalty', compact('loyaltyProgram', 'business'));
        } catch (\Exception $e) {
            return back()->with('error', 'Loyalty program design failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate service packages
     */
    public function servicePackages()
    {
        $business = Auth::user()->business;
        
        try {
            $packages = $this->aiAdvisor->generateServicePackages($business->id);
            
            return view('ai.packages', compact('packages', 'business'));
        } catch (\Exception $e) {
            return back()->with('error', 'Service package generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Generate pricing strategy
     */
    public function pricingStrategy()
    {
        $business = Auth::user()->business;
        
        try {
            $strategy = $this->aiAdvisor->generatePricingStrategy($business->id);
            
            return view('ai.pricing', compact('strategy', 'business'));
        } catch (\Exception $e) {
            return back()->with('error', 'Pricing strategy generation failed: ' . $e->getMessage());
        }
    }

    /**
     * Create video content
     */
    public function createVideo(Request $request)
    {
        $business = Auth::user()->business;
        
        $request->validate([
            'script' => 'required|string',
            'video_type' => 'required|string|in:brand_introduction,product_showcase,testimonial'
        ]);
        
        try {
            $video = $this->aiAdvisor->createVideoContent(
                $business->id,
                $request->script,
                $request->video_type
            );
            
            return response()->json([
                'success' => true,
                'data' => $video
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Video creation failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export AI report
     */
    public function exportReport(Request $request)
    {
        $business = Auth::user()->business;
        $format = $request->input('format', 'json');
        
        try {
            $report = $this->aiAdvisor->exportReport($business->id, $format);
            
            if ($format === 'json') {
                return response()->json($report);
            } else {
                // For PDF/CSV, return download response
                return response()->download($report['file_path'], "ai_report_{$business->id}.{$format}");
            }
        } catch (\Exception $e) {
            return back()->with('error', 'Report export failed: ' . $e->getMessage());
        }
    }

    /**
     * Check AI system status
     */
    public function systemStatus()
    {
        try {
            $pythonStatus = $this->aiAdvisor->checkPythonEnvironment();
            $packageStatus = $this->aiAdvisor->installPythonPackages();
            
            return response()->json([
                'success' => true,
                'python_status' => $pythonStatus,
                'package_status' => $packageStatus
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'System status check failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clear AI cache
     */
    public function clearCache()
    {
        $business = Auth::user()->business;
        
        try {
            Cache::forget("ai_analysis_{$business->id}");
            
            return response()->json([
                'success' => true,
                'message' => 'AI cache cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Cache clearing failed: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show AI insights widget
     */
    public function insightsWidget()
    {
        $business = Auth::user()->business;
        
        try {
            $summary = $this->aiAdvisor->getDashboardSummary($business->id);
            
            return view('ai.widgets.insights', compact('summary', 'business'));
        } catch (\Exception $e) {
            return view('ai.widgets.insights', [
                'summary' => ['error' => $e->getMessage()],
                'business' => $business
            ]);
        }
    }

    /**
     * Show AI recommendations widget
     */
    public function recommendationsWidget()
    {
        $business = Auth::user()->business;
        
        try {
            $recommendations = $this->aiAdvisor->generateBusinessRecommendations($business->id);
            
            return view('ai.widgets.recommendations', compact('recommendations', 'business'));
        } catch (\Exception $e) {
            return view('ai.widgets.recommendations', [
                'recommendations' => ['error' => $e->getMessage()],
                'business' => $business
            ]);
        }
    }

    /**
     * Show AI marketing widget
     */
    public function marketingWidget()
    {
        $business = Auth::user()->business;
        
        try {
            $content = $this->aiAdvisor->generateMarketingContent($business->id);
            
            return view('ai.widgets.marketing', compact('content', 'business'));
        } catch (\Exception $e) {
            return view('ai.widgets.marketing', [
                'content' => ['error' => $e->getMessage()],
                'business' => $business
            ]);
        }
    }
}
