
<div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs uppercase">Sport</p>
                <p class="text-white text-lg font-bold mt-1"><?php echo e($competition->sport->name); ?></p>
            </div>
            <div class="w-10 h-10 bg-blue-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs uppercase">Učesnici</p>
                <p class="text-white text-lg font-bold mt-1"><?php echo e($competition->players->count()); ?></p>
            </div>
            <div class="w-10 h-10 bg-purple-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs uppercase">Format</p>
                <p class="text-white text-lg font-bold mt-1"><?php echo e($competition->is_team_based ? 'Tim' : 'Individualno'); ?></p>
            </div>
            <div class="w-10 h-10 bg-green-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <?php if($competition->start_date): ?>
    <div class="bg-gray-800/50 backdrop-blur-xl rounded-xl p-4 border border-gray-700/50">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-gray-400 text-xs uppercase">Datum Početka</p>
                <p class="text-white text-lg font-bold mt-1"><?php echo e($competition->start_date->format('M d, Y')); ?></p>
            </div>
            <div class="w-10 h-10 bg-yellow-600/20 rounded-lg flex items-center justify-center">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php /**PATH C:\Users\ermin\Projekti\teamsphere\resources\views/organizations/competitions/partials/info-cards.blade.php ENDPATH**/ ?>