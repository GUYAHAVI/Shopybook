<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AIContentEnhancer;
use Illuminate\Support\Facades\Log;

class AIContentController extends Controller
{
    protected $contentEnhancer;
    
    public function __construct(AIContentEnhancer $contentEnhancer)
    {
        $this->contentEnhancer = $contentEnhancer;
    }
    
    /**
     * Show the AI content enhancement interface
     */
    public function index()
    {
        $contentTypes = $this->contentEnhancer->getContentTypes();
        $tones = $this->contentEnhancer->getTones();
        $lengths = $this->contentEnhancer->getLengths();
        
        return view('ai.content-enhancer', compact('contentTypes', 'tones', 'lengths'));
    }
    
    /**
     * Enhance existing content
     */
    public function enhance(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'content_type' => 'required|string',
            'tone' => 'required|string'
        ]);
        
        try {
            $result = $this->contentEnhancer->enhanceContent(
                $request->content,
                $request->content_type,
                $request->tone
            );
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Content enhancement failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Content enhancement failed'
            ], 500);
        }
    }
    
    /**
     * Generate new content from keywords
     */
    public function generate(Request $request)
    {
        $request->validate([
            'keywords' => 'required|string|max:500',
            'content_type' => 'required|string',
            'tone' => 'required|string',
            'length' => 'required|string'
        ]);
        
        try {
            $result = $this->contentEnhancer->generateContent(
                $request->keywords,
                $request->content_type,
                $request->tone,
                $request->length
            );
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Content generation failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Content generation failed'
            ], 500);
        }
    }
    
    /**
     * Optimize content for SEO
     */
    public function optimizeSEO(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'keywords' => 'required|array'
        ]);
        
        try {
            $result = $this->contentEnhancer->optimizeForSEO(
                $request->content,
                $request->keywords
            );
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('SEO optimization failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'SEO optimization failed'
            ], 500);
        }
    }
    
    /**
     * Generate content variations
     */
    public function variations(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:5000',
            'content_type' => 'required|string',
            'count' => 'required|integer|min:1|max:5'
        ]);
        
        try {
            $result = $this->contentEnhancer->generateVariations(
                $request->content,
                $request->content_type,
                $request->count
            );
            
            return response()->json($result);
            
        } catch (\Exception $e) {
            Log::error('Content variations failed: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => 'Content variations failed'
            ], 500);
        }
    }
    
    /**
     * Get available options
     */
    public function getOptions()
    {
        return response()->json([
            'content_types' => $this->contentEnhancer->getContentTypes(),
            'tones' => $this->contentEnhancer->getTones(),
            'lengths' => $this->contentEnhancer->getLengths()
        ]);
    }
    
    /**
     * Check AI service status
     */
    public function status()
    {
        $pythonAvailable = $this->contentEnhancer->checkEnvironment();
        
        return response()->json([
            'python_available' => $pythonAvailable,
            'service_status' => $pythonAvailable ? 'ready' : 'unavailable'
        ]);
    }
}

