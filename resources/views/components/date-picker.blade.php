@props([
    'wireModel',
    'label' => '',
    'placeholder' => 'Wybierz datę',
    'minDate' => null,
    'maxDate' => null,
    'required' => false,
    'id' => null
])

@php
    $id = $id ?? 'date-picker-' . uniqid();
    $minDate = $minDate ?? date('Y-m-d');
@endphp

<div x-data="datePicker"
     x-init="
        wireModelName = '{{ $wireModel }}';
        minDate = '{{ $minDate }}';
        maxDate = '{{ $maxDate }}';
        required = {{ $required ? 'true' : 'false' }};
        selectedDate = $wire.get('{{ $wireModel }}') || '';
     "
     class="relative">

    @if($label)
    <label for="{{ $id }}" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
        {{ $label }}
        @if($required)
            <span class="text-red-500">*</span>
        @endif
    </label>
    @endif

    <!-- Date Input Button -->
    <button
        type="button"
        @click="toggleCalendar"
        id="{{ $id }}"
        class="w-full px-4 py-3.5 border border-gray-300 dark:border-gray-600 rounded-xl text-sm focus:ring-2 focus:ring-purple-500 focus:border-purple-500 dark:bg-gray-700 dark:text-white bg-white text-left flex items-center justify-between min-h-[48px] hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors"
        :class="{ 'border-red-500': required && !selectedDate }"
    >
        <span x-text="selectedDate ? formatDate(selectedDate) : '{{ $placeholder }}'"
              :class="{ 'text-gray-500 dark:text-gray-400': !selectedDate, 'text-gray-900 dark:text-white': selectedDate }"
              class="truncate mr-2">
        </span>
        <svg class="w-5 h-5 text-purple-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2H5a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2z"/>
        </svg>
    </button>

    <!-- Calendar Dropdown -->
    <div x-show="showCalendar"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         @click.away="showCalendar = false"
         class="absolute z-50 mt-2 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-xl shadow-xl p-5 w-80 max-w-full left-0 right-0 mx-auto"
         style="transform: translateX(-50%); left: 50%;"
    >

        <!-- Calendar Header -->
        <div class="flex items-center justify-between mb-5">
            <button type="button" @click="previousMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                </svg>
            </button>
            <h3 class="text-base font-semibold text-gray-900 dark:text-white" x-text="monthName + ' ' + currentYear"></h3>
            <button type="button" @click="nextMonth" class="p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-full transition-colors">
                <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                </svg>
            </button>
        </div>

        <!-- Day Headers -->
        <div class="grid grid-cols-7 gap-1 mb-3">
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Pn</div>
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Wt</div>
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Śr</div>
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Cz</div>
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Pt</div>
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">So</div>
            <div class="py-2 text-center text-xs font-semibold text-gray-500 dark:text-gray-400">Nd</div>
        </div>

        <!-- Calendar Days -->
        <div class="grid grid-cols-7 gap-1">
            <template x-for="day in calendarDays" :key="day.dateString">
                <button
                    type="button"
                    @click="selectDate(day.dateString)"
                    :disabled="day.disabled"
                    class="relative p-2.5 text-sm rounded-lg transition-all duration-200 hover:bg-purple-50 dark:hover:bg-purple-900/20 min-h-[36px] flex items-center justify-center"
                    :class="{
                        'text-gray-400 dark:text-gray-600 cursor-not-allowed': day.disabled,
                        'text-gray-300 dark:text-gray-600': day.otherMonth && !day.disabled,
                        'text-gray-900 dark:text-white': !day.otherMonth && !day.disabled,
                        'bg-purple-600 text-white hover:bg-purple-700 shadow-md': day.selected,
                        'bg-blue-100 dark:bg-blue-900/30 text-blue-800 dark:text-blue-300 font-semibold': day.today && !day.selected,
                        'hover:bg-purple-100 dark:hover:bg-purple-900/30': !day.disabled && !day.selected
                    }"
                >
                    <span x-text="day.day" class="font-medium"></span>
                    <div x-show="day.today && !day.selected" class="absolute bottom-1 left-1/2 transform -translate-x-1/2 w-1.5 h-1.5 bg-blue-600 dark:bg-blue-400 rounded-full"></div>
                </button>
            </template>
        </div>

        <!-- Quick Actions -->
        <div class="mt-5 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex gap-3">
                <button
                    type="button"
                    @click="selectToday"
                    class="flex-1 px-4 py-2.5 text-sm font-medium bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors"
                >
                    Dziś
                </button>
                <button
                    type="button"
                    @click="clearDate"
                    class="flex-1 px-4 py-2.5 text-sm font-medium bg-gray-50 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                >
                    Wyczyść
                </button>
            </div>
        </div>
    </div>
