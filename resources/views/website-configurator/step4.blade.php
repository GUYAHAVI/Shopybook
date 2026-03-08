@extends('layouts.dash')

@section('title', 'Building Your Website')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-gray-900 via-purple-900 to-gray-900 py-12">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Progress Steps -->
        <div class="mb-12">
            <div class="flex items-center justify-center">
                <div class="flex items-center space-x-4">
                    <!-- All previous steps complete -->
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm text-gray-300">Type</span>
                    </div>
                    
                    <div class="w-16 h-1 bg-green-500"></div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm text-gray-300">Details</span>
                    </div>
                    
                    <div class="w-16 h-1 bg-green-500"></div>
                    
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        <span class="ml-2 text-sm text-gray-300">Pages</span>
                    </div>
                    
                    <div class="w-16 h-1 bg-purple-500"></div>
                    
                    <!-- Step 4 - Current -->
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-purple-500 rounded-full flex items-center justify-center shadow-lg ring-4 ring-purple-400 ring-opacity-50 animate-pulse">
                            <span class="text-white font-bold">4</span>
                        </div>
                        <span class="ml-2 text-sm text-white font-semibold">Build</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="bg-gray-800 rounded-2xl shadow-2xl overflow-hidden border border-gray-700">
            <div class="p-8 text-center">
                
                <!-- Building Animation -->
                <div class="mb-8">
                    <div class="relative inline-block">
                        <div class="w-32 h-32 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center shadow-lg animate-pulse">
                            <svg id="buildingIcon" class="w-16 h-16 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <!-- Rotating ring -->
                        <div class="absolute inset-0 border-4 border-purple-400 border-t-transparent rounded-full animate-spin"></div>
                    </div>
                </div>

                <h1 class="text-4xl font-bold text-white mb-3" id="buildTitle">
                    Building Your Website...
                </h1>
                <p class="text-lg text-gray-300 mb-8" id="buildDescription">
                    Please wait while we create your amazing website
                </p>

                <!-- Progress Bar -->
                <div class="mb-8">
                    <div class="bg-gray-700 rounded-full h-4 overflow-hidden shadow-inner">
                        <div id="progressBar" class="bg-gradient-to-r from-purple-500 to-pink-500 h-full rounded-full transition-all duration-500 ease-out" style="width: 0%"></div>
                    </div>
                    <p class="text-purple-400 font-semibold mt-3 text-lg" id="progressText">0%</p>
                </div>

                <!-- Build Steps -->
                <div class="space-y-4 text-left max-w-2xl mx-auto">
                    @php
                    $buildSteps = [
                        ['id' => 'step1', 'title' => 'Initializing website structure', 'icon' => 'M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z'],
                        ['id' => 'step2', 'title' => 'Setting up database tables', 'icon' => 'M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4'],
                        ['id' => 'step3', 'title' => 'Creating your selected pages', 'icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z'],
                        ['id' => 'step4', 'title' => 'Applying theme and styling', 'icon' => 'M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01'],
                        ['id' => 'step5', 'title' => 'Configuring features', 'icon' => 'M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z M15 12a3 3 0 11-6 0 3 3 0 016 0z'],
                        ['id' => 'step6', 'title' => 'Optimizing performance', 'icon' => 'M13 10V3L4 14h7v7l9-11h-7z'],
                        ['id' => 'step7', 'title' => 'Finalizing your website', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                    ];
                    @endphp

                    @foreach($buildSteps as $step)
                    <div id="{{ $step['id'] }}" class="build-step flex items-center p-4 bg-gray-700 rounded-lg border-2 border-gray-600 opacity-50 transition-all duration-300">
                        <div class="step-icon-container mr-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $step['icon'] }}"></path>
                            </svg>
                        </div>
                        <span class="text-gray-300 font-medium flex-1">{{ $step['title'] }}</span>
                        <div class="step-status ml-4">
                            <div class="spinner hidden animate-spin rounded-full h-6 w-6 border-b-2 border-purple-400"></div>
                            <svg class="checkmark hidden w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    @endforeach
                </div>

                <!-- Loading Messages -->
                <div class="mt-8 text-gray-400 italic" id="loadingMessage">
                    <p>"Great websites are not built overnight, but this one will be ready in a few seconds! 🚀"</p>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.getElementById('progressBar');
    const progressText = document.getElementById('progressText');
    const buildTitle = document.getElementById('buildTitle');
    const buildDescription = document.getElementById('buildDescription');
    const loadingMessage = document.getElementById('loadingMessage');
    
    const steps = [
        { id: 'step1', duration: 1000, progress: 15 },
        { id: 'step2', duration: 1500, progress: 30 },
        { id: 'step3', duration: 2000, progress: 50 },
        { id: 'step4', duration: 1500, progress: 65 },
        { id: 'step5', duration: 1000, progress: 80 },
        { id: 'step6', duration: 1000, progress: 90 },
        { id: 'step7', duration: 1000, progress: 100 },
    ];

    const messages = [
        "Assembling the building blocks...",
        "Adding a touch of magic ✨",
        "Making it responsive for all devices...",
        "Polishing the final touches...",
        "Almost there! Just a few more seconds..."
    ];

    let currentMessageIndex = 0;

    function updateProgressBar(percentage) {
        progressBar.style.width = percentage + '%';
        progressText.textContent = percentage + '%';
    }

    function animateStep(stepId, duration, finalProgress) {
        return new Promise((resolve) => {
            const stepElement = document.getElementById(stepId);
            const spinner = stepElement.querySelector('.spinner');
            const checkmark = stepElement.querySelector('.checkmark');
            const icon = stepElement.querySelector('.step-icon-container svg');

            // Activate step
            stepElement.classList.remove('opacity-50');
            stepElement.classList.add('border-purple-500', 'bg-purple-900/20');
            icon.classList.remove('text-gray-400');
            icon.classList.add('text-purple-400');
            spinner.classList.remove('hidden');

            // Simulate processing
            setTimeout(() => {
                // Complete step
                spinner.classList.add('hidden');
                checkmark.classList.remove('hidden');
                stepElement.classList.remove('border-purple-500');
                stepElement.classList.add('border-green-500');
                icon.classList.remove('text-purple-400');
                icon.classList.add('text-green-400');
                
                // Update progress
                updateProgressBar(finalProgress);
                
                resolve();
            }, duration);
        });
    }

    async function buildWebsite() {
        for (let i = 0; i < steps.length; i++) {
            await animateStep(steps[i].id, steps[i].duration, steps[i].progress);
            
            // Update message every 2 steps
            if (i % 2 === 0 && currentMessageIndex < messages.length) {
                loadingMessage.innerHTML = `<p>${messages[currentMessageIndex]}</p>`;
                currentMessageIndex++;
            }
        }

        // All steps complete!
        setTimeout(() => {
            buildTitle.textContent = '🎉 Your Website is Ready!';
            buildDescription.textContent = 'Redirecting you to your new website...';
            loadingMessage.innerHTML = '<p class="text-green-400 font-semibold">Success! Your website has been created.</p>';
            
            // Change icon to success
            document.getElementById('buildingIcon').innerHTML = `
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            `;

            // Submit the form via fetch so we can follow the redirect_url from JSON
            setTimeout(() => {
                const form = document.getElementById('completeBuildForm');
                const formData = new FormData(form);
                fetch(form.action, {
                    method: 'POST',
                    headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                    body: formData
                })
                .then(r => r.json())
                .then(data => {
                    if (data.redirect_url) {
                        window.location.href = data.redirect_url;
                    } else {
                        window.location.href = '{{ route('website.builder.preview') }}';
                    }
                })
                .catch(() => {
                    window.location.href = '{{ route('website.builder.preview') }}';
                });
            }, 2000);
        }, 500);
    }

    // Start the build animation
    buildWebsite();
});
</script>

<!-- Hidden form to submit when build is complete -->
<form id="completeBuildForm" method="POST" action="{{ route('website-configurator.build') }}" style="display: none;">
    @csrf
</form>
@endsection
