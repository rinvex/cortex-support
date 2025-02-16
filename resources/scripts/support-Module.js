import AlpineFloatingUI from '@awcodes/alpine-floating-ui'
import AlpineLazyLoadAssets from 'alpine-lazy-load-assets'
import Tooltip from '@ryangjchandler/alpine-tooltip';
import mousetrap from '@danharrin/alpine-mousetrap';
import { md5 } from 'js-md5';
import ui from '@alpinejs/ui';
import Sortable from './sortable'
import AsyncAlpine from 'async-alpine';
import Alpine from 'alpinejs';


// import '../../../panels/resources/styles/entryPoint.css';
// import '../styles/components/actions.css';
// import '../styles/components/avatar.css'
// import '../styles/components/badge.css'
// import '../styles/components/breadcrumbs.css'
// import '../styles/components/button.css'
// import '../styles/components/dropdown/header.css'
// import '../styles/components/dropdown/index.css'
// import '../styles/components/dropdown/list/index.css'
// import '../styles/components/dropdown/list/item.css'
// import '../styles/components/fieldset.css'
// import '../styles/components/grid.css'
// import '../styles/components/icon.css'
// import '../styles/components/icon-button.css'
// import '../styles/components/input/checkbox.css'
// import '../styles/components/input/index.css'
// import '../styles/components/input/one-time-code.css'
// import '../styles/components/input/radio.css'
// import '../styles/components/input/select.css'
// import '../styles/components/input/wrapper.css'
// import '../styles/components/link.css'
// import '../styles/components/loading-indicator.css'
// import '../styles/components/loading-section.css'
// import '../styles/components/modal.css'
// import '../styles/components/pagination.css'
// import '../styles/components/section.css'
// import '../styles/components/tables/actions.css'
// import '../styles/components/tables/cell.css'
// import '../styles/components/tables/columns/checkbox.css'
// import '../styles/components/tables/columns/color.css'
// import '../styles/components/tables/columns/icon.css'
// import '../styles/components/tables/columns/image.css'
// import '../styles/components/tables/columns/select.css'
// import '../styles/components/tables/columns/text.css'
// import '../styles/components/tables/columns/text-input.css'
// import '../styles/components/tables/columns/toggle.css'
// import '../styles/components/tables/columns/layout/grid.css'
// import '../styles/components/tables/columns/layout/panel.css'
// import '../styles/components/tables/columns/layout/split.css'
// import '../styles/components/tables/columns/layout/stack.css'
// import '../styles/components/tables/columns/summaries/icon-count.css'
// import '../styles/components/tables/columns/summaries/range.css'
// import '../styles/components/tables/columns/summaries/text.css'
// import '../styles/components/tables/columns/summaries/values.css'
// import '../styles/components/tables/container.css'
// import '../styles/components/tables/content.css'
// import '../styles/components/tables/empty-state.css'
// import '../styles/components/tables/header-cell.css'
// import '../styles/components/tables/row.css'
// import '../styles/components/tables/table.css'
// import '../styles/components/tabs.css'
// import '../styles/components/toggle.css'
// import '../styles/components/widgets/chart-widget.css'
// import '../styles/components/widgets/index.css'
// import '../styles/components/widgets/stats-overview-widget.css'

// import 'tippy.js/dist/tippy.css'
// import 'tippy.js/themes/light.css'
// import '../styles/sortable.css'

Alpine.plugin(AlpineFloatingUI);
Alpine.plugin(AlpineLazyLoadAssets);
Alpine.plugin(Tooltip);
Alpine.plugin(mousetrap);
Alpine.plugin(ui);
Alpine.plugin(Sortable);
Alpine.plugin(AsyncAlpine);

// Dynamic alpine plugins
const alpinePlugins = import.meta.glob('@appPath/*/*/resources/scripts/*-AlpinePlugin.js');
await Promise.all(Object.entries(alpinePlugins).map(([path, alpinePlugin]) => alpinePlugin()
    .then(alpinePlugin => Alpine.plugin(alpinePlugin.default))
    .catch(error => console.error(`Failed to load module at path: ${path}`, error))));

// Dynamic async alpine plugins
const asyncAlpinePlugins = import.meta.glob([
    '@appPath/*/*/resources/scripts/components/*.js',
    '@appPath/cortex/tables/resources/scripts/components/columns/*.js'
]);
await Promise.all(Object.entries(asyncAlpinePlugins).map(([path, asyncAlpinePlugin]) => asyncAlpinePlugin()
    .then(module => {
        // Register the module with Alpine Async
        const asyncDataName = Object.keys(module)[0];
        Alpine.asyncData(asyncDataName, () => module[asyncDataName]);
    })
    .catch(error => console.error(`Failed to load module at path: ${path}`, error))));

