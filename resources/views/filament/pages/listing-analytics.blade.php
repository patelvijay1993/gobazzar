<x-filament-panels::page>
    <x-filament-widgets::widgets
        :widgets="$this->getHeaderWidgets()"
        :columns="$this->getHeaderWidgetsColumns()"
        :data="['record' => $this->record]"
    />

    {{ $this->infolist }}
</x-filament-panels::page>
