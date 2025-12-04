<x-filament-widgets::widgets
    :widgets="$this->getHeaderWidgets()"
    :columns="$this->getHeaderWidgetsColumns()"
    class="mb-8"
/>

<div class="space-y-8">
    <section>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Statistics Overview</h2>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Words Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Words</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Word::count() }}</p>
                    </div>
                    <div class="bg-blue-100 dark:bg-blue-900 rounded-full p-4">
                        <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C6.5 6.253 2 10.998 2 17s4.5 10.747 10 10.747c5.5 0 10-4.998 10-10.747 0-6.002-4.5-10.747-10-10.747z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Topics Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Topics</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\Topic::count() }}</p>
                    </div>
                    <div class="bg-green-100 dark:bg-green-900 rounded-full p-4">
                        <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Users Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Total Users</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\User::count() }}</p>
                    </div>
                    <div class="bg-purple-100 dark:bg-purple-900 rounded-full p-4">
                        <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 12H9m6 0a6 6 0 11-12 0 6 6 0 0112 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>

            <!-- Tests Statistics -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 dark:text-gray-400 text-sm font-medium">Completed Tests</p>
                        <p class="text-3xl font-bold text-gray-900 dark:text-white mt-2">{{ \App\Models\DailyTest::where('status', 'completed')->count() }}</p>
                    </div>
                    <div class="bg-orange-100 dark:bg-orange-900 rounded-full p-4">
                        <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Charts Section -->
    <section>
        <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-100 mb-6">Performance Metrics</h2>
        <x-filament-widgets::widgets
            :widgets="$this->getFooterWidgets()"
            :columns="$this->getFooterWidgetsColumns()"
        />
    </section>
</div>
