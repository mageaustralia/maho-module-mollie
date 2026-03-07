class MollieComponents {
    constructor(profileId, testmode, locale) {
        this.components = {};
        this.parentSave = null;

        try {
            this.mollie = Mollie(profileId, {
                testMode: testmode,
                locale: locale
            });

            this.components.cardHolder = this.mollie.createComponent('cardHolder');
            this.components.cardNumber = this.mollie.createComponent('cardNumber');
            this.components.expiryDate = this.mollie.createComponent('expiryDate');
            this.components.verificationCode = this.mollie.createComponent('verificationCode');
        } catch (error) {
            console.error(error);
        }
    }

    getMollieToken() {
        const checked = document.querySelector('input:checked[name="payment[method]"]');
        if (!checked || checked.value !== 'mollie_creditcard') {
            return this.parentSave();
        }

        this.mollie.createToken().then((result) => {
            if (result.error) {
                alert(result.error.message);
                return false;
            }

            const tokenField = document.querySelector('[name=cardToken]');
            if (result.token && tokenField) {
                tokenField.removeAttribute('disabled');
                tokenField.value = result.token;
            }

            this.parentSave();
        });
    }

    mount() {
        payment.addAfterInitFunction('mollie_components', () => {
            this.parentSave = payment.save.bind(payment);
            payment.save = this.getMollieToken.bind(this);
        });

        this.mountElement(this.components.cardHolder, '#card-holder');
        this.mountElement(this.components.cardNumber, '#card-number');
        this.mountElement(this.components.expiryDate, '#expiry-date');
        this.mountElement(this.components.verificationCode, '#verification-code');
    }

    mountElement(element, id) {
        element.mount(id);

        const errorElement = document.querySelector(id + '-error');
        element.addEventListener('change', (event) => {
            if (event.error && event.touched) {
                errorElement.textContent = event.error;
                errorElement.style.display = 'block';
            } else {
                errorElement.textContent = '';
                errorElement.style.display = 'none';
            }
        });
    }
}
