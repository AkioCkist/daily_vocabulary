<div class="space-y-8">
    <section>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Application Settings</h2>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- General Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">General Settings</h3>
                <form class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application Name</label>
                        <input type="text" value="Daily Vocabulary" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Application URL</label>
                        <input type="text" value="{{ config('app.url') }}" class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Environment</label>
                        <input type="text" value="{{ config('app.env') }}" disabled class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-gray-50 dark:bg-gray-700 text-gray-900 dark:text-white opacity-50 cursor-not-allowed">
                    </div>
                </form>
            </div>

            <!-- Database Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Database Information</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Driver</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('database.default') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Host</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('database.connections.'.config('database.default').'.host') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Database</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('database.connections.'.config('database.default').'.database') }}</p>
                    </div>
                </div>
            </div>

            <!-- Mail Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Mail Settings</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Mail Driver</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('mail.default') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">From Address</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('mail.from.address') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">From Name</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('mail.from.name') }}</p>
                    </div>
                </div>
            </div>

            <!-- Cache Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Cache Settings</h3>
                <div class="space-y-4">
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Cache Driver</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('cache.default') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Session Driver</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('session.driver') }}</p>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Queue Driver</p>
                        <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ config('queue.default') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- System Information -->
    <section>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">System Information</h2>
        
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">PHP Version</p>
                    <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ phpversion() }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Laravel Version</p>
                    <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ app()->version() }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Server OS</p>
                    <p class="text-lg text-gray-900 dark:text-white font-semibold">{{ php_uname('s') }}</p>
                </div>
                <div>
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Debug Mode</p>
                    <p class="text-lg text-gray-900 dark:text-white font-semibold">
                        @if(config('app.debug'))
                            <span class="text-red-600">Enabled</span>
                        @else
                            <span class="text-green-600">Disabled</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </section>
</div>
