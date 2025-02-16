export function cortexFormButton() {
    return {
        form: null,

        isProcessing: false,

        processingMessage: null,

        init: function () {
            const formElement = this.$el.closest('form')

            formElement?.addEventListener('form-processing-started', (event) => {
                this.isProcessing = true
                this.processingMessage = event.detail.message
            })

            formElement?.addEventListener('form-processing-finished', () => {
                this.isProcessing = false
            })
        },
    };
}
