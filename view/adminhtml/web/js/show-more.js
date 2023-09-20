require([
    'jquery',
    'mage/translate',
    '!domReady'
], function ($, $t) {

    const COMMENT = Array.from($('form[action*="vendiro"] .mm-ui-heading-comment'));

    if(COMMENT.length) {
        COMMENT.forEach((item) => {
            let showMoreLessBtnHtml = document.createElement("div");

            showMoreLessBtnHtml.classList.add('mm-ui-show-more-actions');
            showMoreLessBtnHtml.innerHTML = `
                    <span class="mm-ui-show-btn-more">
                        ${$t('Show more.')}
                    </span>`;

            item.parentElement.appendChild(showMoreLessBtnHtml);
            checkShowMoreVisibility(item);
        });

        $(document).on('click', '.mm-ui-show-more-actions span', (e) => {
            let button = e.target,
                parent = e.target.closest('.value').querySelector('.mm-ui-heading-comment');

            if (parent.classList.contains('show')) {
                parent.classList.remove('show');
                button.innerHTML = $t('Show more.');
            } else {
                parent.classList.add('show');
                button.innerHTML = $t('Show less.');
            }
        });

        window.onresize = () => {
            COMMENT.forEach((item) => checkShowMoreVisibility(item));
        }
    }

    // Check if 'Show more' need to display
    function checkShowMoreVisibility(text) {
        let sHeight = text.scrollHeight,
            cHeight = text.clientHeight,
            button  = text.parentElement.querySelector('.mm-ui-show-more-actions');

        cHeight >= sHeight ? button.classList.add('hidden') : button.classList.remove('hidden');
    }
});
