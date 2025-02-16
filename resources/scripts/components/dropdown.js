export function cortexDropdown() {
    return {
        toggle: function (event) {
            this.$refs.panel.toggle(event)
        },

        open: function (event) {
            this.$refs.panel.open(event)
        },

        close: function (event) {
            this.$refs.panel.close(event)
        },
    };
}