</div>

<script>
function datePicker() {
    return {
        showCalendar: false,
        selectedDate: '',
        currentMonth: new Date().getMonth(),
        currentYear: new Date().getFullYear(),
        minDate: '',
        maxDate: '',
        required: false,
        wireModelName: '',

        get monthName() {
            const months = [
                'Styczeń', 'Luty', 'Marzec', 'Kwiecień', 'Maj', 'Czerwiec',
                'Lipiec', 'Sierpień', 'Wrzesień', 'Październik', 'Listopad', 'Grudzień'
            ];
            return months[this.currentMonth];
        },

        get calendarDays() {
            const days = [];
            const firstDay = new Date(this.currentYear, this.currentMonth, 1);
            const lastDay = new Date(this.currentYear, this.currentMonth + 1, 0);
            const today = new Date();
            const minDateObj = this.minDate ? new Date(this.minDate) : null;
            const maxDateObj = this.maxDate ? new Date(this.maxDate) : null;

            // Get first day of week (0 = Sunday, 1 = Monday, etc.)
            const startDay = firstDay.getDay();
            const mondayStart = startDay === 0 ? 6 : startDay - 1;

            // Add days from previous month
            for (let i = mondayStart - 1; i >= 0; i--) {
                const date = new Date(firstDay);
                date.setDate(date.getDate() - i - 1);
                days.push(this.createDayObject(date, true, minDateObj, maxDateObj, today));
            }

            // Add days from current month
            for (let day = 1; day <= lastDay.getDate(); day++) {
                const date = new Date(this.currentYear, this.currentMonth, day);
                days.push(this.createDayObject(date, false, minDateObj, maxDateObj, today));
            }

            // Add days from next month to fill the grid
            const remainingDays = 42 - days.length; // 6 weeks * 7 days
            for (let day = 1; day <= remainingDays; day++) {
                const date = new Date(this.currentYear, this.currentMonth + 1, day);
                days.push(this.createDayObject(date, true, minDateObj, maxDateObj, today));
            }

            return days.slice(0, 42); // Ensure exactly 6 weeks
        },

        createDayObject(date, otherMonth, minDateObj, maxDateObj, today) {
            const dateString = date.toISOString().split('T')[0];
            const isDisabled = (minDateObj && date < minDateObj) || (maxDateObj && date > maxDateObj);

            return {
                day: date.getDate(),
                dateString: dateString,
                otherMonth: otherMonth,
                disabled: isDisabled,
                selected: this.selectedDate === dateString,
                today: date.toDateString() === today.toDateString()
            };
        },

        toggleCalendar() {
            this.showCalendar = !this.showCalendar;
        },

        selectDate(dateString) {
            this.selectedDate = dateString;
            if (this.wireModelName) {
                this.$wire.set(this.wireModelName, dateString);
            }
            this.showCalendar = false;
        },

        selectToday() {
            const today = new Date().toISOString().split('T')[0];
            this.selectDate(today);
        },

        clearDate() {
            this.selectedDate = '';
            if (this.wireModelName) {
                this.$wire.set(this.wireModelName, '');
            }
            this.showCalendar = false;
        },

        formatDate(dateString) {
            if (!dateString) return '';
            const date = new Date(dateString);
            const options = {
                year: 'numeric',
                month: 'long',
                day: 'numeric'
            };
            return date.toLocaleDateString('pl-PL', options);
        },

        previousMonth() {
            if (this.currentMonth === 0) {
                this.currentMonth = 11;
                this.currentYear--;
            } else {
                this.currentMonth--;
            }
        },

        nextMonth() {
            if (this.currentMonth === 11) {
                this.currentMonth = 0;
                this.currentYear++;
            } else {
                this.currentMonth++;
            }
        },

        init() {
            // Watch for changes from Livewire
            this.$watch('selectedDate', (value) => {
                if (this.wireModelName) {
                    this.$wire.set(this.wireModelName, value);
                }
            });

            // Listen for updates from Livewire
            Livewire.on('refresh', () => {
                if (this.wireModelName) {
                    this.selectedDate = this.$wire.get(this.wireModelName) || '';
                }
            });
        }
    }
}
</script>