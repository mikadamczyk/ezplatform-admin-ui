(function (global, doc) {
    const form = doc.querySelector('.ez-form-validate');
    const submitBtns = form.querySelectorAll('[type="submit"]:not([formnovalidate])');
    const SELECTOR_INNER_FIELD = '.ez-field-edit';
    const SELECTOR_LABEL = '.ez-field-edit__label';
    const SELECTOR_LABEL_WRAPPER = '.ez-field-edit__label-wrapper';
    const classInvalid ='is-invalid';

    /**
     * Creates an error node
     *
     * @method createErrorNode
     * @param {String} message
     * @returns {HTMLElement}
     * @memberof BaseFieldValidator
     */
    const createErrorNode = (message) => {
        const errorNode = doc.createElement('em');

        errorNode.classList.add('ez-field-edit__error');
        errorNode.innerHTML = message;

        return errorNode;
    }

    /**
     * Toggles the invalid state
     *
     * @method toggleInvalidState
     * @param {Boolean} isError
     * @param {Object} config
     * @param {HTMLElement} input
     * @memberof BaseFieldValidator
     */
    const toggleInvalidState = (isError, fieldContainer, input) => {
        const methodName = isError ? 'add' : 'remove';
        fieldContainer.classList[methodName](classInvalid);
        input.classList[methodName](classInvalid);
    }

    /**
     * Toggles the error message
     *
     * @method toggleErrorMessage
     * @param {Object} validationResult
     * @param {Object} config
     * @param {HTMLElement} input
     * @memberof BaseFieldValidator
     */
    const toggleErrorMessage = (isError, fieldContainer) => {
        const labelWrapper = fieldContainer.querySelector(SELECTOR_LABEL_WRAPPER);
        const errorNodes = [...labelWrapper.querySelectorAll('.ez-field-edit__error')];
        errorNodes.forEach(el => el.remove());

        if (isError) {
            const errorMessage = global.eZ.errors.emptyField.replace('{fieldName}', fieldContainer.querySelector(SELECTOR_LABEL).innerHTML);
            const errorNode = createErrorNode(errorMessage);
            labelWrapper.append(errorNode);
        }
    }

    const validate = (target) => {
        const isRequired = target.required;
        const isEmpty = !target.value.trim();
        const isError = (isRequired && isEmpty);
        const fieldContainer = target.closest(SELECTOR_INNER_FIELD);
        toggleInvalidState(isError, fieldContainer, target);
        toggleErrorMessage(isError, fieldContainer);

        return !isError;
    }

    form.setAttribute('novalidate', true);

    submitBtns.forEach(btn => {
        const clickHandler = (event) => {
            if (!parseInt(btn.dataset.isFormValid, 10)) {
                event.preventDefault();
                const requiredFields = [...form.querySelectorAll('.ez-field-edit input')];
                const isFormValid = requiredFields.map(validate).every(result => result);

                if (isFormValid) {
                    btn.dataset.isFormValid = 1;
                    // for some reason trying to fire click event inside the event handler flow is impossible
                    // the following line breaks the flow so it's possible to fire click event on a button again.
                    window.setTimeout(() => btn.click(), 0);
                }
            }
        };
        btn.dataset.isFormValid = 0;
        btn.addEventListener('click', clickHandler, false);
    });
})(window, document);