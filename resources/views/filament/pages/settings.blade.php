<x-filament-panels::page>
    <div class="fi-page-content">
        <!-- General Settings Card -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <x-filament::section>
                <x-slot name="heading">
                    General Settings
                </x-slot>
                
                <div class="space-y-4">
                    <div>
                        <x-filament::section.description>
                            Application Name
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('app.name', 'Laravel') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            Application URL
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('app.url') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            Environment
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('app.env') }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Database Information
                </x-slot>
                
                <div class="space-y-4">
                    <div>
                        <x-filament::section.description>
                            Driver
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('database.default') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            Host
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('database.connections.'.config('database.default').'.host') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            Database
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('database.connections.'.config('database.default').'.database') }}</p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
            <x-filament::section>
                <x-slot name="heading">
                    Mail Settings
                </x-slot>
                
                <div class="space-y-4">
                    <div>
                        <x-filament::section.description>
                            Mail Driver
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('mail.default') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            From Address
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('mail.from.address') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            From Name
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('mail.from.name') }}</p>
                    </div>
                </div>
            </x-filament::section>

            <x-filament::section>
                <x-slot name="heading">
                    Cache Settings
                </x-slot>
                
                <div class="space-y-4">
                    <div>
                        <x-filament::section.description>
                            Cache Driver
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('cache.default') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            Session Driver
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('session.driver') }}</p>
                    </div>
                    <div>
                        <x-filament::section.description>
                            Queue Driver
                        </x-filament::section.description>
                        <p class="font-semibold">{{ config('queue.default') }}</p>
                    </div>
                </div>
            </x-filament::section>
        </div>

        <!-- System Information -->
        <x-filament::section>
            <x-slot name="heading">
                System Information
            </x-slot>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div>
                    <x-filament::section.description>
                        PHP Version
                    </x-filament::section.description>
                    <p class="font-semibold">{{ phpversion() }}</p>
                </div>
                <div>
                    <x-filament::section.description>
                        Laravel Version
                    </x-filament::section.description>
                    <p class="font-semibold">{{ app()->version() }}</p>
                </div>
                <div>
                    <x-filament::section.description>
                        Server OS
                    </x-filament::section.description>
                    <p class="font-semibold">{{ php_uname('s') }}</p>
                </div>
                <div>
                    <x-filament::section.description>
                        Debug Mode
                    </x-filament::section.description>
                    <p class="font-semibold">
                        @if(config('app.debug'))
                            <span class="text-red-600">Enabled</span>
                        @else
                            <span class="text-green-600">Disabled</span>
                        @endif
                    </p>
                </div>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>

