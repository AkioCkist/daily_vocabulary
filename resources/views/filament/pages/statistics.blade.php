<x-filament-panels::page>
    <div class="fi-page-content space-y-6">
        <!-- Header Widgets (Stats Overview) -->
        <x-filament-widgets::widgets
            :widgets="$this->getHeaderWidgets()"
            :columns="$this->getHeaderWidgetsColumns()"
        />

        <!-- Quick Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-primary-600">{{ \App\Models\Word::count() }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Words</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-success-600">{{ \App\Models\Topic::count() }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Topics</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-warning-600">{{ \App\Models\User::count() }}</div>
                    <div class="text-sm text-gray-500 mt-1">Total Users</div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <div class="text-center">
                    <div class="text-3xl font-bold text-danger-600">{{ \App\Models\DailyTest::where('is_completed', true)->count() }}</div>
                    <div class="text-sm text-gray-500 mt-1">Completed Tests</div>
                </div>
            </x-filament::section>
        </div>

        <!-- Charts and Analytics -->
        <x-filament::section>
            <x-slot name="heading">
                Performance Metrics
            </x-slot>
            
            <x-filament-widgets::widgets
                :widgets="$this->getFooterWidgets()"
                :columns="$this->getFooterWidgetsColumns()"
            />
        </x-filament::section>
    </div>
</x-filament-panels::page>
