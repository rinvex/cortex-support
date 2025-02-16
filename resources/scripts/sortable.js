export default (Alpine) => {
    Alpine.directive('sortable', (el) => {
        let animation = parseInt(el.dataset?.sortableAnimationDuration)

        if (animation !== 0 && !animation) {
            animation = 300
        }

        // Dynamically import the Sortable module when the directive is called
        import('sortablejs').then(({default: Sortable}) => {
            // Initialize Sortable with the element and options
            window.Sortable = Sortable

            el.sortable = Sortable.create(el, {
                group: el.getAttribute('x-sortable-group'),
                draggable: '[x-sortable-item]',
                handle: '[x-sortable-handle]',
                dataIdAttr: 'x-sortable-item',
                animation: animation,
                ghostClass: 'fi-sortable-ghost',
            });
        }).catch(error => {
            console.error('Failed to load sortablejs module', error);
        });
    });
}
