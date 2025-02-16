@props([
    'currentPageOptionProperty' => 'tableRecordsPerPage',
    'extremeLinks' => false,
    'paginator',
    'pageOptions' => [],
])

@php
    use Illuminate\Contracts\Pagination\CursorPaginator;

    $isRtl = __('cortex.panels::layout.direction') === 'rtl';
    $isSimple = ! $paginator instanceof \Illuminate\Pagination\LengthAwarePaginator;
@endphp

<nav
    aria-label="{{ __('cortex.support::components/pagination.label') }}"
    role="navigation"
    {{
        $attributes->class([
            'fi-pagination',
            'fi-simple' => $isSimple,
        ])
    }}
>
    @if (! $paginator->onFirstPage())
        @php
            if ($paginator instanceof CursorPaginator) {
                $wireClickAction = "setPage('{$paginator->previousCursor()->encode()}', '{$paginator->getCursorName()}')";
            } else {
                $wireClickAction = "previousPage('{$paginator->getPageName()}')";
            }
        @endphp

        <x-cortex.support::button
            color="secondary"
            rel="prev"
            :wire:click="$wireClickAction"
            :wire:key="$this->getId() . '.pagination.previous'"
            class="fi-pagination-previous-btn"
        >
            {{ __('cortex.support::components/pagination.actions.previous.label') }}
        </x-cortex.support::button>
    @endif

    @if (! $isSimple)
        <span class="fi-pagination-overview">
            {{
                trans_choice(
                    'cortex.support::components/pagination.overview',
                    $paginator->total(),
                    [
                        'first' => \Illuminate\Support\Number::format($paginator->firstItem() ?? 0),
                        'last' => \Illuminate\Support\Number::format($paginator->lastItem() ?? 0),
                        'total' => \Illuminate\Support\Number::format($paginator->total()),
                    ],
                )
            }}
        </span>
    @endif

    @if (count($pageOptions) > 1)
        <div class="fi-pagination-records-per-page-select-ctn">
            <label class="fi-pagination-records-per-page-select fi-compact">
                <x-cortex.support::input.wrapper>
                    <x-cortex.support::input.select
                        :wire:model.live="$currentPageOptionProperty"
                    >
                        @foreach ($pageOptions as $option)
                            <option value="{{ $option }}">
                                {{ $option === 'all' ? __('cortex.support::components/pagination.fields.records_per_page.options.all') : $option }}
                            </option>
                        @endforeach
                    </x-cortex.support::input.select>
                </x-cortex.support::input.wrapper>

                <span class="sr-only">
                    {{ __('cortex.support::components/pagination.fields.records_per_page.label') }}
                </span>
            </label>

            <label class="fi-pagination-records-per-page-select">
                <x-cortex.support::input.wrapper
                    :prefix="__('cortex.support::components/pagination.fields.records_per_page.label')"
                >
                    <x-cortex.support::input.select
                        :wire:model.live="$currentPageOptionProperty"
                    >
                        @foreach ($pageOptions as $option)
                            <option value="{{ $option }}">
                                {{ $option === 'all' ? __('cortex.support::components/pagination.fields.records_per_page.options.all') : $option }}
                            </option>
                        @endforeach
                    </x-cortex.support::input.select>
                </x-cortex.support::input.wrapper>
            </label>
        </div>
    @endif

    @if ($paginator->hasMorePages())
        @php
            if ($paginator instanceof CursorPaginator) {
                $wireClickAction = "setPage('{$paginator->nextCursor()->encode()}', '{$paginator->getCursorName()}')";
            } else {
                $wireClickAction = "nextPage('{$paginator->getPageName()}')";
            }
        @endphp

        <x-cortex.support::button
            color="secondary"
            rel="next"
            :wire:click="$wireClickAction"
            :wire:key="$this->getId() . '.pagination.next'"
            class="fi-pagination-next-btn"
        >
            {{ __('cortex.support::components/pagination.actions.next.label') }}
        </x-cortex.support::button>
    @endif

    @if ((! $isSimple) && $paginator->hasPages())
        <ol class="fi-pagination-items">
            @if (! $paginator->onFirstPage())
                @if ($extremeLinks)
                    <x-cortex.support::pagination.item
                        :aria-label="__('cortex.support::components/pagination.actions.first.label')"
                        :icon="$isRtl ? \Cortex\Support\Icons\Heroicon::ChevronDoubleRight : \Cortex\Support\Icons\Heroicon::ChevronDoubleLeft"
                        :icon-alias="$isRtl ? 'pagination.first-button.rtl' : 'pagination.first-button'"
                        rel="first"
                        :wire:click="'gotoPage(1, \'' . $paginator->getPageName() . '\')'"
                        :wire:key="$this->getId() . '.pagination.first'"
                    />
                @endif

                <x-cortex.support::pagination.item
                    :aria-label="__('cortex.support::components/pagination.actions.previous.label')"
                    :icon="$isRtl ? \Cortex\Support\Icons\Heroicon::ChevronRight : \Cortex\Support\Icons\Heroicon::ChevronLeft"
                    :icon-alias="$isRtl ? 'pagination.previous-button.rtl' : 'pagination.previous-button'"
                    rel="prev"
                    :wire:click="'previousPage(\'' . $paginator->getPageName() . '\')'"
                    :wire:key="$this->getId() . '.pagination.previous'"
                />
            @endif

            @foreach ($paginator->render()->offsetGet('elements') as $element)
                @if (is_string($element))
                    <x-cortex.support::pagination.item disabled :label="$element" />
                @endif

                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        <x-cortex.support::pagination.item
                            :active="$page === $paginator->currentPage()"
                            :aria-label="trans_choice('cortex.support::components/pagination.actions.go_to_page.label', $page, ['page' => $page])"
                            :label="$page"
                            :wire:click="'gotoPage(' . $page . ', \'' . $paginator->getPageName() . '\')'"
                            :wire:key="$this->getId() . '.pagination.' . $paginator->getPageName() . '.' . $page"
                        />
                    @endforeach
                @endif
            @endforeach

            @if ($paginator->hasMorePages())
                <x-cortex.support::pagination.item
                    :aria-label="__('cortex.support::components/pagination.actions.next.label')"
                    :icon="$isRtl ? 'heroicon-m-chevron-left' : 'heroicon-m-chevron-right'"
                    :icon-alias="$isRtl ? 'pagination.next-button.rtl' : 'pagination.next-button'"
                    rel="next"
                    :wire:click="'nextPage(\'' . $paginator->getPageName() . '\')'"
                    :wire:key="$this->getId() . '.pagination.next'"
                />

                @if ($extremeLinks)
                    <x-cortex.support::pagination.item
                        :aria-label="__('cortex.support::components/pagination.actions.last.label')"
                        :icon="$isRtl ? \Cortex\Support\Icons\Heroicon::ChevronDoubleLeft : \Cortex\Support\Icons\Heroicon::ChevronDoubleRight"
                        :icon-alias="$isRtl ? 'pagination.last-button.rtl' : 'pagination.last-button'"
                        rel="last"
                        :wire:click="'gotoPage(' . $paginator->lastPage() . ', \'' . $paginator->getPageName() . '\')'"
                        :wire:key="$this->getId() . '.pagination.last'"
                    />
                @endif
            @endif
        </ol>
    @endif
</nav>
