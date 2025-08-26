<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SetupCanadianAI extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'ai:setup-kenyan-model {--force}';

    /**
     * The console command description.
     */
    protected $description = 'Setup the KENADA (Kenya National Data) AI model integration for Shopybook';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🤖 Setting up KENADA AI Model Integration');
        $this->info('==========================================');

        // Step 1: Run migrations
        $this->info('📊 Setting up database tables...');
        try {
            Artisan::call('migrate', ['--force' => true]);
            $this->info('✅ Database migrations completed');
        } catch (\Exception $e) {
            $this->error('❌ Migration failed: ' . $e->getMessage());
            return;
        }

        // Step 2: Check Python environment
        $this->info('🐍 Checking Python environment...');
        $this->checkPythonEnvironment();

        // Step 3: Check AI model files
        $this->info('🧠 Checking AI model files...');
        $this->checkModelFiles();

        // Step 4: Create required directories
        $this->info('📁 Creating required directories...');
        $this->createDirectories();

        // Step 5: Check dependencies
        $this->info('📦 Checking Python dependencies...');
        $this->checkPythonDependencies();

        // Step 6: Test basic functionality
        $this->info('🔧 Testing basic functionality...');
        $this->testBasicFunctionality();

        // Step 7: Configuration recommendations
        $this->info('⚙️ Configuration recommendations...');
        $this->showConfigurationRecommendations();

        $this->info('');
        $this->info('✅ Setup completed successfully!');
        $this->info('🚀 Your KENADA AI model is ready to use.');
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Place your Kenyan MSME data file in the shopybookaimodels directory');
        $this->info('2. Test the integration: php artisan ai:test-kenyan-model');
        $this->info('3. Access the enhanced AI analysis in your Shopybook dashboard');
        $this->info('4. Use the KENADA chatbot for interactive business insights');
    }

    protected function checkPythonEnvironment()
    {
        $pythonPath = config('ai.models.kenyan_msme.python_path', 'python');
        
        $result = shell_exec("{$pythonPath} --version 2>&1");
        
        if (strpos($result, 'Python') !== false) {
            $this->info("✅ Python found: {$result}");
        } else {
            $this->warn("⚠️ Python not found or not accessible");
            $this->warn("Please ensure Python is installed and accessible via '{$pythonPath}'");
            $this->warn("You can update the path in config/ai.php");
        }
    }

    protected function checkModelFiles()
    {
        $modelPath = base_path('shopybookaimodels');
        
        $requiredFiles = [
            'Shopybookbusinessanalyst_local.py',
            'data_dictionary.py'
        ];
        
        $this->info("Checking model directory: {$modelPath}");
        
        foreach ($requiredFiles as $file) {
            $filePath = $modelPath . DIRECTORY_SEPARATOR . $file;
            if (File::exists($filePath)) {
                $this->info("✅ Found: {$file}");
            } else {
                $this->error("❌ Missing: {$file}");
                $this->error("Please ensure all AI model files are in the shopybookaimodels directory");
            }
        }

        // Check for trained model
        $trainedModelPath = $modelPath . DIRECTORY_SEPARATOR . 'trained_model.pkl';
        if (File::exists($trainedModelPath)) {
            $this->info("✅ Found trained model: trained_model.pkl");
        } else {
            $this->warn("⚠️ Trained model not found: trained_model.pkl");
            $this->warn("The model will train automatically on first use or you can run the training script manually");
        }

        // Check for data file
        $dataFile = $modelPath . DIRECTORY_SEPARATOR . '2016 MSME Survey ver. 1.0.dta';
        if (File::exists($dataFile)) {
            $this->info("✅ Found Kenyan data file");
        } else {
            $this->warn("⚠️ KENADA MSME data file not found");
            $this->warn("Please place the '2016 MSME Survey ver. 1.0.dta' file in the shopybookaimodels directory");
        }
    }

    protected function createDirectories()
    {
        $directories = [
            storage_path('app/ai_data'),
            storage_path('app/ai_cache'),
            storage_path('app/ai_reports'),
            storage_path('logs/ai'),
        ];

        foreach ($directories as $dir) {
            if (!File::exists($dir)) {
                File::makeDirectory($dir, 0755, true);
                $this->info("✅ Created directory: {$dir}");
            } else {
                $this->info("✅ Directory exists: {$dir}");
            }
        }
    }

    protected function checkPythonDependencies()
    {
        $pythonPath = config('ai.models.canadian_msme.python_path', 'python');
        
        $requiredPackages = [
            'pandas',
            'numpy',
            'scikit-learn',
            'matplotlib',
            'pickle',
        ];

        $this->info("Checking Python packages...");
        
        foreach ($requiredPackages as $package) {
            $result = shell_exec("{$pythonPath} -c \"import {$package}\" 2>&1");
            
            if (empty($result)) {
                $this->info("✅ {$package} is installed");
            } else {
                $this->error("❌ {$package} is missing");
                $this->error("Install with: pip install {$package}");
            }
        }
    }

    protected function testBasicFunctionality()
    {
        try {
            // Test database connection
            DB::connection()->getPdo();
            $this->info("✅ Database connection successful");

            // Test if AI tables exist
            $tables = ['ai_business_analysis', 'ai_business_recommendations', 'ai_model_performance'];
            
            foreach ($tables as $table) {
                if (DB::getSchemaBuilder()->hasTable($table)) {
                    $this->info("✅ Table exists: {$table}");
                } else {
                    $this->error("❌ Table missing: {$table}");
                }
            }

        } catch (\Exception $e) {
            $this->error("❌ Database test failed: " . $e->getMessage());
        }
    }

    protected function showConfigurationRecommendations()
    {
        $this->info("Environment Configuration:");
        $this->info("Add these to your .env file for optimal performance:");
        $this->info("");
        
        $recommendations = [
            'AI_DEFAULT_MODEL=kenyan_msme',
            'AI_KENYAN_MODEL_ENABLED=true',
            'AI_PYTHON_PATH=python',
            'AI_CACHE_RESULTS=true',
            'AI_CACHE_DURATION=3600',
            'AI_MAX_ANALYSIS_TIME=300',
            'AI_LOGGING_ENABLED=true',
            'AI_STORE_PREDICTIONS=true',
        ];

        foreach ($recommendations as $rec) {
            $this->line("  {$rec}");
        }

        $this->info("");
        $this->info("For production environments, also consider:");
        $this->line("  AI_ENCRYPT_DATA=true");
        $this->line("  AI_MASK_SENSITIVE_DATA=true");
        $this->line("  AI_DATA_RETENTION_DAYS=90");
    }
}
