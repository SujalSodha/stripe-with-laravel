<style>
    #payment-form {
        max-width: 500px;
        margin: 0 auto;
    }

    #payment-element {
        margin-bottom: 16px;
    }

    button#submit {
        width: 100%;
        padding: 12px;
        background-color: #1a73e8;
        color: white;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }

    button#submit:hover {
        background-color: #166ad8;
    }
</style>
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <label for="payment_method" class="block text-sm font-medium text-gray-700 dark:text-gray-200">Select Payment
                Method</label>
            <select id="payment_method" name="payment_method" onchange="onSelectChange(this)"
                class="mt-1 block w-full p-3 border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-800 rounded-md shadow-sm">
                @foreach ($cust_payment_methods as $payment_method)
                    <option value="{{ $payment_method['id'] }}">
                        **** **** **** {{ $payment_method['last4'] }}
                    </option>
                @endforeach
            </select>
            {{-- @dd($paymentMethods); --}}
            <div class="mt-3">
                <div>
                    <input type="text" id="saved-name" class="w-full rounded" readonly
                        value="{{ $paymentMethods->data[0]->billing_details->name }}">
                </div>
                <div class="flex justify-between my-4">
                    <input type="text" id="saved-last4" class="rounded" readonly
                        value="{{ '**** **** **** ' . $paymentMethods->data[0]->card->last4 }}">
                    <input type="text" id="saved-exp" class="rounded" readonly
                        value="{{ $paymentMethods->data[0]->card->exp_month . '/' . $paymentMethods->data[0]->card->exp_year }}">
                </div>
                <input type="hidden" name="only_pmid" value="{{ $paymentMethods->data[0]->id }}">
            </div>

            <button type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 mt-3"
                id="addNewCard">Add new card</button>

            <button type="button"
                class="text-white bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 me-2 mb-2 dark:bg-blue-600 dark:hover:bg-blue-700 focus:outline-none dark:focus:ring-blue-800 mt-3"
                id="make-payment">Make Payment</button>


            <div class="mt-8 bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm hidden" id="toggelCard">
                <form id="payment-form" class="space-y-4">
                    <div>
                        <label for="cardholder-name" class="block text-sm font-medium text-gray-700">Cardholder
                            Name</label>
                        <input type="text" id="cardholder-name" name="cardholder-name" required
                            class="mt-1 block w-full p-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div id="payment-element"></div>
                    <button id="submit"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500">Submit</button>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>

<script src="https://js.stripe.com/v3/"></script>
<script type="module">
    var paymentMethods = {!! json_encode($paymentMethods) !!}

    window.onSelectChange = function(element) {
        paymentMethods.data.forEach(item => {
            if (item.id == element.value) {
                document.getElementById('saved-name').value = item.billing_details.name;
                document.getElementById('saved-exp').value = item.card.exp_month + '/' + item.card.exp_year;
                document.getElementById('saved-last4').value = '**** **** **** ' + item.card.last4;
            }
        });
    }

    document.addEventListener("DOMContentLoaded", async (event) => {
        const stripe = Stripe('{{ config('app_cus\tom.STRIPE_KEY') }}');

        const appearance = {
            theme: 'flat',
            variables: {
                colorPrimaryText: '#262626'
            }
        };

        const elements = stripe.elements({
            appearance,
        });

        const options = {
            hidePostalCode: true,
            layout: {
                type: 'tabs',
                defaultCollapsed: false,
            }
        };

        const paymentElement = elements.create('card', options);
        paymentElement.mount('#payment-element');

        const form = document.getElementById('payment-form');
        const submitButton = document.getElementById('submit');

        form.addEventListener('submit', function(event) {
            event.preventDefault();
            stripe.createToken(paymentElement).then(function(result) {
                if (result.error) {
                    var errorElement = document.getElementById('card-errors');
                    errorElement.textContent = result.error.message;
                } else {
                    stripeTokenHandler(result.token);
                }
            });
        });

        function stripeTokenHandler(token) {
            $.ajax({
                url: "{{ route('handle-payment') }}",
                type: 'POST',
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                data: {
                    stripeToken: token.id,
                    cardholderName: document.getElementById('cardholder-name').value
                },
                success: function(response) {
                    if (response.status) {
                        window.location.reload();
                    }
                },
                error: function(error) {
                    console.error(error.message);
                }
            });

        }
    });

    $('#addNewCard').on('click', function() {
        $("#toggelCard").removeClass("hidden");
    });

    $('#make-payment').on('click', function() {
        var payment_method = $('#payment_method').val();
        console.log('payment_method', payment_method)
        $.ajax({
            url: "{{ route('make-payment') }}",
            type: 'POST',
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: {
                payment_method: payment_method
            },
            success: function(response) {
                if (response.status) {
                    window.location.reload();
                }
            },
            error: function(error) {
                console.error(error.message);
            }
        });
    });
</script>
