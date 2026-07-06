<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\ContinuousKnowledgeService;
use App\Models\Business;
use App\Models\AIBusinessAdvice;
use App\Models\AILearningCache;

class ContinuousKnowledgeController extends Controller
{
    protected $knowledgeService;

    public function __construct(ContinuousKnowledgeService $knowledgeService)
    {
        $this->knowledgeService = $knowledgeService;
    }

    /**
     * Display the knowledge dashboard
     */
    public function dashboard()
    {
        $dashboard = $this->knowledgeService->getKnowledgeDashboard();
        
        return view('business.knowledge-dashboard', compact('dashboard'));
    }

    /**
     * Start the continuous learning system
     */
    public function startLearning(Request $request)
    {
        try {
            $success = $this->knowledgeService->startContinuousLearning();
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Continuous learning system started successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to start continuous learning system'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error starting continuous learning system. Please try again.'
            ], 500);
        }
    }

    /**
     * Stop the continuous learning system
     */
    public function stopLearning(Request $request)
    {
        try {
            $success = $this->knowledgeService->stopContinuousLearning();
            
            if ($success) {
                return response()->json([
                    'success' => true,
                    'message' => 'Continuous learning system stopped successfully'
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to stop continuous learning system'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error stopping continuous learning system. Please try again.'
            ], 500);
        }
    }

    /**
     * Get latest knowledge data
     */
    public function getLatestKnowledge(Request $request)
    {
        $dataType = $request->get('data_type');
        $limit = $request->get('limit', 50);
        
        $knowledge = $this->knowledgeService->getLatestKnowledge($dataType, $limit);
        
        return response()->json([
            'success' => true,
            'data' => $knowledge
        ]);
    }

    /**
     * Get trending topics
     */
    public function getTrendingTopics(Request $request)
    {
        $hours = $request->get('hours', 24);
        
        $topics = $this->knowledgeService->getTrendingTopics($hours);
        
        return response()->json([
            'success' => true,
            'data' => $topics
        ]);
    }

    /**
     * Get knowledge statistics
     */
    public function getKnowledgeStats()
    {
        $stats = $this->knowledgeService->getKnowledgeStats();
        
        return response()->json([
            'success' => true,
            'data' => $stats
        ]);
    }

    /**
     * Generate business-specific insights
     */
    public function generateBusinessInsights(Request $request)
    {
        $businessId = $request->get('business_id');
        
        if (!$businessId) {
            return response()->json([
                'success' => false,
                'message' => 'Business ID is required'
            ], 400);
        }

        try {
            $insights = $this->knowledgeService->generateBusinessInsights($businessId);
            
            if ($insights) {
                return response()->json([
                    'success' => true,
                    'data' => $insights
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to generate business insights'
                ], 500);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating business insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get business insights
     */
    public function getBusinessInsights(Request $request)
    {
        $businessId = $request->get('business_id');
        
        if (!$businessId) {
            return response()->json([
                'success' => false,
                'message' => 'Business ID is required'
            ], 400);
        }

        try {
            // Get cached insights
            $cachedInsights = AILearningCache::where('business_id', $businessId)->first();
            
            if ($cachedInsights) {
                $insights = json_decode($cachedInsights->learned_data, true);
                
                return response()->json([
                    'success' => true,
                    'data' => $insights,
                    'last_updated' => $cachedInsights->updated_at
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No insights available for this business'
                ], 404);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting business insights: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get system status
     */
    public function getSystemStatus()
    {
        try {
            $stats = $this->knowledgeService->getKnowledgeStats();
            $status = $stats['system_status'] ?? [];
            
            return response()->json([
                'success' => true,
                'data' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting system status: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get knowledge by type
     */
    public function getKnowledgeByType()
    {
        try {
            $stats = $this->knowledgeService->getKnowledgeStats();
            $knowledgeByType = $stats['knowledge_by_type'] ?? [];
            
            return response()->json([
                'success' => true,
                'data' => $knowledgeByType
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error getting knowledge by type: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Search knowledge
     */
    public function searchKnowledge(Request $request)
    {
        $query = $request->get('query');
        $dataType = $request->get('data_type');
        $limit = $request->get('limit', 20);
        
        if (!$query) {
            return response()->json([
                'success' => false,
                'message' => 'Search query is required'
            ], 400);
        }

        try {
            $knowledge = $this->knowledgeService->searchKnowledge($query, $dataType, $limit);
            
            return response()->json([
                'success' => true,
                'data' => $knowledge
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching knowledge: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get knowledge dashboard for a specific business
     */
    public function businessKnowledgeDashboard($businessId)
    {
        try {
            $business = Business::findOrFail($businessId);
            
            // Get business insights
            $insights = $this->knowledgeService->generateBusinessInsights($businessId);
            
            // Get relevant knowledge
            $relevantKnowledge = $this->knowledgeService->getLatestKnowledge(null, 20);
            
            // Get trending topics
            $trendingTopics = $this->knowledgeService->getTrendingTopics();
            
            return view('business.business-knowledge-dashboard', compact(
                'business',
                'insights',
                'relevantKnowledge',
                'trendingTopics'
            ));
            
        } catch (\Exception $e) {
            return back()->with('error', 'Error loading knowledge dashboard: ' . $e->getMessage());
        }
    }

    /**
     * Export knowledge data
     */
    public function exportKnowledge(Request $request)
    {
        $dataType = $request->get('data_type');
        $format = $request->get('format', 'json');
        $limit = $request->get('limit', 1000);
        
        try {
            $knowledge = $this->knowledgeService->getLatestKnowledge($dataType, $limit);
            
            if ($format === 'csv') {
                return $this->exportToCsv($knowledge, $dataType);
            } else {
                return response()->json([
                    'success' => true,
                    'data' => $knowledge
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error exporting knowledge: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Export to CSV
     */
    protected function exportToCsv($data, $dataType)
    {
        $filename = 'knowledge_export_' . ($dataType ?? 'all') . '_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];
        
        $callback = function() use ($data) {
            $file = fopen('php://output', 'w');
            
            // Write headers
            fputcsv($file, ['Data Type', 'Title', 'Description', 'Source', 'Sentiment', 'Created At']);
            
            // Write data
            foreach ($data as $item) {
                $rowData = $item['data'];
                fputcsv($file, [
                    $item['data_type'],
                    $rowData['title'] ?? '',
                    $rowData['description'] ?? '',
                    $rowData['source'] ?? '',
                    $rowData['sentiment'] ?? '',
                    $item['created_at']
                ]);
            }
            
            fclose($file);
        };
        
        return response()->stream($callback, 200, $headers);
    }
}