const findClosestLivewireComponent = (el) => {
    let closestRoot = Alpine.findClosest(el, (i) => i.__livewire)

    if (!closestRoot) {
        throw 'Could not find Livewire component in DOM tree'
    }

    return closestRoot.__livewire
}

Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
    respond(() => {
        queueMicrotask(() => {
            if (component.effects.html) {
                return
            }

            for (const [name, html] of Object.entries(
                component.effects.partials ?? {},
            )) {
                let els = Array.from(
                    component.el.querySelectorAll(
                        `[wire\\:partial="${name}"]`,
                    ),
                ).filter(
                    (el) => findClosestLivewireComponent(el) === component,
                )

                if (!els.length) {
                    continue
                }

                if (els.length > 1) {
                    throw `Multiple elements found for partial [${name}].`
                }

                let el = els[0]

                let wrapperTag = el.parentElement
                    ? // If the root element is a "tr", we need the wrapper to be a "table"...
                      el.parentElement.tagName.toLowerCase()
                    : 'div'

                let wrapper = document.createElement(wrapperTag)

                wrapper.innerHTML = html
                wrapper.__livewire = component

                let to = wrapper.firstElementChild

                to.__livewire = component

                window.Alpine.morph(el, to, {
                    updating: (el, toEl, childrenOnly, skip) => {
                        if (isntElement(el)) {
                            return
                        }

                        if (el.__livewire_replace === true) {
                            el.innerHTML = toEl.innerHTML
                        }

                        if (el.__livewire_replace_self === true) {
                            el.outerHTML = toEl.outerHTML

                            return skip()
                        }

                        if (el.__livewire_ignore === true) {
                            return skip()
                        }

                        if (el.__livewire_ignore_self === true) {
                            childrenOnly()
                        }

                        if (
                            isComponentRootEl(el) &&
                            el.getAttribute('wire:id') !== component.id
                        ) {
                            return skip()
                        }

                        if (isComponentRootEl(el)) {
                            toEl.__livewire = component
                        }
                    },

                    key: (el) => {
                        if (isntElement(el)) {
                            return
                        }

                        if (el.hasAttribute(`wire:key`)) {
                            return el.getAttribute(`wire:key`)
                        }

                        if (el.hasAttribute(`wire:id`)) {
                            return el.getAttribute(`wire:id`)
                        }

                        return el.id
                    },

                    lookahead: false,
                })
            }
        })
    })

    function isntElement(el) {
        return typeof el.hasAttribute !== 'function'
    }

    function isComponentRootEl(el) {
        return el.hasAttribute('wire:id')
    }
})

Livewire.hook('commit', ({ component, commit, respond, succeed, fail }) => {
    succeed(({ snapshot, effects }) => {
        effects.dispatches?.forEach((dispatch) => {
            if (!dispatch.params?.awaitSchemaComponent) {
                return
            }

            let els = Array.from(
                component.el.querySelectorAll(
                    `[wire\\:partial="schema-component::${dispatch.params.awaitSchemaComponent}"]`,
                ),
            ).filter((el) => findClosestLivewireComponent(el) === component)

            if (els.length === 1) {
                return
            }

            if (els.length > 1) {
                throw `Multiple schema components found with key [${dispatch.params.awaitSchemaComponent}].`
            }

            window.addEventListener(
                `schema-component-${component.id}-${dispatch.params.awaitSchemaComponent}-loaded`,
                () => {
                    window.dispatchEvent(
                        new CustomEvent(dispatch.name, {
                            detail: dispatch.params,
                        }),
                    )
                },
                { once: true },
            )
        })
    })
});

// https://github.com/laravel/framework/blob/5299c22321c0f1ea8ff770b84a6c6469c4d6edec/src/Illuminate/Translation/MessageSelector.php#L15
const pluralize = function (text, number, variables) {
    function extract(segments, number) {
        for (const part of segments) {
            const line = extractFromString(part, number)

            if (line !== null) {
                return line
            }
        }
    }

    function extractFromString(part, number) {
        const matches = part.match(/^[\{\[]([^\[\]\{\}]*)[\}\]](.*)/s)

        if (matches === null || matches.length !== 3) {
            return null
        }

        const condition = matches[1]

        const value = matches[2]

        if (condition.includes(',')) {
            const [from, to] = condition.split(',', 2)

            if (to === '*' && number >= from) {
                return value
            } else if (from === '*' && number <= to) {
                return value
            } else if (number >= from && number <= to) {
                return value
            }
        }

        return condition == number ? value : null
    }

    function ucfirst(string) {
        return (
            string.toString().charAt(0).toUpperCase() +
            string.toString().slice(1)
        )
    }

    function replace(line, replace) {
        if (replace.length === 0) {
            return line
        }

        const shouldReplace = {}

        for (let [key, value] of Object.entries(replace)) {
            shouldReplace[':' + ucfirst(key ?? '')] = ucfirst(value ?? '')
            shouldReplace[':' + key.toUpperCase()] = value
                .toString()
                .toUpperCase()
            shouldReplace[':' + key] = value
        }

        Object.entries(shouldReplace).forEach(([key, value]) => {
            line = line.replaceAll(key, value)
        })

        return line
    }

    function stripConditions(segments) {
        return segments.map((part) =>
            part.replace(/^[\{\[]([^\[\]\{\}]*)[\}\]]/, ''),
        )
    }

    let segments = text.split('|')

    const value = extract(segments, number)

    if (value !== null && value !== undefined) {
        return replace(value.trim(), variables)
    }

    segments = stripConditions(segments)

    return replace(
        segments.length > 1 && number > 1 ? segments[1] : segments[0],
        variables,
    )
}

window.jsMd5 = md5
window.pluralize = pluralize
