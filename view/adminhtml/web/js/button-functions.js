require([
    'jquery',
    'Magento_Ui/js/modal/modal',
    'loader'
], function ($, modal) {

    /**
     * @param{String} modalSelector - modal css selector.
     * @param{Object} options - modal options.
     */
    function initModal(modalSelector, options) {

        if (!$(modalSelector).length) return;

        let defaultOptions = {
            modalClass: 'mm-ui-modal',
            type: 'popup',
            responsive: true,
            innerScroll: true,
            title: options.title || '',
            buttons: [
                {
                    text: $.mage.__('Close window'),
                    class: 'action primary',
                    click: function () {
                        this.closeModal();
                    },
                }
            ]
        };

        // Additional buttons for downloading
        if (options.buttons) {
            let additionalButtons = {
                text: $.mage.__('download as .txt file'),
                class: 'mm-ui-button__download mm-ui-icon__download-alt',
                click: () => {
                    let elText = document.getElementById(`mm-ui-result_${options.buttons}`).innerText || '',
                        link = document.createElement('a');

                    link.setAttribute('download', `${options.buttons}-log.txt`);
                    link.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(elText));
                    link.click();
                },
            };

            defaultOptions.buttons.unshift(additionalButtons);
        }

        modal(defaultOptions, $(modalSelector));
        $(modalSelector).loader({texts: ''});
    }

    var successHandlers = {
        /**
         * @param{Object[]} result - Ajax request response data.
         * @param{Object} $container - jQuery container element.
         */
        logs: function (data, $container, action) {
            let blockClass = action === 'debug' ? 'result' : 'error',
                result = data;

            if (Array.isArray(data)) {
                result = `<ul>
                            ${data.map((err) => {
                    return `<li class="mm-ui-ui-${blockClass}_debug-item">
                                            <strong>${err.date}</strong>
                                            <p>${err.msg}</p>
                                        </li>`;
                }).join('')}
                        </ul>`;
            }

            $container[0].querySelector('.result').innerHTML = result;
        },
    }

    // init debug modal
    $(() => {
        initModal('#mm-ui-result_debug-modal', {title: $.mage.__('Last 50 debug log lines'), buttons: 'debug'});
        initModal('#mm-ui-result_error-modal', {title: $.mage.__('Last 50 error log records'), buttons: 'error'});
    });

    /**
     * Ajax request event
     */
    $(document).on('click', '[id^=vendiro-button]', function () {
        var actionName = this.id.split('_')[1];
        var $modal = $(`#mm-ui-result_${actionName}-modal`);
        var $result = $(`#mm-ui-result_${actionName}`);

        $modal.modal('openModal').loader('show');
        $result.hide();

        fetch($modal.attr('data-mm-ui-endpoind-url'))
            .then((res) => res.json())
            .then((json) => {
                successHandlers['logs'](json.result, $result, actionName);
                $result.fadeIn();
                $modal.loader('hide');
            });
    });
});
