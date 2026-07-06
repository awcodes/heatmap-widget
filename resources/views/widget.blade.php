<x-filament-widgets::widget>
    <x-filament::section :heading="$this->getHeading()">
        <div
            x-data="{
                loading: true,
                weeks: [],
                monthLabels: {},
                thresholds: [1, 2, 3],
                cellSize: 0,
                gap: 2,
                labelWidth: 30,
                numWeeks: @js($this->weeks),
                unit: @js($this->unit),
                color: @js($this->color),
                weekStartsOn: @js($this->weekStartsOn),
                dayLabels: @js($this->getDayLabels()),
                isDark: document.documentElement.classList.contains('dark'),
                tooltip: { show: false, text: '', x: 0, y: 0 },
                lightShades: [100, 200, 400, 500, 700],
                darkShades: [800, 900, 700, 500, 400],
                async init() {
                    new MutationObserver(() => {
                        this.isDark = document.documentElement.classList.contains('dark');
                    }).observe(document.documentElement, { attributes: true, attributeFilter: ['class'] });

                    const result = await $wire.getHeatmapData();
                    this.buildGrid(result.data, result.max);
                    this.calculateCellSize();
                    this.loading = false;
                },
                calculateCellSize() {
                    const containerWidth = this.$refs.grid.clientWidth;
                    const weekCount = this.weeks.length;
                    const availableWidth = containerWidth - this.labelWidth - (this.gap * (weekCount - 1));
                    this.cellSize = Math.floor(availableWidth / weekCount);
                },
                buildGrid(data, max) {
                    const now = new Date();
                    const start = new Date(now);
                    start.setDate(start.getDate() - (this.numWeeks * 7));
                    const dayOfWeek = start.getDay();
                    // Calculate offset to align with the configured week start (0 = Sunday, 1 = Monday, etc.)
                    const offset = (dayOfWeek - this.weekStartsOn + 7) % 7;
                    start.setDate(start.getDate() - offset);

                    this.thresholds = max > 0
                        ? [Math.ceil(max * 0.25), Math.ceil(max * 0.50), Math.ceil(max * 0.75)]
                        : [1, 2, 3];

                    const weeks = [];
                    const monthLabels = {};
                    const current = new Date(start);
                    let weekIndex = 0;

                    while (current <= now) {
                        if (current.getDate() <= 7 || weekIndex === 0) {
                            monthLabels[weekIndex] = current.toLocaleDateString('en-US', { month: 'short' });
                        }

                        const week = [];
                        for (let day = 0; day < 7; day++) {
                            const date = new Date(current);
                            date.setDate(date.getDate() + day);

                            if (date > now) {
                                week.push(null);
                            } else {
                                const dateKey = date.toISOString().split('T')[0];
                                const count = data[dateKey] ?? 0;
                                week.push({
                                    date: dateKey,
                                    count: count,
                                    label: date.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }),
                                });
                            }
                        }

                        weeks.push(week);
                        current.setDate(current.getDate() + 7);
                        weekIndex++;
                    }

                    this.weeks = weeks;
                    this.monthLabels = monthLabels;
                },
                cellBg(count) {
                    const shades = this.isDark ? this.darkShades : this.lightShades;
                    const name = count === 0 ? 'gray' : this.color;

                    let level = 0;
                    if (count > 0 && count <= this.thresholds[0]) level = 1;
                    else if (count > 0 && count <= this.thresholds[1]) level = 2;
                    else if (count > 0 && count <= this.thresholds[2]) level = 3;
                    else if (count > 0) level = 4;

                    return `background-color: var(--${name}-${shades[level]})`;
                },
                legendBg(level) {
                    const shades = this.isDark ? this.darkShades : this.lightShades;
                    const name = level === 0 ? 'gray' : this.color;

                    return `background-color: var(--${name}-${shades[level]})`;
                },
                showTooltip(event, text) {
                    const rect = this.$refs.grid.getBoundingClientRect();
                    this.tooltip.text = text;
                    this.tooltip.x = event.clientX - rect.left;
                    this.tooltip.y = event.clientY - rect.top - 30;
                    this.tooltip.show = true;
                },
                hideTooltip() {
                    this.tooltip.show = false;
                },
            }"
            x-on:resize.window.debounce.150ms="calculateCellSize()"
            class="relative"
            x-ref="grid"
        >
            {{-- Loading state --}}
            <div x-show="loading" class="flex items-center justify-center py-8">
                <x-filament::loading-indicator class="h-6 w-6 text-gray-400" />
            </div>

            {{-- Heatmap content --}}
            <div x-show="!loading" x-cloak>
                {{-- Month labels --}}
                <div class="mb-1 flex text-xs text-gray-400 dark:text-gray-500" :style="`padding-left: ${labelWidth}px`">
                    <template x-for="(week, weekIdx) in weeks" :key="weekIdx">
                        <div :style="`width: ${cellSize + gap}px; min-width: ${cellSize + gap}px;`" class="text-center">
                            <span x-show="monthLabels[weekIdx]" class="relative -left-1" x-text="monthLabels[weekIdx]"></span>
                        </div>
                    </template>
                </div>

                <div class="flex" :style="`gap: ${gap}px`">
                    {{-- Day labels --}}
                    <div class="flex flex-col pr-1 text-xs text-gray-400 dark:text-gray-500" :style="`width: ${labelWidth}px; gap: ${gap}px`">
                        <template x-for="(label, index) in dayLabels" :key="index">
                            <div class="flex items-center justify-end" :style="`height: ${cellSize}px`">
                                <span x-text="label"></span>
                            </div>
                        </template>
                    </div>

                    {{-- Heatmap grid --}}
                    <div class="flex" :style="`gap: ${gap}px`">
                        <template x-for="(week, weekIdx) in weeks" :key="weekIdx">
                            <div class="flex flex-col" :style="`gap: ${gap}px`">
                                <template x-for="(dayData, dayIdx) in week" :key="weekIdx + '-' + dayIdx">
                                    <div>
                                        <div
                                            x-show="dayData !== null"
                                            class="cursor-pointer rounded-sm"
                                            :style="`width: ${cellSize}px; height: ${cellSize}px; ${dayData ? cellBg(dayData.count) : ''}`"
                                            @mouseenter="dayData && showTooltip($event, dayData.count + (unit ? ' ' + unit : '') + ' on ' + dayData.label)"
                                            @mouseleave="hideTooltip()"
                                        ></div>
                                        <div
                                            x-show="dayData === null"
                                            :style="`width: ${cellSize}px; height: ${cellSize}px;`"
                                        ></div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                {{-- Legend --}}
                <div class="mt-2 flex items-center justify-end gap-1 text-xs text-gray-400 dark:text-gray-500">
                    <span>Less</span>
                    <template x-for="level in [0, 1, 2, 3, 4]" :key="level">
                        <div class="h-3 w-3 rounded-sm" :style="legendBg(level)"></div>
                    </template>
                    <span>More</span>
                </div>
            </div>

            {{-- Tooltip --}}
            <div
                x-show="tooltip.show"
                x-transition.opacity.duration.150ms
                :style="`left: ${tooltip.x}px; top: ${tooltip.y}px;`"
                class="pointer-events-none absolute z-10 whitespace-nowrap rounded bg-gray-900 px-2 py-1 text-xs text-white shadow-lg dark:bg-gray-100 dark:text-gray-900"
                x-text="tooltip.text"
                x-cloak
            ></div>
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
