<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Too Many Requests - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Figtree', 'sans-serif'],
                    },
                }
            }
        }
    </script>
</head>
<body class="h-full bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800 font-sans antialiased">
    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <!-- Header -->
            <div class="text-center">
                <div class="mx-auto h-24 w-24 text-red-500 dark:text-red-400">
                    <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-24 h-24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 16.5c-.77.833.192 2.5 1.732 2.5z" />
                    </svg>
                </div>
                
                <h2 class="mt-6 text-3xl font-extrabold text-gray-900 dark:text-white">
                    @if(isset($isLocked) && $isLocked)
                        Account Locked
                    @else
                        Too Many Requests
                    @endif
                </h2>
                
                <p class="mt-2 text-sm text-gray-600 dark:text-gray-400">
                    @if(isset($isLocked) && $isLocked)
                        Your account has been temporarily locked due to excessive violations.
                    @else
                        You've made too many requests in a short period of time.
                    @endif
                </p>
            </div>
            
            <!-- Main Card -->
            <div class="bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-700 shadow-lg p-6 space-y-6">
                <!-- Alert Banner -->
                @if(isset($isLocked) && $isLocked)
                <div class="flex items-center space-x-3 p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-red-800 dark:text-red-200">Account Locked</h3>
                        <p class="text-sm text-red-700 dark:text-red-300">Your account has been locked due to excessive rate limit violations.</p>
                        <p class="text-xs text-red-600 dark:text-red-400 mt-1">Please contact an administrator to unlock your account.</p>
                    </div>
                </div>
                @else
                <div class="flex items-center space-x-3 p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">Rate Limit Exceeded</h3>
                        <p class="text-sm text-yellow-700 dark:text-yellow-300">Please wait before making more requests.</p>
                    </div>
                </div>
                @endif
                
                @if(isset($retryAfter) && $retryAfter > 0)
                <!-- Countdown Info -->
                <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-800 rounded-lg p-4">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-semibold text-indigo-800 dark:text-indigo-200">
                                @if($retryAfter >= 3600)
                                    Please wait {{ ceil($retryAfter / 3600) }} hour(s)
                                @elseif($retryAfter >= 60)
                                    Please wait {{ ceil($retryAfter / 60) }} minute(s)
                                @else
                                    Please wait {{ $retryAfter }} second(s)
                                @endif
                            </h3>
                            <div class="mt-2 text-sm text-indigo-700 dark:text-indigo-300">
                                <p>You can try again in <span class="font-semibold" id="countdown">{{ $retryAfter }}</span> seconds.</p>
                                @if($retryAfter > 3600)
                                    <p class="mt-1 text-xs">⚠️ Repeated violations result in longer timeouts</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Explanation -->
                <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                    <h4 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Why did this happen?</h4>
                    <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-2">
                        <li class="flex items-center">
                            <span class="text-indigo-500 mr-2">•</span>
                            Too many requests in a short time period
                        </li>
                        <li class="flex items-center">
                            <span class="text-indigo-500 mr-2">•</span>
                            Rate limiting helps protect our servers
                        </li>
                        <li class="flex items-center">
                            <span class="text-indigo-500 mr-2">•</span>
                            Timeout increases with each violation
                        </li>
                        @if(isset($retryAfter) && $retryAfter > 1800)
                        <li class="flex items-center">
                            <span class="text-red-500 mr-2">•</span>
                            <span class="text-red-600 dark:text-red-400">Excessive violations may result in account lock</span>
                        </li>
                        @endif
                    </ul>
                    
                    @if(isset($retryAfter) && $retryAfter >= 3600)
                    <div class="mt-3 p-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded text-xs text-red-800 dark:text-red-200">
                        <strong>Warning:</strong> If violations continue, your account may be temporarily locked and require administrator assistance.
                    </div>
                    @endif
                </div>
                
                <!-- Action Buttons -->
                <div class="flex space-x-3">
                    <button 
                        onclick="goBackSafely()" 
                        class="flex-1 inline-flex items-center justify-center rounded-lg border border-gray-300 dark:border-gray-600 bg-white dark:bg-gray-700 px-4 py-2 text-sm font-semibold text-gray-700 dark:text-gray-300 shadow-sm transition-all duration-200 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800"
                    >
                        ← Go Back
                    </button>
                    
                    <a 
                        href="{{ route('home') }}" 
                        class="flex-1 inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-indigo-600 to-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-lg shadow-indigo-500/30 transition-all duration-200 hover:shadow-xl hover:shadow-indigo-500/40 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 active:translate-y-0 dark:shadow-indigo-500/20 dark:focus:ring-offset-gray-800"
                    >
                        🏠 Home Page
                    </a>
                </div>
                
                @if(isset($retryAfter) && $retryAfter > 0)
                <!-- Retry Button -->
                <div class="text-center pt-4 border-t border-gray-200 dark:border-gray-600">
                    <button 
                        id="retryButton"
                        onclick="retryNow()"
                        disabled
                        class="inline-flex items-center justify-center rounded-lg bg-gray-300 dark:bg-gray-600 cursor-not-allowed text-white font-semibold py-2 px-6 text-sm transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-offset-2"
                    >
                        🔄 Retry in <span id="retryCountdown">{{ $retryAfter }}</span>s
                    </button>
                </div>
                @endif
            </div>
            
            <!-- Footer -->
            <div class="text-center text-sm text-gray-500 dark:text-gray-400">
                <p>
                    If this problem persists, please 
                    <a href="mailto:support@{{ request()->getHost() }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 font-medium">
                        contact support
                    </a>.
                </p>
            </div>
        </div>
    </div>

    @if(isset($retryAfter) && $retryAfter > 0)
    <script>
        let countdown = {{ $retryAfter }};
        const countdownElement = document.getElementById('countdown');
        const retryButton = document.getElementById('retryButton');
        const retryCountdownElement = document.getElementById('retryCountdown');
        
        const interval = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            retryCountdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(interval);
                retryButton.disabled = false;
                retryButton.className = 'inline-flex items-center justify-center rounded-lg bg-gradient-to-r from-green-600 to-green-700 px-6 py-2 text-sm font-semibold text-white shadow-lg shadow-green-500/30 transition-all duration-200 hover:shadow-xl hover:shadow-green-500/40 hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 active:translate-y-0 cursor-pointer dark:shadow-green-500/20 dark:focus:ring-offset-gray-800';
                retryButton.innerHTML = '✓ Retry Now';
            }
        }, 1000);
        
        function retryNow() {
            if (countdown <= 0) {
                // Try to go to the original URL that was rate limited
                @if(isset($originalUrl))
                    window.location.href = '{{ $originalUrl }}';
                @else
                    // Fallback to referrer or homepage
                    const referrer = document.referrer;
                    if (referrer && referrer !== window.location.href && !referrer.includes('/errors/429')) {
                        window.location.href = referrer;
                    } else {
                        window.location.href = '{{ route("home") }}';
                    }
                @endif
            }
        }
        
        function goBackSafely() {
            // Check if there's a valid history to go back to
            const referrer = document.referrer;
            if (referrer && referrer !== window.location.href && !referrer.includes('/errors/429')) {
                history.back();
            } else {
                // If no valid history, go to homepage
                window.location.href = '{{ route("home") }}';
            }
        }
        
        // Dark mode detection
        if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
            document.documentElement.classList.add('dark');
        }
        
        // Listen for dark mode changes
        window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', e => {
            if (e.matches) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        });
    </script>
    @endif
</body>
</html>