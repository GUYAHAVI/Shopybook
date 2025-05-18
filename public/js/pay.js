// $(document).ready(function () {
//     // Toggle visibility of payment method details based on selected option
//     $('input[name="pay"]').on('change', function () {
//         if ($(this).val() === 'mpesa') {
//             $('#mpesaDetails').show();
//             $('#creditCardDetails').hide();
//         } else if ($(this).val() === 'credit_card') {
//             $('#mpesaDetails').hide();
//             $('#creditCardDetails').show();
//         } else {
//             $('#mpesaDetails').hide();
//             $('#creditCardDetails').hide();
//         }
//     });

//     // Handle form submission
//     $('#paymentForm').on('submit', function (e) {
//         e.preventDefault();
//         let paymentMethod = $('input[name="pay"]:checked').val();
//         let amount = $('#totalPrice').text();

//         if (paymentMethod === 'mpesa') {
//             $('#statusMessage').text('Processing Mpesa payment...')
//                 .removeClass('alert alert-success alert-danger')
//                 .addClass('alert alert-info');

//             $.ajax({
//                 url: '{{ route("mpesa.pay") }}',
//                 method: 'POST',
//                 data: {
//                     _token: '{{ csrf_token() }}',
//                     phone_number: $('#phone_number').val(),
//                     amount: amount
//                 },
//                 success: function (response) {
//                     if (response.ResponseCode === '0') {
//                         $('#statusMessage').text(response.CustomerMessage)
//                             .removeClass('alert-info')
//                             .addClass('alert-success');
//                     } else {
//                         $('#statusMessage').text('Payment failed. Please try again.')
//                             .removeClass('alert-info')
//                             .addClass('alert-danger');
//                     }
//                 },
//                 error: function (xhr) {
//                     $('#statusMessage').text('An error occurred. Please try again.')
//                         .removeClass('alert-info')
//                         .addClass('alert-danger');
//                 }
//             });

//         } else if (paymentMethod === 'paypal') {
//             $('#statusMessage').text('Processing PayPal payment...')
//                 .removeClass('alert alert-success alert-danger')
//                 .addClass('alert alert-info');

//             $.ajax({
//                 url: '{{ route("paypal.pay") }}',
//                 method: 'POST',
//                 data: {
//                     _token: '{{ csrf_token() }}',
//                     amount: amount
//                 },
//                 headers: {
//                     'X-CSRF-TOKEN': '{{ csrf_token() }}' // Add CSRF token to headers
//                 },
//                 success: function (response) {
//                     // Redirect to PayPal for payment
//                     if (response.redirect_url) {
//                         window.location.href = response.redirect_url;
//                     } else {
//                         $('#statusMessage').text('An error occurred. Please try again.')
//                             .removeClass('alert-info')
//                             .addClass('alert-danger');
//                     }
//                 },
//                 error: function (xhr) {
//                     $('#statusMessage').text('An error occurred. Please try again.')
//                         .removeClass('alert-info')
//                         .addClass('alert-danger');
//                 }
//             });
//         } else {
//             $('#statusMessage').text('Please select a payment method.')
//                 .removeClass('alert alert-success alert-danger')
//                 .addClass('alert alert-warning');
//         }
//     });
// });
