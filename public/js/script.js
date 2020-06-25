var stripe = Stripe('pk_test_93HttH8banovfqTSiF8XG0I200BhANxI2C');
var elements = stripe.elements();


function stripePaymentMethodHandler(result, email) {
    if (result.error) {
        // Show error in payment form
    } else {
        console.log(result);
        document.getElementById('{{ form.children.token.vars.id }}').setAttribute('value', result.token.id);
        form.submit();

    }
}

